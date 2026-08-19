<?php

namespace App\Http\Controllers;

use App\Services\GoogleSheetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Notification;
use App\Models\Announcement;
use App\Models\AnnouncementComment;

class LmsController extends Controller
{
    protected GoogleSheetService $gs;

    public function __construct()
    {
        $this->gs = new GoogleSheetService();
    }

    // ============================================================
    // DASHBOARD SISWA - Data utama
    // ============================================================

    /**
     * Ambil semua data untuk dashboard siswa sekaligus (Materi + Tugas + Nilai + Jadwal)
     * Menggunakan caching sehingga sangat cepat meski 30+ siswa akses bersamaan
     */
    public function getDashboardData(Request $request)
    {
        $email = session('email');
        if (empty($email)) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        // Release PHP session lock immediately so concurrent AJAX requests don't queue
        if (function_exists('session_write_close')) {
            session_write_close();
        }

        // Ambil semua data dashboard secara lebih efisien.
        $dashboardData = $this->gs->getDashboardData($email);
        $rawJadwal  = $dashboardData['jadwal'];
        
        // Defensive check: if rawJadwal is an associative array (e.g. error response), treat as empty
        if (is_array($rawJadwal) && isset($rawJadwal['status']) && $rawJadwal['status'] === 'error') {
            $rawJadwal = [];
        }

        $now = \Carbon\Carbon::now();
        $jadwalList = array_map(function ($item) {
            return array_merge(is_array($item) ? $item : [], ['parsed_datetime' => $this->parseScheduleDateTime($item)]);
        }, is_array($rawJadwal) ? $rawJadwal : []);

        usort($jadwalList, function ($a, $b) use ($now) {
            $ad = $a['parsed_datetime'];
            $bd = $b['parsed_datetime'];

            if ($ad && $bd) {
                $aFuture = $ad->greaterThanOrEqualTo($now);
                $bFuture = $bd->greaterThanOrEqualTo($now);

                if ($aFuture && !$bFuture) {
                    return -1;
                }
                if (!$aFuture && $bFuture) {
                    return 1;
                }

                if ($aFuture && $bFuture) {
                    return $ad->timestamp <=> $bd->timestamp;
                } else {
                    return $bd->timestamp <=> $ad->timestamp;
                }
            }

            if ($ad) return -1;
            if ($bd) return 1;
            return 0;
        });
        
        $jadwal = $jadwalList;
        
        $siswaKelas = session('kelas') ? trim(session('kelas')) : '';
        
        $rawMateri = $dashboardData['materi'];
        if (is_array($rawMateri) && isset($rawMateri['status']) && $rawMateri['status'] === 'error') {
            $rawMateri = [];
        }

        $materi  = array_values(array_filter(is_array($rawMateri) ? $rawMateri : [], function($m) use ($siswaKelas) {
            if (!is_array($m)) return false;
            if (($m['status'] ?? 'Rilis') !== 'Rilis') {
                return false;
            }
            
            $targetKelas = isset($m['kelas']) ? trim($m['kelas']) : '';
            
            // Jika target kelas kosong, atau berisi "Semua", "Semua Kelas", atau sama dengan kelas siswa
            return $targetKelas === '' || 
                   strcasecmp($targetKelas, 'Semua') === 0 || 
                   strcasecmp($targetKelas, 'Semua Kelas') === 0 || 
                   strcasecmp($targetKelas, $siswaKelas) === 0;
        }));
        
        $tugas   = $dashboardData['tugas'];
        if (is_array($tugas) && isset($tugas['status']) && $tugas['status'] === 'error') {
            $tugas = [];
        }
        $nilai   = $dashboardData['nilai'];
        if (is_array($nilai) && isset($nilai['status']) && $nilai['status'] === 'error') {
            $nilai = [];
        }

        // Hitung progress siswa: berapa pelajaran yang sudah ada tugasnya
        $mapelSudahKumpul = array_unique(
            array_column(
                array_filter(is_array($nilai) ? $nilai : [], fn($n) => is_array($n) && ($n['nilai'] !== '-' || $n['link_tugas'] !== '')),
                'id_tugas'
            )
        );

        // Ambil ID tugas unik per mapel dari daftar tugas
        $tugasPerMapel = [];
        foreach ($tugas as $t) {
            $tugasPerMapel[$t['mapel']][] = $t['id_tugas'];
        }

        // Hitung berapa mapel yang SUDAH ADA pengumpulan tugas
        $mapelSelesai = 0;
        foreach ($tugasPerMapel as $mapel => $idTugasList) {
            $adaKumpul = false;
            foreach ($idTugasList as $idTugas) {
                foreach ($nilai as $n) {
                    if ($n['id_tugas'] === $idTugas) {
                        $adaKumpul = true;
                        break 2;
                    }
                }
            }
            if ($adaKumpul) $mapelSelesai++;
        }

        $totalMapel = max(1, count($tugasPerMapel) ?: 12); // Dinamis dari data, fallback ke 12
        $progressPersen = $totalMapel > 0 ? round(($mapelSelesai / $totalMapel) * 100) : 0;

        // Fetch attendance history for the student
        $absensi = $this->gs->getAbsensiSiswa($email);
        if (is_array($absensi) && isset($absensi['status']) && $absensi['status'] === 'error') {
            $absensi = [];
        }

        return response()->json([
            'jadwal'          => $jadwal,
            'materi'          => $materi,
            'tugas'           => $tugas,
            'nilai'           => $nilai,
            'absensi'         => $absensi,
            'progress_persen' => $progressPersen,
            'mapel_selesai'   => $mapelSelesai,
            'total_mapel'     => $totalMapel,
        ]);
    }

    /**
     * Tampilkan view halaman dashboard siswa
     */
    public function studentDashboard()
    {
        $email = session('email');
        if ($email) {
            $user = \App\Models\LmsUser::findByEmail($email);
            if ($user) {
                session([
                    'sertifikat' => $user->sertifikat ?? '',
                    'kelas'      => $user->kelas ?? '',
                    'nama'       => $user->nama ?? '',
                ]);
            }
        }

        $siswaKelas = session('kelas') ? trim(session('kelas')) : '';
        $announcements = Announcement::where(function($q) use ($siswaKelas) {
                $q->where('target_kelas', 'ALL')
                  ->orWhere('target_kelas', $siswaKelas);
            })
            ->with(['comments' => function($q) {
                $q->orderBy('created_at', 'asc');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('siswa.dashboard', compact('announcements'));
    }

    // ============================================================
    // ABSENSI
    // ============================================================

    /**
     * Catat absensi siswa (dipanggil saat klik Join Zoom atau Konfirmasi Rekaman)
     */
    public function catatAbsen(Request $request)
    {
        $request->validate([
            'mapel'  => 'required|string',
            'metode' => 'required|in:Live Zoom,Nonton Rekaman YouTube',
        ]);

        $email = session('email');
        $nama  = session('nama');

        // BUG-021: Guard null email/nama
        if (empty($email) || empty($nama)) {
            return response()->json(['status' => 'error', 'message' => 'Session tidak valid. Silakan login ulang.'], 401);
        }

        // Fast release session lock so other AJAX requests don't queue
        if (function_exists('session_write_close')) {
            session_write_close();
        }

        // BUG-011: Backend duplicate check (per email + mapel + tanggal)
        $todayKey = 'absen_' . md5($email . '_' . $request->mapel . '_' . now()->format('Y-m-d'));
        if (Cache::has($todayKey)) {
            return response()->json(['status' => 'error', 'message' => 'Kehadiran Anda untuk mata pelajaran ini hari ini sudah tercatat.'], 409);
        }

        $hasil = $this->gs->catatAbsen($email, $nama, $request->mapel, $request->metode);

        if ($hasil) {
            // Set cache guard agar tidak bisa double absen hari ini (TTL: hingga tengah malam)
            $secondsUntilMidnight = now()->diffInSeconds(now()->endOfDay());
            Cache::put($todayKey, true, $secondsUntilMidnight);
            return response()->json(['status' => 'success', 'message' => 'Kehadiran berhasil dicatat!']);
        }

        return response()->json(['status' => 'error', 'message' => 'Gagal mencatat kehadiran.'], 500);
    }

    /**
     * Tambah Absensi Manual (dipanggil oleh Admin / Guru)
     */
    public function storeAbsensiManual(Request $request)
    {
        // BUG-022: Middleware sudah handle ini. Manual check dihapus (redundant).
        $request->validate([
            'email'   => 'required|email',
            'mapel'   => 'required|string',
            'metode'  => 'required|string',
            'tanggal' => 'nullable|date_format:Y-m-d',
            'jam'     => 'nullable|string', // e.g. "09:00"
        ]);

        // Cari user di database lokal untuk mendapatkan nama
        $user = \App\Models\LmsUser::findByEmail($request->email);
        $nama = $user ? $user->nama : 'Peserta';

        // Tentukan timestamp lengkap
        $timestamp = null;
        if ($request->filled('tanggal')) {
            $jam = $request->filled('jam') ? $request->jam : '09:00';
            $timestamp = \Carbon\Carbon::parse($request->tanggal . ' ' . $jam)->toIso8601String();
        }

        $hasil = $this->gs->addAbsensiManual(
            $request->email,
            $nama,
            $request->mapel,
            $request->metode,
            $timestamp
        );

        if (isset($hasil['status']) && $hasil['status'] === 'success') {
            return response()->json(['status' => 'success', 'message' => 'Absensi berhasil ditambahkan!']);
        }

        return response()->json(['status' => 'error', 'message' => $hasil['message'] ?? 'Gagal menambahkan absensi.'], 500);
    }

    /**
     * Hapus Absensi (hanya untuk Admin)
     */
    public function deleteAbsensi(Request $request)
    {
        if (!in_array(session('role'), ['ADMIN', 'ADMIN_LMS'])) {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak. Hanya Admin yang dapat menghapus absensi.'], 403);
        }

        $request->validate([
            'email'     => 'required|email',
            'mapel'     => 'required|string',
            'timestamp' => 'required|string',
        ]);

        $hasil = $this->gs->deleteAbsensi(
            $request->email,
            $request->mapel,
            $request->timestamp
        );

        if (isset($hasil['status']) && $hasil['status'] === 'success') {
            return response()->json(['status' => 'success', 'message' => 'Data absensi berhasil dihapus!']);
        }

        return response()->json(['status' => 'error', 'message' => $hasil['message'] ?? 'Gagal menghapus absensi.'], 500);
    }

    // ============================================================
    // UPLOAD TUGAS SISWA
    // ============================================================

    /**
     * Kirim tugas siswa ke Google Drive via Apps Script
     */
    public function submitTugas(Request $request)
    {
        $email = session('email');
        if (empty($email)) {
            return response()->json(['status' => 'error', 'message' => 'Sesi login berakhir. Silakan login kembali.'], 401);
        }

        // Release PHP session lock immediately
        if (function_exists('session_write_close')) {
            session_write_close();
        }

        $request->validate([
            'id_tugas'   => 'required|string',
            'file_tugas' => 'nullable|file|max:30720', // max 30MB
            'base64'     => 'nullable|string',
            'fileName'   => 'nullable|string',
            'mimeType'   => 'nullable|string',
            'link_tugas' => 'nullable|string',
        ]);

        if (!$request->hasFile('file_tugas') && empty($request->base64) && empty($request->link_tugas)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Harap unggah berkas atau masukkan tautan (link) tugas Anda!'
            ], 422);
        }

        $linkTugas = trim($request->link_tugas ?? '');
        if (!empty($linkTugas) && !preg_match('~^(?:f|ht)tps?://~i', $linkTugas)) {
            $linkTugas = 'https://' . $linkTugas;
        }

        // Option A: Direct multipart file upload (Fastest & Ultra Low Memory)
        if ($request->hasFile('file_tugas')) {
            $file = $request->file('file_tugas');
            if (!$this->isSafeExtension($file->getClientOriginalName())) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Unggahan ditolak. Format file tidak diperbolehkan demi keamanan sistem!'
                ], 422);
            }
            try {
                $linkTugas = $this->storePublicFile($file, 'submissions');
            } catch (\Exception $e) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Gagal menyimpan berkas tugas di server: ' . $e->getMessage()
                ], 500);
            }
        }
        // Option B: Legacy Base64 fallback
        elseif (!empty($request->base64) && !empty($request->fileName)) {
            if (!$this->isSafeExtension($request->fileName)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Unggahan ditolak. Format file tidak diperbolehkan demi keamanan sistem!'
                ], 422);
            }
            try {
                $fileData = base64_decode($request->base64);
                $cleanFileName = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $request->fileName);
                $filePath = 'submissions/' . $cleanFileName;

                // Simpan ke storage/app/public/submissions/
                \Illuminate\Support\Facades\Storage::disk('public')->put($filePath, $fileData);

                // URL file lokal di hosting
                $linkTugas = asset('storage/' . $filePath);
            } catch (\Exception $e) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Gagal menyimpan berkas tugas di server: ' . $e->getMessage()
                ], 500);
            }
        }

        $payload = [
            'email'      => $email,
            'nama'       => session('nama'),
            'id_tugas'   => $request->id_tugas,
            'base64'     => '',
            'fileName'   => '',
            'mimeType'   => '',
            'link_tugas' => $linkTugas,
        ];

        $result = $this->gs->submitTugas($payload);
        return response()->json($result);
    }

    // ============================================================
    // TUTOR: Manajemen Materi
    // ============================================================

    /**
     * Tampilkan halaman manajemen materi tutor
     */
    public function materiIndex()
    {
        $materi = $this->gs->getMateri();
        return view('tutor.materi', compact('materi'));
    }

    /**
     * Simpan materi baru dari form Tutor
     */
    public function materiStore(Request $request)
    {
        $request->validate([
            'mapel'        => 'required|string',
            'judul'        => 'required|string|max:255',
            'link_modul'   => 'nullable|string',
            'link_youtube' => 'nullable|string',
            'keterangan'   => 'nullable|string',
            'file_modul'   => 'nullable|file|max:30720', // max 30MB
            'status'       => 'nullable|in:Rilis,Draft',
            'kelas'        => 'nullable|string|max:100',
        ]);

        $linkModul = trim($request->input('link_modul', ''));
        if (!empty($linkModul) && !preg_match('~^(?:f|ht)tps?://~i', $linkModul)) {
            $linkModul = 'https://' . $linkModul;
        }

        $linkYoutube = trim($request->input('link_youtube', ''));
        if (!empty($linkYoutube) && !preg_match('~^(?:f|ht)tps?://~i', $linkYoutube)) {
            $linkYoutube = 'https://' . $linkYoutube;
        }

        // If a file was uploaded, store it locally
        if ($request->hasFile('file_modul')) {
            $file = $request->file('file_modul');
            if (!$this->isSafeExtension($file->getClientOriginalName())) {
                return back()->with('error', 'Gagal: Format berkas modul tidak diperbolehkan demi keamanan sistem!');
            }
            $linkModul = $this->storePublicFile($file, 'materi');
        }

        $result = $this->gs->addMateri([
            'mapel'        => $request->mapel,
            'judul'        => $request->judul,
            'link_modul'   => $linkModul,
            'link_youtube' => $linkYoutube,
            'keterangan'   => $request->keterangan ?? '',
            'status'       => $request->status ?? 'Rilis',
            'kelas'        => $request->kelas ?? '',
        ]);

        if ($result['status'] === 'success') {
            return back()->with('success', 'Materi berhasil ditambahkan!');
        }

        return back()->with('error', 'Gagal menambahkan materi: ' . ($result['message'] ?? 'Unknown error'));
    }

    public function materiUpdate(Request $request)
    {
        $request->validate([
            'id'             => 'nullable|integer',
            'original_mapel' => 'nullable|string',
            'original_judul' => 'nullable|string|max:255',
            'mapel'          => 'required|string',
            'judul'          => 'required|string|max:255',
            'link_modul'     => 'nullable|string',
            'keterangan'     => 'nullable|string',
            'file_modul'     => 'nullable|file|max:30720',
            'status'         => 'nullable|in:Rilis,Draft',
            'kelas'          => 'nullable|string|max:100',
        ]);

        $linkModul = trim($request->input('link_modul', ''));
        if (!empty($linkModul) && !preg_match('~^(?:f|ht)tps?://~i', $linkModul)) {
            $linkModul = 'https://' . $linkModul;
        }

        if ($request->hasFile('file_modul')) {
            $file = $request->file('file_modul');
            if (!$this->isSafeExtension($file->getClientOriginalName())) {
                return back()->with('error', 'Gagal: Format berkas modul tidak diperbolehkan demi keamanan sistem!');
            }
            $linkModul = $this->storePublicFile($file, 'materi');
        }

        $result = $this->gs->updateMateri([
            'id'             => $request->id,
            'original_mapel' => $request->original_mapel ?? ($request->mapel ?? ''),
            'original_judul' => $request->original_judul ?? ($request->judul ?? ''),
            'mapel'          => $request->mapel,
            'judul'          => $request->judul,
            'link_modul'     => $linkModul,
            'keterangan'     => $request->keterangan ?? '',
            'status'         => $request->status ?? 'Rilis',
            'kelas'          => $request->kelas ?? '',
        ]);

        if ($result['status'] === 'success') {
            return back()->with('success', 'Materi berhasil diperbarui!');
        }

        return back()->with('error', 'Gagal memperbarui materi: ' . ($result['message'] ?? 'Unknown error'));
    }

    // ============================================================
    // TUTOR: Manajemen Tugas
    // ============================================================

    /**
     * Tampilkan halaman manajemen tugas tutor
     */
    public function tugasIndex()
    {
        $tugasList = $this->gs->getTugas();
        return view('tutor.tugas', compact('tugasList'));
    }

    /**
     * Simpan tugas baru dari form Tutor
     */
    public function tugasStore(Request $request)
    {
        $request->validate([
            'id_tugas'        => 'required|string|max:50',
            'mapel'           => 'required|string',
            'judul'           => 'required|string|max:255',
            'deskripsi'       => 'nullable|string',
            'link_soal'       => 'nullable|string',
            'deadline_date'   => 'nullable|date',
            'deadline_hour'   => 'nullable|string',
            'deadline_minute' => 'nullable|string',
            'file_soal'       => 'nullable|file|max:30720', // max 30MB
            'blast'           => 'nullable|string',
        ]);

        $linkSoal = trim($request->input('link_soal', ''));
        if (!empty($linkSoal) && !preg_match('~^(?:f|ht)tps?://~i', $linkSoal)) {
            $linkSoal = 'https://' . $linkSoal;
        }

        if ($request->hasFile('file_soal')) {
            $file = $request->file('file_soal');
            if (!$this->isSafeExtension($file->getClientOriginalName())) {
                return back()->with('error', 'Gagal: Format berkas soal tidak diperbolehkan demi keamanan sistem!');
            }
            $linkSoal = $this->storePublicFile($file, 'soal');
        }

        $deadline = '';
        if ($request->deadline_date) {
            $deadline = $request->deadline_date . ' ' . ($request->deadline_hour ?? '23') . ':' . ($request->deadline_minute ?? '59') . ' WIB';
        }

        $result = $this->gs->addTugas([
            'id_tugas'  => $request->id_tugas,
            'mapel'     => $request->mapel,
            'judul'     => $request->judul,
            'deskripsi' => $request->deskripsi ?? '',
            'link_soal' => $linkSoal,
            'deadline'  => $deadline,
            'blast'     => $request->has('blast') ? true : false,
        ]);

        if ($result['status'] === 'success') {
            // BUG-009: Filter notifikasi hanya untuk kelas yang sesuai
            $targetKelas = $request->kelas ?? 'Semua';
            Notification::create([
                'email'   => 'SISWA',
                'title'   => 'Tugas Baru: ' . $request->judul,
                'message' => 'Tugas baru telah dirilis untuk mata pelajaran ' . $request->mapel . (($targetKelas && strtolower($targetKelas) !== 'semua') ? ' (Kelas: ' . $targetKelas . ')' : ''),
                'link'    => '/siswa/dashboard?tab=tugas',
                'is_read' => false
            ]);
            return back()->with('success', $result['message'] ?? 'Tugas berhasil dibuat!');
        }

        return back()->with('error', 'Gagal membuat tugas: ' . ($result['message'] ?? 'Unknown error'));
    }

    public function tugasUpdate(Request $request)
    {
        $request->validate([
            'id'                => 'nullable|integer',
            'original_id_tugas' => 'nullable|string|max:50',
            'id_tugas'          => 'required|string|max:50',
            'mapel'             => 'required|string',
            'judul'             => 'required|string|max:255',
            'deskripsi'         => 'nullable|string',
            'link_soal'         => 'nullable|string',
            'deadline_date'     => 'nullable|date',
            'deadline_hour'     => 'nullable|string',
            'deadline_minute'   => 'nullable|string',
            'file_soal'         => 'nullable|file|max:30720',
            'blast'             => 'nullable|string',
        ]);

        $linkSoal = trim($request->input('link_soal', ''));
        if (!empty($linkSoal) && !preg_match('~^(?:f|ht)tps?://~i', $linkSoal)) {
            $linkSoal = 'https://' . $linkSoal;
        }

        if ($request->hasFile('file_soal')) {
            $file = $request->file('file_soal');
            if (!$this->isSafeExtension($file->getClientOriginalName())) {
                return back()->with('error', 'Gagal: Format berkas soal tidak diperbolehkan demi keamanan sistem!');
            }
            $linkSoal = $this->storePublicFile($file, 'soal');
        }

        $deadline = '';
        if ($request->deadline_date) {
            $deadline = $request->deadline_date . ' ' . ($request->deadline_hour ?? '23') . ':' . ($request->deadline_minute ?? '59') . ' WIB';
        }

        $result = $this->gs->updateTugas([
            'id'                => $request->id,
            'original_id_tugas' => $request->original_id_tugas ?? ($request->id_tugas ?? ''),
            'id_tugas'          => $request->id_tugas,
            'mapel'             => $request->mapel,
            'judul'             => $request->judul,
            'deskripsi'         => $request->deskripsi ?? '',
            'link_soal'         => $linkSoal,
            'deadline'          => $deadline,
            'blast'             => $request->has('blast') ? true : false,
        ]);

        if ($result['status'] === 'success') {
            // Create system notification for students
            Notification::create([
                'email' => 'SISWA',
                'title' => 'Tugas Diperbarui: ' . $request->judul,
                'message' => 'Tugas ' . $request->id_tugas . ' telah diperbarui untuk mata pelajaran ' . $request->mapel,
                'link' => '/siswa/dashboard?tab=tugas',
                'is_read' => false
            ]);
            return back()->with('success', $result['message'] ?? 'Tugas berhasil diperbarui!');
        }

        return back()->with('error', 'Gagal memperbarui tugas: ' . ($result['message'] ?? 'Unknown error'));
    }

    // ============================================================
    // ADMIN: Sinkronisasi Cache Google Sheets
    // ============================================================

    /**
     * Berikan penilaian untuk tugas siswa
     */
    public function gradeSubmission(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'id_tugas' => 'required|string',
            'nilai'    => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $result = $this->gs->gradeSubmission([
            'email'    => $request->email,
            'id_tugas' => $request->id_tugas,
            'nilai'    => $request->nilai,
            'feedback' => $request->feedback ?? '',
        ]);

        if (isset($result['status']) && $result['status'] === 'success') {
            // Create system notification for the specific student
            Notification::create([
                'email' => $request->email,
                'title' => 'Tugas Dinilai: ' . $request->id_tugas,
                'message' => 'Tugas ' . $request->id_tugas . ' Anda telah dinilai oleh Dosen. Nilai: ' . $request->nilai,
                'link' => '/siswa/dashboard?tab=nilai',
                'is_read' => false
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Nilai berhasil disimpan!'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => $result['message'] ?? 'Gagal menyimpan nilai.'
        ], 500);
    }

    /**
     * Berikan penilaian secara massal untuk tugas siswa
     */
    public function batchGradeSubmissions(Request $request)
    {
        $request->validate([
            'items'            => 'required|array|min:1',
            'items.*.email'    => 'required|email',
            'items.*.id_tugas' => 'required|string',
            'items.*.nilai'    => 'required|numeric|min:0|max:100',
            'items.*.feedback' => 'nullable|string',
        ]);

        $result = $this->gs->gradeSubmissionsBatch($request->items);

        if (isset($result['status']) && $result['status'] === 'success') {
            // Create system notifications for each graded student in batch
            foreach ($request->items as $item) {
                Notification::create([
                    'email' => $item['email'],
                    'title' => 'Tugas Dinilai: ' . $item['id_tugas'],
                    'message' => 'Tugas ' . $item['id_tugas'] . ' Anda telah dinilai oleh Dosen. Nilai: ' . $item['nilai'],
                    'link' => '/siswa/dashboard?tab=nilai',
                    'is_read' => false
                ]);
            }
            return response()->json([
                'status' => 'success',
                'message' => $result['message'] ?? 'Nilai massal berhasil disimpan!'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => $result['message'] ?? 'Gagal menyimpan nilai massal.'
        ], 500);
    }


    /**
     * Reset cache agar data dari Google Sheets terbaru langsung tampil
     * BUG-007: Diperbaiki agar sinkron (tidak bergantung queue worker yang mungkin tidak aktif)
     */
    public function syncCache()
    {
        $gs = new \App\Services\GoogleSheetService();
        $gs->clearAllCache();
        return response()->json(['status' => 'success', 'message' => 'Sinkronisasi data Google Sheets selesai.']);
    }
 
    /**
     * Webhook untuk menghapus cache LMS ketika Google Sheets di-update
     * BUG-008: Diperbaiki agar sinkron (tidak bergantung queue worker)
     */
    public function webhookClearCache(Request $request)
    {
        $token = $request->input('token');
        $expectedToken = env('API_LMS_TOKEN', 'TC_UIN_LMS_SECURE_2026');
 
        if ($token !== $expectedToken) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Token tidak valid!'
            ], 403);
        }
 
        $gs = new \App\Services\GoogleSheetService();
        $gs->clearAllCache();
 
        \Illuminate\Support\Facades\Log::info('[Webhook] Sync cache LMS berhasil dipicu otomatis oleh Google Sheets.');
 
        return response()->json([
            'status'  => 'success',
            'message' => 'Webhook diterima. Sinkronisasi cache selesai!'
        ]);
    }

    /**
     * Ambil daftar notifikasi untuk user login
     */
    public function getNotifications(Request $request)
    {
        $email = session('email');
        $role = session('role');

        if (!$email) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        // BUG-016: Perbaikan cross-query TUTOR <-> GURU — keduanya menerima notif 'GURU' karena sama-sama pengajar
        $notifications = Notification::where(function ($q) use ($email, $role) {
            $q->where('email', $email)
              ->orWhere('email', 'ALL');
            if (in_array($role, ['TUTOR', 'GURU'])) {
                $q->orWhere('email', 'GURU')->orWhere('email', 'TUTOR');
            } else {
                $q->orWhere('email', $role);
            }
        })
        ->latest()
        ->take(20)
        ->get();

        $unreadCount = Notification::where(function ($q) use ($email, $role) {
            $q->where('email', $email)
              ->orWhere('email', 'ALL');
            if (in_array($role, ['TUTOR', 'GURU'])) {
                $q->orWhere('email', 'GURU')->orWhere('email', 'TUTOR');
            } else {
                $q->orWhere('email', $role);
            }
        })
        ->where('is_read', false)
        ->count();

        return response()->json([
            'status' => 'success',
            'data' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Tandai semua notifikasi milik user login sebagai sudah dibaca
     */
    public function markNotificationsRead(Request $request)
    {
        $email = session('email');
        $role = session('role');

        if (!$email) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        Notification::where(function ($q) use ($email, $role) {
            $q->where('email', $email)
              ->orWhere('email', $role)
              ->orWhere('email', 'ALL');
            if ($role === 'TUTOR') {
                $q->orWhere('email', 'GURU');
            } elseif ($role === 'GURU') {
                $q->orWhere('email', 'TUTOR');
            }
        })
        ->where('is_read', false)
        ->update(['is_read' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Semua notifikasi ditandai dibaca.'
        ]);
    }

    // ============================================================
    // PENGUMUMAN & KOMENTAR KELAS (FEED)
    // ============================================================

    /**
     * Posting pengumuman baru (Dosen / Admin)
     */
    public function storeAnnouncement(Request $request)
    {
        $role = session('role');
        if (!in_array($role, ['TUTOR', 'ADMIN', 'ADMIN_LMS', 'GURU'])) {
            return redirect()->back()->with('error', 'Akses khusus Tutor & Admin.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_kelas' => 'required|string',
        ]);

        $announcement = Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'author_name' => session('nama', 'Dosen'),
            'author_email' => session('email'),
            'target_kelas' => $request->target_kelas,
        ]);

        // Picu Notifikasi Internal (Bell Icon) ke semua siswa target
        $targetKelas = $request->target_kelas;
        $title = "Pengumuman Baru: " . $request->title;
        $message = "Ada pengumuman baru yang dirilis oleh " . session('nama') . ". Silakan cek feed di beranda.";

        Notification::create([
            'email' => 'SISWA',
            'title' => $title,
            'message' => $message,
            'link' => route('siswa.dashboard') . '?tab=overview',
        ]);

        return redirect()->back()->with('success', 'Pengumuman kelas berhasil dipublikasikan!');
    }

    /**
     * Hapus pengumuman (Hanya pembuat atau Admin)
     */
    public function deleteAnnouncement(Announcement $announcement)
    {
        $role = session('role');
        $email = session('email');

        $isAuthor = ($announcement->author_email === $email);
        $isAdmin = in_array($role, ['ADMIN', 'ADMIN_LMS']);

        if (!$isAuthor && !$isAdmin) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus pengumuman ini.');
        }

        $announcement->delete();

        return redirect()->back()->with('success', 'Pengumuman kelas berhasil dihapus.');
    }

    /**
     * Tulis komentar baru (Siswa, Guru, Admin)
     */
    public function storeComment(Request $request, Announcement $announcement)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        AnnouncementComment::create([
            'announcement_id' => $announcement->id,
            'name' => session('nama', 'User'),
            'email' => session('email'),
            'content' => $request->content,
        ]);

        return redirect()->back()->with('success', 'Komentar berhasil ditambahkan!');
    }

    /**
     * Hapus komentar (Hanya pembuat komentar, pembuat pengumuman, atau Admin)
     */
    public function deleteComment(AnnouncementComment $comment)
    {
        $role = session('role');
        $email = session('email');

        $isCommenter = ($comment->email === $email);
        $isAnnouncementAuthor = ($comment->announcement->author_email === $email);
        $isAdmin = in_array($role, ['ADMIN', 'ADMIN_LMS']);

        if (!$isCommenter && !$isAnnouncementAuthor && !$isAdmin) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus komentar ini.');
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Komentar berhasil dihapus.');
    }

    /**
     * Perbarui Profil & Password Pengguna (Siswa / Guru)
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'nama'             => 'required|string|max:255',
            'old_password'     => 'nullable|required_with:new_password|string',
            'new_password'     => 'nullable|string|min:6|confirmed',
        ]);

        $email = session('email');
        $user = \App\Models\LmsUser::findByEmail($email);

        if (!$user) {
            return back()->with('error', 'User tidak ditemukan.');
        }

        // Jika mengubah password, validasi password lama
        $newPassword = null;
        if ($request->filled('new_password')) {
            if (!\Illuminate\Support\Facades\Hash::check($request->old_password, $user->password)) {
                return back()->with('error', 'Password lama yang Anda masukkan salah!');
            }
            $newPassword = $request->new_password;
        }

        // Kirim ke Google Sheets
        $result = $this->gs->updateProfile($email, $request->nama, $newPassword);

        if ($result['status'] === 'success') {
            // Update SQLite LmsUser lokal
            $user->nama = $request->nama;
            if ($newPassword) {
                $user->password = \Illuminate\Support\Facades\Hash::make($newPassword);
            }
            $user->save();

            // Jika role Guru/Tutor, update juga nama di model Guru lokal
            if (in_array(session('role'), ['TUTOR', 'GURU'])) {
                $guru = \App\Models\Guru::findByEmail($email);
                if ($guru) {
                    $guru->nama = $request->nama;
                    $guru->save();
                }
            }

            // Update session
            session(['nama' => $request->nama]);
            session()->save();

            return back()->with('success', 'Profil dan Password berhasil diperbarui!');
        }

        return back()->with('error', 'Gagal memperbarui profil: ' . ($result['message'] ?? 'Unknown error'));
    }

    /**
     * Memeriksa apakah nama berkas memiliki ekstensi yang aman (anti-RCE)
     */
    private function isSafeExtension(string $fileName): bool
    {
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $blacklist = ['php', 'phtml', 'php3', 'php4', 'php5', 'html', 'htm', 'js', 'jsp', 'asp', 'aspx', 'sh', 'exe', 'pl', 'cgi', 'htaccess'];
        return !in_array($ext, $blacklist);
    }

    /**
     * Parse schedule date-time safely
     */
    private function parseScheduleDateTime(array $item): ?\Carbon\Carbon
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
                $parsed = \Carbon\Carbon::createFromFormat($format, $dateTime);
                if ($parsed !== false) {
                    return $parsed;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        try {
            return \Carbon\Carbon::parse($dateTime);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Helper aman untuk menyimpan file ke storage & folder public secara langsung.
     * Mencegah error 404 pada cPanel shared hosting yang tidak mendukung symlink.
     */
    private function storePublicFile($file, string $folder): string
    {
        $cleanFileName = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName());
        
        // 1. Simpan via Laravel Storage Disk (storage/app/public/{folder})
        $path = $file->storeAs($folder, $cleanFileName, 'public');
        
        // 2. Salin fisik langsung ke public_path("storage/{$folder}")
        try {
            $publicDir = public_path("storage/{$folder}");
            if (!file_exists($publicDir)) {
                @mkdir($publicDir, 0755, true);
            }
            $targetPath = $publicDir . '/' . $cleanFileName;
            $sourcePath = storage_path("app/public/{$path}");
            if (file_exists($sourcePath)) {
                @copy($sourcePath, $targetPath);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("[storePublicFile] Copy to public_path failed: " . $e->getMessage());
        }

        // 3. Kembalikan URL absolut dinamis (otomatis mengikuti domain request saat ini)
        return url('storage/' . $path);
    }
}

