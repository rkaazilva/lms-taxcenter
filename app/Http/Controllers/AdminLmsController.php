<?php

namespace App\Http\Controllers;

use App\Services\GoogleSheetService;
use Illuminate\Http\Request;
use App\Models\Notification;
use Carbon\Carbon;

class AdminLmsController extends Controller
{
    protected GoogleSheetService $gs;

    public function __construct()
    {
        $this->gs = new GoogleSheetService();
    }

    /**
     * Dashboard Admin LMS - Overview semua data
     */
    public function index()
    {
        // Cek session
        $role = session('role');
        if (!session()->has('email') || !in_array($role, ['ADMIN_LMS', 'ADMIN'])) {
            return redirect()->route('login')->with('error', 'Akses khusus Admin LMS.');
        }

        $materi = $this->gs->getMateri();
        $tugas = $this->gs->getTugas();
        $jadwal = $this->gs->getJadwal();
        $siswaList = $this->gs->getAllSiswa();
        $absensi = $this->gs->getAllAbsensi();
        if (is_array($absensi) && isset($absensi['status']) && $absensi['status'] === 'error') {
            $absensi = [];
        }

        $submissions = $this->gs->getAllSubmissions();
        if (is_array($submissions) && isset($submissions['status']) && $submissions['status'] === 'error') {
            $submissions = [];
        }

        // Hitung statistik
        $totalMateri = is_array($materi) ? count($materi) : 0;
        $totalTugas = is_array($tugas) ? count($tugas) : 0;
        $totalSesi = is_array($jadwal) ? count($jadwal) : 0;

        // Compute session targets per mapel based on uploaded Materi count (each Materi is 1 study session)
        $mapelSessionCounts = [
            "Ketentuan Umum dan Tata Cara Perpajakan (KUP) A & B" => 6,
            "Pajak Penghasilan (PPh) Orang Pribadi" => 6,
            "Pajak Pemotongan dan Pemungutan (PPh Pasal 21)" => 4,
            "Pajak Pemotongan dan Pemungutan (PPh Pasal 22, 23, 26, & 4(2))" => 4,
            "Pajak Penghasilan (PPh) Badan" => 6,
            "Pajak Pertambahan Nilai (PPN) dan PPnBM A & B" => 6,
            "Pajak Bumi dan Bangunan (PBB), BPHTB, & Bea Meterai" => 4,
            "Akuntansi Perpajakan" => 6,
            "Pemeriksaan dan Penyidikan Pajak" => 4,
            "Pengisian e-SPT / Aplikasi Perpajakan (e-Faktur, dll)" => 4,
            "Tax Planning (Perencanaan Pajak)" => 4,
            "Ujian Kelulusan / Komprehensif Brevet" => 1
        ];

        $materiCounts = [];
        foreach (is_array($materi) ? $materi : [] as $m) {
            if (is_array($m)) {
                $mapelName = GoogleSheetService::normalizeMapelName($m['mapel'] ?? '');
                if ($mapelName) {
                    if (!isset($materiCounts[$mapelName])) {
                        $materiCounts[$mapelName] = 0;
                    }
                    $materiCounts[$mapelName]++;
                }
            }
        }
        foreach ($materiCounts as $name => $count) {
            if ($count > 0) {
                $mapelSessionCounts[$name] = $count;
            }
        }

        // Petakan riwayat kehadiran siswa (deduplikasi per hari)
        $siswaAbsensiMap = [];
        foreach (is_array($absensi) ? $absensi : [] as $a) {
            $email = strtolower(trim($a['email'] ?? ''));
            $mapelRaw = trim($a['mapel'] ?? '');
            $mapel = GoogleSheetService::normalizeMapelName($mapelRaw);
            $timestamp = trim($a['timestamp'] ?? '');
            
            if ($email && $mapel && $timestamp) {
                $date = substr($timestamp, 0, 10);
                if (!isset($siswaAbsensiMap[$email])) {
                    $siswaAbsensiMap[$email] = [];
                }
                if (!isset($siswaAbsensiMap[$email][$mapel])) {
                    $siswaAbsensiMap[$email][$mapel] = [];
                }
                if (!in_array($date, $siswaAbsensiMap[$email][$mapel])) {
                    $siswaAbsensiMap[$email][$mapel][] = $date;
                }
            }
        }
        foreach ($siswaAbsensiMap as $email => $mapels) {
            foreach ($mapels as $mapel => $dates) {
                $siswaAbsensiMap[$email][$mapel] = count($dates);
            }
        }

        return view('admin-lms.dashboard', compact(
            'totalMateri', 'totalTugas', 'totalSesi', 'jadwal', 'materi', 'tugas',
            'siswaList', 'absensi', 'siswaAbsensiMap', 'mapelSessionCounts', 'submissions'
        ));
    }

    /**
     * Halaman Manajemen Jadwal & Link Zoom
     */
    public function jadwalIndex()
    {
        $role = session('role');
        if (!session()->has('email') || !in_array($role, ['ADMIN_LMS', 'ADMIN'])) {
            return redirect()->route('login')->with('error', 'Akses khusus Admin LMS.');
        }

        $rawJadwal = $this->gs->getJadwal();
        $jadwalList = array_map(function ($item) {
            return array_merge(is_array($item) ? $item : [], ['parsed_datetime' => $this->parseScheduleDateTime($item)]);
        }, is_array($rawJadwal) ? $rawJadwal : []);

        // Sort by tanggal descending (newest first)
        usort($jadwalList, function ($a, $b) {
            $ad = $a['parsed_datetime'];
            $bd = $b['parsed_datetime'];
            if ($ad && $bd) {
                return $bd->timestamp <=> $ad->timestamp;
            }
            if ($ad) return -1;
            if ($bd) return 1;
            return 0;
        });

        $jadwal = $jadwalList;

        $matakuliah = $this->gs->getMatakuliah();
        
        return view('admin-lms.jadwal.index', compact('jadwal', 'matakuliah'));
    }

    /**
     * Tambah/Edit Jadwal
     */
    public function jadwalStore(Request $request)
    {
        $request->validate([
            'tanggal'   => 'required|string',
            'jam'       => 'nullable|string',
            'mapel'     => 'required|string',
            'materi'    => 'required|string',
            'dosen'     => 'required|string',
            'link'      => 'required|url',
            'blast'     => 'nullable|string',
        ]);

        // Simpan ke MySQL Native via GoogleSheetService
        $result = $this->gs->addJadwal([
            'tanggal'   => $request->tanggal,
            'jam'       => $request->jam ?? '',
            'mapel'     => $request->mapel,
            'materi'    => $request->materi,
            'dosen'     => $request->dosen,
            'link'      => $request->link,
            'link_zoom' => $request->link,
            'blast'     => $request->has('blast') ? true : false,
        ]);

        if ($result['status'] === 'success') {
            // Create system notification for students
            Notification::create([
                'email' => 'SISWA',
                'title' => 'Jadwal Baru: ' . $request->materi,
                'message' => 'Jadwal baru dirilis untuk mata pelajaran ' . $request->mapel . ' oleh Dosen ' . $request->dosen,
                'link' => '/siswa/dashboard',
                'is_read' => false
            ]);
            
            return back()->with('success', 'Jadwal berhasil ditambahkan!');
        }

        return back()->with('error', 'Gagal menambahkan jadwal: ' . ($result['message'] ?? 'Unknown error'));
    }

    /**
     * Update Jadwal
     */
    public function jadwalUpdate(Request $request)
    {
        $request->validate([
            'original_tanggal' => 'required|string',
            'original_jam'     => 'required|string',
            'original_dosen'   => 'required|string',
            'tanggal'          => 'required|string',
            'jam'              => 'nullable|string',
            'materi'           => 'required|string',
            'dosen'            => 'required|string',
            'link'             => 'required|url',
        ]);

        \Illuminate\Support\Facades\Log::info('[AdminLmsController] jadwalUpdate Request', [
            'original_tanggal' => $request->original_tanggal,
            'original_jam'     => $request->original_jam,
            'original_dosen'   => $request->original_dosen,
            'tanggal'          => $request->tanggal,
            'jam'              => $request->jam,
            'materi'           => $request->materi,
            'dosen'            => $request->dosen,
        ]);

        $result = $this->gs->updateJadwal([
            'original_tanggal' => $request->original_tanggal,
            'original_jam'     => $request->original_jam,
            'original_dosen'   => $request->original_dosen,
            'tanggal'          => $request->tanggal,
            'jam'              => $request->jam ?? '',
            'materi'           => $request->materi,
            'dosen'            => $request->dosen,
            'moderator'        => '',
            'link'             => $request->link,
            'blast'            => $request->has('blast') ? true : false,
        ]);

        \Illuminate\Support\Facades\Log::info('[AdminLmsController] jadwalUpdate Result', $result);

        if ($result['status'] === 'success') {
            // Create system notification for students
            Notification::create([
                'email' => 'SISWA',
                'title' => 'Jadwal Diperbarui: ' . $request->materi,
                'message' => 'Jadwal mata pelajaran ' . $request->materi . ' telah diperbarui.',
                'link' => '/siswa/dashboard',
                'is_read' => false
            ]);
            return back()->with('success', 'Jadwal berhasil diperbarui!');
        }

        return back()->with('error', 'Gagal memperbarui jadwal: ' . ($result['message'] ?? 'Unknown error'));
    }

    /**
     * Hapus Jadwal
     */
    public function jadwalDelete(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|string',
            'jam'     => 'required|string',
            'dosen'   => 'required|string',
        ]);

        \Illuminate\Support\Facades\Log::info('[AdminLmsController] jadwalDelete Request', [
            'tanggal' => $request->tanggal,
            'jam'     => $request->jam,
            'dosen'   => $request->dosen,
        ]);

        $result = $this->gs->deleteJadwal([
            'tanggal' => $request->tanggal,
            'jam'     => $request->jam,
            'dosen'   => $request->dosen,
        ]);

        \Illuminate\Support\Facades\Log::info('[AdminLmsController] jadwalDelete Result', $result);

        if ($result['status'] === 'success') {
            return back()->with('success', 'Jadwal berhasil dihapus!');
        }

        return back()->with('error', 'Gagal menghapus jadwal: ' . ($result['message'] ?? 'Unknown error'));
    }

    /**
     * Halaman Manajemen Materi
     */
    public function materiIndex()
    {
        $role = session('role');
        if (!session()->has('email') || !in_array($role, ['ADMIN_LMS', 'ADMIN'])) {
            return redirect()->route('login')->with('error', 'Akses khusus Admin LMS.');
        }

        $materi = $this->gs->getMateri();
        return view('admin-lms.materi.index', compact('materi'));
    }

    /**
     * Update YouTube Link Materi (Admin Only)
     */
    public function materiUpdateYoutube(Request $request)
    {
        $request->validate([
            'mapel'        => 'required|string',
            'judul'        => 'required|string',
            'link_youtube' => 'required|url',
        ]);

        $result = $this->gs->updateMateriYoutube([
            'mapel'        => $request->mapel,
            'judul'        => $request->judul,
            'link_youtube' => $request->link_youtube,
        ]);

        if ($result['status'] === 'success') {
            return back()->with('success', 'Link YouTube berhasil diperbarui!');
        }

        return back()->with('error', 'Gagal memperbarui link: ' . ($result['message'] ?? 'Unknown error'));
    }

    /**
     * Manajemen Tugas
     */
    public function tugasIndex()
    {
        $role = session('role');
        if (!session()->has('email') || !in_array($role, ['ADMIN_LMS', 'ADMIN'])) {
            return redirect()->route('login')->with('error', 'Akses khusus Admin LMS.');
        }

        $tugas = $this->gs->getTugas();
        return view('admin-lms.tugas.index', compact('tugas'));
    }

    /**
     * Halaman Kelola Absensi / Kehadiran Siswa (Admin)
     */
    public function absensiIndex()
    {
        $role = session('role');
        if (!session()->has('email') || !in_array($role, ['ADMIN_LMS', 'ADMIN'])) {
            return redirect()->route('login')->with('error', 'Akses khusus Admin LMS.');
        }

        $siswaList = $this->gs->getAllSiswa();
        $absensi = $this->gs->getAllAbsensi();
        $allMateri = $this->gs->getMateri();
        if (is_array($absensi) && isset($absensi['status']) && $absensi['status'] === 'error') {
            $absensi = [];
        }

        // 1. Hitung total sesi berdasarkan jumlah Materi yang ter-upload (setiap Materi bernilai 1 sesi)
        $mapelSessionCounts = [
            "Ketentuan Umum dan Tata Cara Perpajakan (KUP) A & B" => 6,
            "Pajak Penghasilan (PPh) Orang Pribadi" => 6,
            "Pajak Pemotongan dan Pemungutan (PPh Pasal 21)" => 4,
            "Pajak Pemotongan dan Pemungutan (PPh Pasal 22, 23, 26, & 4(2))" => 4,
            "Pajak Penghasilan (PPh) Badan" => 6,
            "Pajak Pertambahan Nilai (PPN) dan PPnBM A & B" => 6,
            "Pajak Bumi dan Bangunan (PBB), BPHTB, & Bea Meterai" => 4,
            "Akuntansi Perpajakan" => 6,
            "Pemeriksaan dan Penyidikan Pajak" => 4,
            "Pengisian e-SPT / Aplikasi Perpajakan (e-Faktur, dll)" => 4,
            "Tax Planning (Perencanaan Pajak)" => 4,
            "Ujian Kelulusan / Komprehensif Brevet" => 1
        ];

        $materiCounts = [];
        foreach (is_array($allMateri) ? $allMateri : [] as $m) {
            if (is_array($m)) {
                $mapelName = GoogleSheetService::normalizeMapelName($m['mapel'] ?? '');
                if ($mapelName) {
                    if (!isset($materiCounts[$mapelName])) {
                        $materiCounts[$mapelName] = 0;
                    }
                    $materiCounts[$mapelName]++;
                }
            }
        }
        foreach ($materiCounts as $name => $count) {
            if ($count > 0) {
                $mapelSessionCounts[$name] = $count;
            }
        }

        // 2. Petakan riwayat kehadiran siswa (deduplikasi per hari)
        $siswaAbsensiMap = [];
        foreach ($absensi as $a) {
            $email = strtolower(trim($a['email'] ?? ''));
            $mapelRaw = trim($a['mapel'] ?? '');
            $mapel = GoogleSheetService::normalizeMapelName($mapelRaw);
            $timestamp = trim($a['timestamp'] ?? '');
            
            if ($email && $mapel && $timestamp) {
                $date = substr($timestamp, 0, 10); // YYYY-MM-DD
                if (!isset($siswaAbsensiMap[$email])) {
                    $siswaAbsensiMap[$email] = [];
                }
                if (!isset($siswaAbsensiMap[$email][$mapel])) {
                    $siswaAbsensiMap[$email][$mapel] = [];
                }
                if (!in_array($date, $siswaAbsensiMap[$email][$mapel])) {
                    $siswaAbsensiMap[$email][$mapel][] = $date;
                }
            }
        }

        // Hitung total hadir per mapel per siswa
        foreach ($siswaAbsensiMap as $email => $mapels) {
            foreach ($mapels as $mapel => $dates) {
                $siswaAbsensiMap[$email][$mapel] = count($dates);
            }
        }

        $daftarMapel = [
            "Ketentuan Umum dan Tata Cara Perpajakan (KUP) A & B",
            "Pajak Penghasilan (PPh) Orang Pribadi",
            "Pajak Pemotongan dan Pemungutan (PPh Pasal 21)",
            "Pajak Pemotongan dan Pemungutan (PPh Pasal 22, 23, 26, & 4(2))",
            "Pajak Penghasilan (PPh) Badan",
            "Pajak Pertambahan Nilai (PPN) dan PPnBM A & B",
            "Pajak Bumi dan Bangunan (PBB), BPHTB, & Bea Meterai",
            "Akuntansi Perpajakan",
            "Pemeriksaan dan Penyidikan Pajak",
            "Pengisian e-SPT / Aplikasi Perpajakan (e-Faktur, dll)",
            "Tax Planning (Perencanaan Pajak)",
            "Ujian Kelulusan / Komprehensif Brevet"
        ];

        return view('admin-lms.absensi.index', compact(
            'siswaList',
            'absensi',
            'mapelSessionCounts',
            'siswaAbsensiMap',
            'daftarMapel'
        ));
    }

    /**
     * Sinkronisasi Cache
     */
    public function syncCache()
    {
        $role = session('role');
        if (!session()->has('email') || !in_array($role, ['ADMIN_LMS', 'ADMIN'])) {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak'], 403);
        }
 
        try {
            $gs = new \App\Services\GoogleSheetService();
            $gs->clearAllCache();
            return response()->json(['status' => 'success', 'message' => 'Sinkronisasi data Google Sheets berhasil diselesaikan.']);
        } catch (\Exception $e) {
            \Log::error("[AdminLmsController] syncCache error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal sinkronisasi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Halaman Broadcast Notifikasi
     */
    public function notificationIndex()
    {
        $role = session('role');
        if (!session()->has('email') || !in_array($role, ['ADMIN_LMS', 'ADMIN'])) {
            return redirect()->route('login')->with('error', 'Akses khusus Admin LMS.');
        }

        $notifications = Notification::latest()->paginate(15);
        return view('admin-lms.notifikasi.index', compact('notifications'));
    }

    /**
     * Kirim Notifikasi Baru & Simpan ke DB
     */
    public function notificationStore(Request $request)
    {
        $role = session('role');
        if (!session()->has('email') || !in_array($role, ['ADMIN_LMS', 'ADMIN'])) {
            return redirect()->route('login')->with('error', 'Akses khusus Admin LMS.');
        }

        $request->validate([
            'target'  => 'required|in:ALL,SISWA,GURU',
            'title'   => 'required|string|max:255',
            'message' => 'required|string',
            'link'    => 'nullable|string',
        ]);

        // Simpan in-app notification ke SQLite
        Notification::create([
            'email'   => $request->target,
            'title'   => $request->title,
            'message' => $request->message,
            'link'    => $request->link,
            'is_read' => false,
        ]);

        $waSent = false;
        $waError = false;

        if ($request->has('send_wa')) {
            $waMessage = "📢 *PENGUMUMAN LMS TAX CENTER* 📢\n\n";
            $waMessage .= "*Judul:* " . $request->title . "\n";
            $waMessage .= "*Target:* " . ($request->target === 'ALL' ? 'Semua Pengguna' : ($request->target === 'SISWA' ? 'Siswa Sahaja' : 'Guru/Tutor Sahaja')) . "\n\n";
            $waMessage .= $request->message . "\n";
            
            if ($request->link) {
                $link = $request->link;
                if (strpos($link, 'http') !== 0) {
                    if (strpos($link, '/') !== 0) {
                        $link = '/' . $link;
                    }
                    $link = $request->getSchemeAndHttpHost() . $link;
                }
                $waMessage .= "\n*Tautan:* " . $link;
            }

            $waSent = $this->gs->sendWaBroadcast($waMessage);
            if (!$waSent) {
                $waError = true;
            }
        }

        if ($waError) {
            return redirect()->route('admin-lms.notifikasi.index')->with('warning', 'Notifikasi in-app berhasil dikirim, tetapi gagal dikirim via WhatsApp Group. Periksa log atau kredensial Fonnte Anda.');
        }

        if ($request->has('send_wa') && $waSent) {
            return redirect()->route('admin-lms.notifikasi.index')->with('success', 'Notifikasi in-app dan WhatsApp Group berhasil dikirim!');
        }

        return redirect()->route('admin-lms.notifikasi.index')->with('success', 'Notifikasi in-app berhasil dikirim!');
    }

    /**
     * Parse schedule date-time safely
     */
    private function parseScheduleDateTime(array $item): ?Carbon
    {
        $date = trim((string) ($item['tanggal'] ?? $item['date'] ?? ''));
        $time = trim((string) ($item['jam'] ?? $item['time'] ?? ''));

        if ($date === '') {
            return null;
        }

        $dateTime = trim($date . ' ' . $time);
        $formats = [
            '!Y-m-d H:i',
            '!Y-m-d H:i:s',
            '!Y/m/d H:i',
            '!Y/m/d H:i:s',
            '!d/m/Y H:i',
            '!d/m/Y H:i:s',
            '!d-m-Y H:i',
            '!d-m-Y H:i:s',
            '!Y-m-d',
            '!Y/m/d',
            '!d/m/Y',
            '!d-m-Y',
        ];

        foreach ($formats as $format) {
            try {
                $parsed = Carbon::createFromFormat($format, $dateTime);
                if ($parsed !== false) {
                    return $parsed;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        try {
            return Carbon::parse($dateTime);
        } catch (\Exception $e) {
            return null;
        }
    }
}
