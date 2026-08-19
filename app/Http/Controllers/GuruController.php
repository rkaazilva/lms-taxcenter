<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleSheetService;
use App\Models\Guru;
use App\Models\Announcement;
use Carbon\Carbon;

class GuruController extends Controller
{
    protected GoogleSheetService $gs;

    public function __construct()
    {
        $this->gs = new GoogleSheetService();
    }

    public function index(Request $request)
    {
        // Perpanjang batas waktu eksekusi PHP agar tidak crash (Fatal error 60s) 
        // saat menunggu respon lambat dari Google Apps Script
        set_time_limit(120);

        // Cek session
        $role = session('role');
        if (!session()->has('email') || !in_array($role, ['TUTOR', 'ADMIN', 'ADMIN_LMS', 'GURU'])) {
            return redirect()->route('login')->with('error', 'Akses khusus Tutor & Admin.');
        }

        // Fetch guru data from database (if exists) for filtering by mapel
        $guruEmail = session('email');
        $guru = Guru::findByEmail($guruEmail);
        $guruMapel = $guru ? $guru->mapel : []; // Array of mapel this guru teaches
 
        // Update session name dynamically if empty or default 'User' to avoid logout requirement
        if ($guru && (!session()->has('nama') || session('nama') === 'User' || session('nama') === '')) {
            session(['nama' => $guru->nama]);
        }

        // Get all data from Google Sheets in parallel / cached
        $guruData = $this->gs->getGuruDashboardData();
        $allMateri = $guruData['materi'];
        $allTugas = $guruData['tugas'];
        $jadwal = $guruData['jadwal'];
        $matakuliah = $guruData['matakuliah'];
        $allSubmissions = $guruData['submissions'];
 
        // Filter materi by guru's mapel
        $materi = $this->filterByMapel($allMateri, $guruMapel, $role);
 
        // Filter tugas by guru's mapel
        $tugas = $this->filterByMapel($allTugas, $guruMapel, $role);

        // Filter submissions by guru's mapel & task IDs
        if (in_array(strtoupper($role), ['ADMIN', 'ADMIN_LMS']) || empty($guruMapel)) {
            $submissions = is_array($allSubmissions) ? $allSubmissions : [];
        } else {
            $guruTugasIds = array_filter(array_column(is_array($tugas) ? $tugas : [], 'id_tugas'));
            $normalizedGuruMapel = array_map(function($m) {
                return \App\Services\GoogleSheetService::normalizeMapelName($m);
            }, (array)$guruMapel);

            $submissions = array_values(array_filter(is_array($allSubmissions) ? $allSubmissions : [], function ($sub) use ($guruMapel, $normalizedGuruMapel, $guruTugasIds) {
                $idT = $sub['id_tugas'] ?? '';
                if (!empty($idT) && in_array($idT, $guruTugasIds)) {
                    return true;
                }
                $subMapel = $sub['mapel'] ?? '';
                if (!empty($subMapel) && in_array($subMapel, $guruMapel)) {
                    return true;
                }
                $normSubMapel = \App\Services\GoogleSheetService::normalizeMapelName($subMapel);
                if (!empty($normSubMapel) && in_array($normSubMapel, $normalizedGuruMapel)) {
                    return true;
                }
                if (empty($subMapel)) {
                    return true;
                }
                return false;
            }));
        }

        $namaGuru = session('nama');
        $jadwalKhusus = [];
        if (is_array($jadwal)) {
            $jadwalKhusus = array_values(array_filter($jadwal, function ($item) use ($guruMapel) {
                $mapelName = trim($item['mapel'] ?? $item['subject'] ?? $item['materi'] ?? '');
                // Cocokkan apakah kelas ini adalah salah satu mata pelajaran yang diampu dosen ini
                return in_array($mapelName, $guruMapel);
            }));
        }

        if (empty($jadwalKhusus)) {
            $jadwalKhusus = is_array($jadwal) ? $jadwal : [];
        }

        $jadwalKhusus = array_map(function ($item) {
            return array_merge($item, ['parsed_datetime' => $this->parseScheduleDateTime($item)]);
        }, $jadwalKhusus);

        $now = Carbon::now();

        // Sort all schedules chronologically: upcoming first (ascending), past last (descending)
        usort($jadwalKhusus, function ($a, $b) use ($now) {
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

        // Determine nextSession as the first upcoming session
        $nextSession = null;
        foreach ($jadwalKhusus as $item) {
            if ($item['parsed_datetime'] instanceof Carbon) {
                if ($item['parsed_datetime']->greaterThanOrEqualTo($now)) {
                    $nextSession = $item;
                    break;
                }
            } else {
                $nextSession = $item;
                break;
            }
        }

        if (!$nextSession && !empty($jadwalKhusus)) {
            $nextSession = $jadwalKhusus[0];
        }

        $sheetId = env('SHEET_ID_MASTER');
        $sheetUrl = $sheetId ? "https://docs.google.com/spreadsheets/d/{$sheetId}" : null;
        $sheetUrlPenilaian = env('SHEET_URL_PENILAIAN', $sheetUrl);
        $sheetUrlAbsensi = env('SHEET_URL_ABSENSI', $sheetUrl);

        $siswaList = $this->gs->getAllSiswa();
        if (is_array($siswaList) && isset($siswaList['status']) && $siswaList['status'] === 'error') {
            $siswaList = [];
        } else if (is_array($siswaList)) {
            $uniqueSiswa = [];
            foreach ($siswaList as $siswa) {
                if (isset($siswa['email'])) {
                    $uniqueSiswa[strtolower(trim($siswa['email']))] = $siswa;
                }
            }
            $siswaList = array_values($uniqueSiswa);
        }
        $absensi = $this->gs->getAllAbsensi();
        if (is_array($absensi) && isset($absensi['status']) && $absensi['status'] === 'error') {
            $absensi = [];
        }

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
        foreach (is_array($allMateri) ? $allMateri : [] as $m) {
            if (is_array($m)) {
                $mapelName = \App\Services\GoogleSheetService::normalizeMapelName($m['mapel'] ?? '');
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

        // Compute student attendance mapping (unique days present)
        $siswaAbsensiMap = [];
        foreach ($absensi as $a) {
            $email = strtolower(trim($a['email'] ?? ''));
            $mapelRaw = trim($a['mapel'] ?? '');
            $mapel = \App\Services\GoogleSheetService::normalizeMapelName($mapelRaw);
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

        foreach ($siswaAbsensiMap as $email => $mapels) {
            foreach ($mapels as $mapel => $dates) {
                $siswaAbsensiMap[$email][$mapel] = count($dates);
            }
        }

        // Build dynamic mapel list from schedule
        $daftarMapelRaw = [];
        foreach ($jadwalKhusus as $j) {
            $mRaw = trim($j['mapel'] ?? $j['subject'] ?? '');
            $m = \App\Services\GoogleSheetService::normalizeMapelName($mRaw);
            if ($m) {
                $daftarMapelRaw[] = $m;
            }
        }
        // Build dynamic mapel list prioritizing official guru subjects
        $daftarMapel = !empty($guruMapel) ? $guruMapel : array_values(array_unique($daftarMapelRaw));
        if (empty($daftarMapel)) {
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
        }

        $mapelAbbreviations = [
            "Ketentuan Umum dan Tata Cara Perpajakan (KUP) A & B" => "KUP",
            "Pajak Penghasilan (PPh) Orang Pribadi" => "PPh OP",
            "Pajak Pemotongan dan Pemungutan (PPh Pasal 21)" => "PPh 21",
            "Pajak Pemotongan dan Pemungutan (PPh Pasal 22, 23, 26, & 4(2))" => "PPh 22-26",
            "Pajak Penghasilan (PPh) Badan" => "PPh Badan",
            "Pajak Pertambahan Nilai (PPN) dan PPnBM A & B" => "PPN",
            "Pajak Bumi dan Bangunan (PBB), BPHTB, & Bea Meterai" => "PBB",
            "Akuntansi Perpajakan" => "Akuntansi",
            "Pemeriksaan dan Penyidikan Pajak" => "Pemeriksaan",
            "Pengisian e-SPT / Aplikasi Perpajakan (e-Faktur, dll)" => "e-SPT",
            "Tax Planning (Perencanaan Pajak)" => "Tax Planning",
            "Ujian Kelulusan / Komprehensif Brevet" => "Ujian"
        ];

        $announcements = Announcement::with(['comments' => function($q) {
                $q->orderBy('created_at', 'asc');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('guru.dashboard', compact(
            'materi',
            'tugas',
            'jadwalKhusus',
            'nextSession',
            'sheetUrlPenilaian',
            'sheetUrlAbsensi',
            'matakuliah',
            'guru',
            'guruMapel',
            'submissions',
            'siswaList',
            'absensi',
            'mapelSessionCounts',
            'siswaAbsensiMap',
            'daftarMapel',
            'mapelAbbreviations',
            'announcements'
        ));
    }

    /**
     * Filter items by guru's mapel
     */
    private function filterByMapel($items, $guruMapel, $role = '')
    {
        if (in_array(strtoupper($role), ['ADMIN', 'ADMIN_LMS'])) {
            return is_array($items) ? $items : [];
        }

        if (!is_array($items)) {
            return [];
        }

        // Fallback: If guru has no specific mapel assigned yet, show all items so they are not blocked
        if (empty($guruMapel)) {
            return $items;
        }

        $normalizedGuruMapel = array_map(function($m) {
            return \App\Services\GoogleSheetService::normalizeMapelName($m);
        }, (array)$guruMapel);

        return array_values(array_filter($items, function ($item) use ($guruMapel, $normalizedGuruMapel) {
            $itemMapel = $item['mapel'] ?? '';
            if (in_array($itemMapel, $guruMapel)) {
                return true;
            }
            $normItemMapel = \App\Services\GoogleSheetService::normalizeMapelName($itemMapel);
            return in_array($normItemMapel, $normalizedGuruMapel);
        }));
    }

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