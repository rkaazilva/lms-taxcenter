<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * GoogleSheetService
 * 
 * Service terpusat untuk semua komunikasi ke Google Apps Script LMS.
 * Dilengkapi Smart Caching untuk mencegah lag saat 30+ siswa akses bersamaan,
 * dan API Token Security agar hanya sistem kita yang bisa mengakses data.
 */
class GoogleSheetService
{
    private string $apiUrl;
    private string $token;
    private int $cacheTtl;

    public function __construct()
    {
        $this->apiUrl   = env('API_GOOGLE_SHEET');
        $this->token    = env('API_LMS_TOKEN', 'TC_UIN_LMS_SECURE_2026');
        $this->cacheTtl = (int) env('LMS_CACHE_TTL', 86400); // 24 jam default
    }

    /** TTL pendek khusus untuk submissions (data paling dinamis) */
    private function getSubmissionsTtl(): int
    {
        return (int) env('LMS_SUBMISSIONS_TTL', 120); // 2 menit default
    }

    /**
     * Normalisasi nama mata pelajaran agar variasi/singkatan/studi kasus (misal: "ujian 1", "KUP", "PPh OP")
     * terpetakan secara sempurna ke nama resmi 12 mata pelajaran Brevet.
     */
    public static function normalizeMapelName(?string $mapel): string
    {
        if (empty($mapel)) return '';
        $mapel = trim($mapel);

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

        if (in_array($mapel, $daftarMapel)) {
            return $mapel;
        }

        $lower = strtolower($mapel);

        if (strpos($lower, 'ujian') !== false || strpos($lower, 'komprehensif') !== false) {
            return "Ujian Kelulusan / Komprehensif Brevet";
        }
        if (strpos($lower, 'kup') !== false || strpos($lower, 'ketentuan umum') !== false) {
            return "Ketentuan Umum dan Tata Cara Perpajakan (KUP) A & B";
        }
        if (strpos($lower, 'pph op') !== false || strpos($lower, 'orang pribadi') !== false) {
            return "Pajak Penghasilan (PPh) Orang Pribadi";
        }
        if (strpos($lower, 'pph 21') !== false || strpos($lower, 'pasal 21') !== false) {
            return "Pajak Pemotongan dan Pemungutan (PPh Pasal 21)";
        }
        if (strpos($lower, 'pph 22') !== false || strpos($lower, 'pasal 22') !== false || strpos($lower, 'pph 23') !== false || strpos($lower, 'pasal 23') !== false || strpos($lower, '4(2)') !== false) {
            return "Pajak Pemotongan dan Pemungutan (PPh Pasal 22, 23, 26, & 4(2))";
        }
        if (strpos($lower, 'pph badan') !== false || strpos($lower, 'badan') !== false) {
            return "Pajak Penghasilan (PPh) Badan";
        }
        if (strpos($lower, 'ppn') !== false || strpos($lower, 'ppnbm') !== false) {
            return "Pajak Pertambahan Nilai (PPN) dan PPnBM A & B";
        }
        if (strpos($lower, 'pbb') !== false || strpos($lower, 'bphtb') !== false || strpos($lower, 'bea meterai') !== false) {
            return "Pajak Bumi dan Bangunan (PBB), BPHTB, & Bea Meterai";
        }
        if (strpos($lower, 'akuntansi') !== false) {
            return "Akuntansi Perpajakan";
        }
        if (strpos($lower, 'pemeriksaan') !== false || strpos($lower, 'penyidikan') !== false) {
            return "Pemeriksaan dan Penyidikan Pajak";
        }
        if (strpos($lower, 'espt') !== false || strpos($lower, 'e-spt') !== false || strpos($lower, 'efaktur') !== false || strpos($lower, 'e-faktur') !== false) {
            return "Pengisian e-SPT / Aplikasi Perpajakan (e-Faktur, dll)";
        }
        if (strpos($lower, 'tax planning') !== false || strpos($lower, 'perencanaan') !== false) {
            return "Tax Planning (Perencanaan Pajak)";
        }

        foreach ($daftarMapel as $m) {
            if (strcasecmp($m, $mapel) === 0) {
                return $m;
            }
        }

        return $mapel;
    }

    // ============================================================
    // GET REQUESTS (Dengan Smart Caching)
    // ============================================================

    /**
     * Ambil daftar jadwal kelas dari Google Sheets (dengan cache 10 menit)
     */
    public function getJadwal(): array
    {
        return $this->safeRemember('lms_jadwal', $this->cacheTtl, function () {
            return $this->getFromApi('getJadwal');
        });
    }

    /**
     * Ambil semua materi & link rekaman YouTube (dengan cache 10 menit)
     */
    public function getMateri(): array
    {
        return $this->safeRemember('lms_materi', $this->cacheTtl, function () {
            return $this->getFromApi('getMateri');
        });
    }

    /**
     * Ambil daftar tugas yang didefinisikan oleh dosen (dengan cache 10 menit)
     */
    public function getTugas(): array
    {
        return $this->safeRemember('lms_tugas', $this->cacheTtl, function () {
            return $this->getFromApi('getTugas');
        });
    }

    /**
     * Ambil rekap nilai siswa berdasarkan email (tidak di-cache karena personal)
     */
    public function getNilaiSiswa(string $email): array
    {
        return $this->getFromApi('getNilaiSiswa', ['email' => $email]);
    }

    /**
     * Ambil data lengkap dashboard siswa secara optimal.
     * - Data statis (jadwal, materi, tugas) dari cache jika tersedia.
     * - Ambil data statis yang hilang secara paralel bersama nilai siswa.
     */
    public function getDashboardData(string $email): array
    {
        $jadwal = $this->getJadwal();
        $materi = $this->getMateri();
        $tugas  = $this->getTugas();
        $nilai  = $this->getNilaiSiswa($email);

        return [
            'jadwal' => $jadwal,
            'materi' => $materi,
            'tugas'  => $tugas,
            'nilai'  => $nilai,
        ];
    }

    /**
     * Ambil data lengkap dashboard Guru/Tutor secara optimal dengan parallel request & cache.
     */
    public function getGuruDashboardData(): array
    {
        $jadwal      = $this->getJadwal();
        $materi      = $this->getMateri();
        $tugas       = $this->getTugas();
        $matakuliah  = $this->getMatakuliah();
        $submissions = $this->getAllSubmissions();

        return [
            'jadwal' => $jadwal,
            'materi' => $materi,
            'tugas'  => $tugas,
            'matakuliah' => $matakuliah,
            'submissions' => $submissions,
        ];
    }

    // ============================================================
    // POST REQUESTS (Langsung, tidak di-cache)
    // ============================================================

    /**
     * Catat absensi siswa (Live Zoom atau Nonton Rekaman YouTube)
     */
    public function catatAbsen(string $email, string $nama, string $mapel, string $metode): bool
    {
        $result = $this->postToApi([
            'action' => 'catatAbsen',
            'email'  => $email,
            'nama'   => $nama,
            'mapel'  => $mapel,
            'metode' => $metode,
        ]);

        if (isset($result['status']) && $result['status'] === 'success') {
            Cache::forget('lms_absensi');
            return true;
        }

        return false;
    }

    /**
     * Ambil semua data absensi (Admin/Guru)
     */
    public function getAllAbsensi(): array
    {
        return $this->safeRemember('lms_absensi', $this->cacheTtl, function () {
            $data = $this->postToApi(['action' => 'getAllAbsensi']);
            return is_array($data) ? $data : [];
        }, true);
    }

    /**
     * Ambil data absensi siswa spesifik (Siswa)
     */
    public function getAbsensiSiswa(string $email): array
    {
        $data = $this->postToApi([
            'action' => 'getAbsensiSiswa',
            'email' => $email
        ]);
        return is_array($data) ? $data : [];
    }

    /**
     * Tambah absensi manual (Admin/Guru)
     */
    public function addAbsensiManual(string $email, string $nama, string $mapel, string $metode, ?string $timestamp = null): array
    {
        $payload = [
            'action' => 'addAbsensiManual',
            'email'  => $email,
            'nama'   => $nama,
            'mapel'  => $mapel,
            'metode' => $metode,
        ];
        if ($timestamp) {
            $payload['timestamp'] = $timestamp;
        }
        Cache::forget('lms_absensi');
        return $this->postToApi($payload);
    }

    /**
     * Hapus absensi (Admin)
     */
    public function deleteAbsensi(string $email, string $mapel, string $timestamp): array
    {
        Cache::forget('lms_absensi');
        return $this->postToApi([
            'action'    => 'deleteAbsensi',
            'email'     => $email,
            'mapel'     => $mapel,
            'timestamp' => $timestamp,
        ]);
    }

    /**
     * Kirim tugas siswa ke Google Drive dan catat di sheet SUBMISSION_TUGAS
     */
    public function submitTugas(array $payload): array
    {
        $payload['action'] = 'submitTugas';
        return $this->postToApi($payload);
    }

    /**
     * Tambah materi baru oleh Tutor
     */
    public function addMateri(array $data): array
    {
        $data['action'] = 'addMateri';
        Cache::forget('lms_materi'); // Paksa refresh cache materi
        return $this->postToApi($data);
    }

    /**
     * Tambah tugas baru oleh Tutor
     */
    public function addTugas(array $data): array
    {
        $data['action'] = 'addTugas';
        Cache::forget('lms_tugas'); // Paksa refresh cache tugas
        return $this->postToApi($data);
    }

    /**
     * Perbarui materi yang sudah ada menurut ID materi
     */
    public function updateMateri(array $data): array
    {
        $data['action'] = 'updateMateriByKey';
        Cache::forget('lms_materi');
        return $this->postToApi($data);
    }

    /**
     * Perbarui tugas yang sudah ada menurut ID tugas
     */
    public function updateTugas(array $data): array
    {
        $data['action'] = 'updateTugas';
        Cache::forget('lms_tugas');
        return $this->postToApi($data);
    }

    /**
     * Upload materi file to Drive and return link (via Apps Script)
     */
    public function uploadMateriFile(array $payload): array
    {
        $payload['action'] = 'submitMateriFile';
        return $this->postToApi($payload);
    }

    // ============================================================
    // ADMIN: Jadwal & Materi Management
    // ============================================================

    /**
     * Ambil daftar mata pelajaran untuk dropdown (dari MATERI_BELAJAR)
     */
    public function getMatakuliah(): array
    {
        return $this->safeRemember('lms_matakuliah', $this->cacheTtl, function () {
            $res = $this->getFromApi('getMatakuliah');
            return is_array($res) && isset($res['data']) ? $res['data'] : (is_array($res) ? $res : []);
        });
    }

    /**
     * Update jadwal existing (Admin)
     */
    public function updateJadwal(array $data): array
    {
        $data['action'] = 'updateJadwal';
        Cache::forget('lms_jadwal');
        return $this->postToApi($data);
    }

    /**
     * Delete jadwal (Admin)
     */
    public function deleteJadwal(array $data): array
    {
        $data['action'] = 'deleteJadwal';
        Cache::forget('lms_jadwal');
        return $this->postToApi($data);
    }

    /**
     * Update password / profil di Google Sheets (Self Service)
     */
    public function updateProfile(string $email, string $nama, ?string $newPassword): array
    {
        return $this->postToApi([
            'action'       => 'updateProfile',
            'email'        => $email,
            'nama'         => $nama,
            'new_password' => $newPassword,
        ]);
    }

    /**
     * Update YouTube link untuk materi (Admin only)
     */
    public function updateMateriYoutube(array $data): array
    {
        $data['action'] = 'updateMateriYoutube';
        Cache::forget('lms_materi');
        return $this->postToApi($data);
    }

    /**
     * Ambil semua submission tugas dari siswa (Guru/Tutor & Admin)
     */
    public function getAllSubmissions(): array
    {
        return $this->safeRemember('lms_submissions', $this->getSubmissionsTtl(), function () {
            $data = $this->getFromApi('getAllSubmissions');
            return is_array($data) ? $data : [];
        }, true);
    }

    /**
     * Berikan nilai & feedback untuk tugas siswa (Guru/Tutor & Admin)
     */
    public function gradeSubmission(array $data): array
    {
        $data['action'] = 'penilaianTugas';
        Cache::forget('lms_submissions'); // Hapus cache submissions agar langsung update
        return $this->postToApi($data);
    }

    /**
     * Berikan nilai & feedback secara massal (batch) untuk tugas siswa
     */
    public function gradeSubmissionsBatch(array $items): array
    {
        Cache::forget('lms_submissions'); // Hapus cache submissions agar langsung update
        return $this->postToApi([
            'action' => 'batchPenilaianTugas',
            'items' => $items
        ]);
    }

    /**
     * Ambil daftar semua siswa terdaftar dari Google Sheets (DATA_LOGIN)
     */
    public function getAllSiswa(): array
    {
        return $this->safeRemember('lms_siswa_list', $this->cacheTtl, function () {
            return $this->getFromApi('getAllSiswa');
        });
    }

    // ============================================================
    // ADMIN: Paksa Refresh Cache (Tombol "Sinkronisasi Data Google")
    // ============================================================
    public function clearAllCache(): void
    {
        Log::info("[GoogleSheetService] Membersihkan cache LMS statis...");
        Cache::forget('lms_jadwal');
        Cache::forget('lms_materi');
        Cache::forget('lms_tugas');
        Cache::forget('lms_matakuliah');
        Cache::forget('lms_submissions');
        Cache::forget('lms_siswa_list');
        Cache::forget('lms_absensi'); // Hapus cache absensi saat sync manual
        
        Log::info("[GoogleSheetService] Memulai sinkronisasi data login user ke SQLite lokal...");
        $this->syncLmsUsers();

        $this->warmCache(true);

        // Panaskan cache siswa dan absensi secara sekuensial agar tidak memicu limit konkurensi Google Script
        try {
            $this->getAllSiswa();
            $this->getAllAbsensi();
        } catch (\Exception $e) {
            Log::warning("[GoogleSheetService] Gagal memanaskan cache siswa/absensi saat sync: " . $e->getMessage());
        }
    }

    /**
     * Sinkronisasi data user dari Google Sheets DATA_LOGIN ke SQLite lms_users
     */
    public function syncLmsUsers(): bool
    {
        $apiKey = env('GOOGLE_API_KEY');
        $sheetId = env('SHEET_ID_MASTER');
        
        if (empty($apiKey) || empty($sheetId)) {
            Log::warning("[GoogleSheetService] syncLmsUsers dibatalkan: GOOGLE_API_KEY atau SHEET_ID_MASTER kosong di .env.");
            return false;
        }

        try {
            $url = "https://sheets.googleapis.com/v4/spreadsheets/{$sheetId}/values/DATA_LOGIN!A2:G100?key={$apiKey}";
            $response = Http::retry(2, 50)
                ->timeout(6)
                ->get($url);

            if (!$response->successful()) {
                Log::error("[GoogleSheetService] syncLmsUsers - Error Google Sheets API: " . $response->status() . " - " . $response->body());
                return false;
            }

            $data = $response->json();
            $values = $data['values'] ?? [];
            
            $syncedEmails = [];

            foreach ($values as $row) {
                $email = isset($row[0]) ? trim(strtolower($row[0])) : '';
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }

                $password = isset($row[1]) ? trim($row[1]) : '';
                $role = isset($row[2]) ? trim(strtoupper($row[2])) : 'SISWA';
                $nama = isset($row[3]) ? preg_replace('/^Halo\s+/i', '', trim($row[3])) : '';
                $link = isset($row[4]) ? trim($row[4]) : '';
                $sertifikat = isset($row[5]) ? trim($row[5]) : '';
                $kelas = isset($row[6]) ? trim($row[6]) : '';

                // Cek apakah password sudah ter-hash. Jika belum, kita hash.
                $hashedPassword = (strpos($password, '$2y$') === 0 && strlen($password) === 60) 
                    ? $password 
                    : \Illuminate\Support\Facades\Hash::make($password);

                // Upsert data ke SQLite
                \App\Models\LmsUser::updateOrCreate(
                    ['email' => $email],
                    [
                        'password' => $hashedPassword,
                        'role' => $role,
                        'nama' => $nama,
                        'link' => $link,
                        'sertifikat' => $sertifikat,
                        'kelas' => $kelas,
                    ]
                );

                $syncedEmails[] = $email;
            }

            // Pruning: Hapus akun lokal yang sudah dihapus dari Google Sheets
            // Threshold minimum 3 user agar pruning tidak berjalan jika Google Sheets API timeout / respons kosong
            $MINIMUM_USERS_THRESHOLD = 3;
            if (count($syncedEmails) >= $MINIMUM_USERS_THRESHOLD) {
                \App\Models\LmsUser::whereNotIn('email', $syncedEmails)->delete();
                Log::info("[GoogleSheetService] syncLmsUsers - Sinkronisasi berhasil. Total: " . count($syncedEmails) . " user terdaftar.");
            } elseif (count($syncedEmails) > 0) {
                Log::warning("[GoogleSheetService] syncLmsUsers - Sync dapat " . count($syncedEmails) . " user (di bawah threshold). Pruning dilewati untuk keamanan data.");
            } else {
                Log::warning("[GoogleSheetService] syncLmsUsers - Tidak ada data user valid yang ditemukan. Pruning dibatalkan.");
            }

            return true;

        } catch (\Exception $e) {
            Log::error("[GoogleSheetService] syncLmsUsers - Exception saat sinkronisasi: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Melakukan pre-fetch data statis secara paralel dan mengisi cache.
     * Mencegah lag/bottleneck saat banyak siswa mengakses web secara bersamaan.
     */
    public function warmCache(bool $force = false): void
    {
        $hasJadwal = Cache::has('lms_jadwal');
        $hasMateri = Cache::has('lms_materi');
        $hasTugas = Cache::has('lms_tugas');
        $hasMatakuliah = Cache::has('lms_matakuliah');

        if ($force || !$hasJadwal || !$hasMateri || !$hasTugas || !$hasMatakuliah) {
            try {
                $jadwalData = $this->getFromApi('getJadwal');
                Cache::put('lms_jadwal', is_array($jadwalData) ? $jadwalData : [], $this->cacheTtl);

                $materiData = $this->getFromApi('getMateri');
                Cache::put('lms_materi', is_array($materiData) ? $materiData : [], $this->cacheTtl);

                $tugasData = $this->getFromApi('getTugas');
                Cache::put('lms_tugas', is_array($tugasData) ? $tugasData : [], $this->cacheTtl);

                $res = $this->getFromApi('getMatakuliah');
                $matakuliahData = is_array($res) && isset($res['data']) ? $res['data'] : (is_array($res) ? $res : []);
                Cache::put('lms_matakuliah', $matakuliahData, $this->cacheTtl);
            } catch (\Exception $e) {
                Log::error("[GoogleSheetService] warmCache sequential request failed: " . $e->getMessage());
            }
        }
    }

    /**
     * Kirim pesan broadcast ke WhatsApp Group menggunakan Fonnte API
     */
    public function sendWaBroadcast(string $message): bool
    {
        $token = env('FONNTE_TOKEN');
        $target = env('FONNTE_TARGET');

        if (empty($token) || empty($target)) {
            Log::warning("[GoogleSheetService] sendWaBroadcast aborted: FONNTE_TOKEN or FONNTE_TARGET is empty.");
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->timeout(15)->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                Log::info("[GoogleSheetService] sendWaBroadcast response: " . json_encode($result));
                return isset($result['status']) && $result['status'] == true;
            }

            Log::error("[GoogleSheetService] sendWaBroadcast - HTTP Error {$response->status()}: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("[GoogleSheetService] sendWaBroadcast Exception: " . $e->getMessage());
            return false;
        }
    }

    // ============================================================
    // HELPER METHODS PRIVATE
    // ============================================================

    private function getFromApi(string $action, array $extraParams = []): array
    {
        try {
            $params = array_merge([
                'action' => $action,
                'token'  => $this->token,
            ], $extraParams);

            // Gunakan timeout pendek (6s) agar fail fast jika Google Script lemot / koneksi putus-putus
            $response = Http::retry(2, 50)->timeout(6)->get($this->apiUrl, $params);

            if ($response->successful()) {
                $data = $response->json();
                // Jika response adalah array sukses, kembalikan
                return is_array($data) ? $data : [];
            }

            Log::error("[GoogleSheetService] GET {$action} - HTTP Error: " . $response->status());
            return [];

        } catch (\Exception $e) {
            Log::error("[GoogleSheetService] GET {$action} - Exception: " . $e->getMessage());
            return [];
        }
    }

    public function forgetAllCacheKeys(): void
    {
        Cache::forget('lms_jadwal');
        Cache::forget('lms_materi');
        Cache::forget('lms_tugas');
        Cache::forget('lms_matakuliah');
        Cache::forget('lms_submissions');
        Cache::forget('lms_siswa_list');
        Cache::forget('lms_absensi');
    }

    public function postToApi(array $payload): array
    {
        try {
            // Selalu sertakan token di setiap POST request
            $payload['token'] = $this->token;

            // Post data ke Drive biasanya butuh waktu, timeout 12s sudah sangat cukup untuk fail fast
            $response = Http::retry(2, 100)->timeout(12)->post($this->apiUrl, $payload);

            if ($response->successful()) {
                $result = $response->json() ?? ['status' => 'error', 'message' => 'Respons kosong'];
                if (isset($result['status']) && $result['status'] === 'success') {
                    $this->forgetAllCacheKeys();
                    Log::info("[GoogleSheetService] Local cache keys cleared instantly after successful post action: {$payload['action']}");
                }
                return $result;
            }

            Log::error("[GoogleSheetService] POST {$payload['action']} - HTTP Error: " . $response->status());
            return ['status' => 'error', 'message' => 'Koneksi ke server gagal'];

        } catch (\Exception $e) {
            Log::error("[GoogleSheetService] POST Exception: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function safeRemember(string $key, int $ttl, \Closure $callback, bool $canBeEmpty = false): array
    {
        $cached = Cache::get($key);
        if (is_array($cached)) {
            return $cached;
        }

        try {
            $data = $callback();
        } catch (\Exception $e) {
            $data = [];
        }

        if (is_array($data)) {
            $isEmpty = empty($data);
            $isError = isset($data['status']) && $data['status'] === 'error';

            if ($isError) {
                Cache::put($key, $data, 10); // Cache error hanya 10 detik
            } elseif ($isEmpty) {
                if ($canBeEmpty) {
                    Cache::put($key, $data, min($ttl, 120)); // Jika absensi/tugas kosong, cache maks 2 menit
                } else {
                    Cache::put($key, $data, 10); // Jika jadwal/materi kosong (biasanya error sheet), cache 10 detik
                }
            } else {
                Cache::put($key, $data, $ttl);
            }
        } else {
            $data = [];
        }

        return $data;
    }
}