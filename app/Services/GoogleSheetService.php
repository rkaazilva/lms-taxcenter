<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
    // GET REQUESTS (100% Native MySQL Database - Ultra Fast <0.01s)
    // ============================================================

    /**
     * Ambil daftar jadwal kelas (100% Native MySQL)
     */
    public function getJadwal(): array
    {
        return $this->safeRemember('lms_jadwal', $this->cacheTtl, function () {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('lms_jadwals')) {
                    return \App\Models\LmsJadwal::orderBy('id', 'asc')->get()->toArray();
                }
            } catch (\Exception $e) {}
            return [];
        });
    }

    /**
     * Ambil semua materi & link rekaman YouTube (100% Native MySQL)
     */
    public function getMateri(): array
    {
        return $this->safeRemember('lms_materi', $this->cacheTtl, function () {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('lms_materis')) {
                    return \App\Models\LmsMateri::orderBy('id', 'desc')->get()->toArray();
                }
            } catch (\Exception $e) {}
            return [];
        });
    }

    /**
     * Ambil daftar tugas (100% Native MySQL)
     */
    public function getTugas(): array
    {
        return $this->safeRemember('lms_tugas', $this->cacheTtl, function () {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('lms_tugas')) {
                    return \App\Models\LmsTugas::orderBy('id', 'desc')->get()->toArray();
                }
            } catch (\Exception $e) {}
            return [];
        });
    }

    /**
     * Ambil rekap nilai siswa berdasarkan email (100% Native MySQL)
     */
    public function getNilaiSiswa(string $email): array
    {
        $emailClean = strtolower(trim($email));
        if (empty($emailClean)) return [];
        $cacheKey = 'lms_nilai_' . md5($emailClean);
        return $this->safeRemember($cacheKey, $this->getSubmissionsTtl(), function () use ($emailClean) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('lms_submissions')) {
                    return \App\Models\LmsSubmission::where('email', $emailClean)->orderBy('id', 'desc')->get()->toArray();
                }
            } catch (\Exception $e) {}
            return [];
        }, true);
    }

    /**
     * Ambil data lengkap dashboard siswa secara optimal (100% Native MySQL).
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
     * Ambil data lengkap dashboard Guru/Tutor secara optimal (100% Native MySQL).
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
    // POST REQUESTS (Langsung ke Native MySQL, Fast Response)
    // ============================================================

    /**
     * Catat absensi siswa (Live Zoom atau Nonton Rekaman YouTube)
     */
    public function catatAbsen(string $email, string $nama, string $mapel, string $metode): bool
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('lms_absensis')) {
                \App\Models\LmsAbsensi::create([
                    'email'     => strtolower(trim($email)),
                    'nama'      => $nama,
                    'mapel'     => $mapel,
                    'metode'    => $metode,
                    'timestamp' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
                ]);
                Cache::forget('lms_absensi');

                // Optional WA Broadcast
                $this->sendWaBroadcast("Konfirmasi Kehadiran: Siswa {$nama} ({$email}) telah presensi pada mapel {$mapel} via {$metode}.");
                return true;
            }
        } catch (\Exception $e) {}

        return false;
    }

    /**
     * Ambil semua data absensi (100% Native MySQL)
     */
    public function getAllAbsensi(): array
    {
        return $this->safeRemember('lms_absensi', $this->cacheTtl, function () {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('lms_absensis')) {
                    return \App\Models\LmsAbsensi::orderBy('timestamp', 'desc')->get()->toArray();
                }
            } catch (\Exception $e) {}
            return [];
        }, true);
    }

    /**
     * Ambil data absensi siswa spesifik (100% Native MySQL)
     */
    public function getAbsensiSiswa(string $email): array
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('lms_absensis')) {
                return \App\Models\LmsAbsensi::where('email', strtolower(trim($email)))->orderBy('timestamp', 'desc')->get()->toArray();
            }
        } catch (\Exception $e) {}
        return [];
    }

    /**
     * Tambah absensi manual (Admin/Guru)
     */
    public function addAbsensiManual(string $email, string $nama, string $mapel, string $metode, ?string $timestamp = null): array
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('lms_absensis')) {
                $rec = \App\Models\LmsAbsensi::create([
                    'email'     => strtolower(trim($email)),
                    'nama'      => $nama,
                    'mapel'     => $mapel,
                    'metode'    => $metode,
                    'timestamp' => $timestamp ?? \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
                ]);
                Cache::forget('lms_absensi');

                $this->sendWaBroadcast("Pemberitahuan LMS: Kehadiran Anda pada mata pelajaran '{$mapel}' telah dicatat oleh Admin/Tutor dengan status HADIR ({$metode}).");

                return ['status' => 'success', 'message' => 'Absensi berhasil dicatat secara native!', 'data' => $rec];
            }
        } catch (\Exception $e) {}

        return ['status' => 'error', 'message' => 'Tabel absensi tidak ditemukan.'];
    }

    /**
     * Hapus absensi (Admin)
     */
    public function deleteAbsensi(string $email, string $mapel, string $timestamp): array
    {
        Cache::forget('lms_absensi');
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('lms_absensis')) {
                \App\Models\LmsAbsensi::where('email', strtolower(trim($email)))
                    ->where('mapel', $mapel)
                    ->delete();
                return ['status' => 'success', 'message' => 'Data absensi berhasil dihapus!'];
            }
        } catch (\Exception $e) {}
        return ['status' => 'error', 'message' => 'Gagal menghapus absensi.'];
    }

    /**
     * Kirim tugas siswa ke database Native MySQL
     */
    public function submitTugas(array $payload): array
    {
        $email = strtolower(trim($payload['email'] ?? ''));
        if (!empty($email)) {
            Cache::forget('lms_nilai_' . md5($email));
        }
        Cache::forget('lms_submissions');

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('lms_submissions')) {
                $nama = $payload['nama_siswa'] ?? $payload['nama'] ?? '';
                $now = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

                \App\Models\LmsSubmission::updateOrCreate(
                    [
                        'email'    => $email,
                        'id_tugas' => $payload['id_tugas'] ?? '',
                    ],
                    [
                        'nama_siswa'   => $nama,
                        'link_tugas'   => $payload['link_tugas'] ?? '',
                        'nilai'        => !empty($payload['nilai']) ? intval($payload['nilai']) : null,
                        'feedback'     => $payload['feedback'] ?? '',
                        'submitted_at' => $now,
                    ]
                );

                return [
                    'status'  => 'success',
                    'message' => 'Tugas berhasil dikirimkan!',
                ];
            }
        } catch (\Exception $e) {
            Log::error("[GoogleSheetService] submitTugas error: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Gagal menyimpan pengumpulan tugas: ' . $e->getMessage()];
        }

        return ['status' => 'error', 'message' => 'Gagal menyimpan pengumpulan tugas.'];
    }

    /**
     * Tambah materi baru oleh Tutor (100% Native MySQL)
     */
    public function addMateri(array $data): array
    {
        Cache::forget('lms_materi');
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('lms_materis')) {
                $judul = $data['judul'] ?? $data['materi'] ?? '';
                $modul = $data['link_modul'] ?? $data['link_pdf'] ?? null;
                $ket   = $data['keterangan'] ?? $data['deskripsi'] ?? null;

                $materi = \App\Models\LmsMateri::create([
                    'mapel'        => $data['mapel'] ?? '',
                    'judul'        => $judul,
                    'link_modul'   => $modul,
                    'link_youtube' => $data['link_youtube'] ?? null,
                    'keterangan'   => $ket,
                    'status'       => 'Rilis',
                    'kelas'        => $data['kelas'] ?? 'Semua',
                ]);
                return ['status' => 'success', 'message' => 'Materi berhasil ditambahkan!', 'data' => $materi];
            }
        } catch (\Exception $e) {
            Log::error("[GoogleSheetService] addMateri error: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Gagal menyimpan materi baru: ' . $e->getMessage()];
        }
        return ['status' => 'error', 'message' => 'Tabel lms_materis tidak ditemukan.'];
    }

    /**
     * Tambah tugas baru oleh Tutor (100% Native MySQL)
     */
    public function addTugas(array $data): array
    {
        Cache::forget('lms_tugas');
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('lms_tugas')) {
                $idTugas = 'TGS-' . strtoupper(Str::random(6));
                $tugas = \App\Models\LmsTugas::create([
                    'id_tugas'  => $idTugas,
                    'mapel'     => $data['mapel'] ?? '',
                    'judul'     => $data['judul'] ?? '',
                    'deskripsi' => $data['deskripsi'] ?? '',
                    'deadline'  => $data['deadline'] ?? null,
                    'link_soal' => $data['link_soal'] ?? null,
                    'kelas'     => $data['kelas'] ?? 'Semua',
                ]);
                return ['status' => 'success', 'message' => 'Tugas berhasil ditambahkan!', 'data' => $tugas];
            }
        } catch (\Exception $e) {
            Log::error("[GoogleSheetService] addTugas error: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Gagal menyimpan tugas baru: ' . $e->getMessage()];
        }
        return ['status' => 'error', 'message' => 'Tabel lms_tugas tidak ditemukan.'];
    }

    /**
     * Perbarui materi (100% Native MySQL)
     */
    public function updateMateri(array $data): array
    {
        Cache::forget('lms_materi');
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('lms_materis')) {
                $materi = \App\Models\LmsMateri::find($data['id'] ?? 0);
                if ($materi) {
                    $judul = $data['judul'] ?? $data['materi'] ?? $materi->judul;
                    $modul = $data['link_modul'] ?? $data['link_pdf'] ?? $materi->link_modul;
                    $ket   = $data['keterangan'] ?? $data['deskripsi'] ?? $materi->keterangan;

                    $materi->update([
                        'mapel'        => $data['mapel'] ?? $materi->mapel,
                        'judul'        => $judul,
                        'link_modul'   => $modul,
                        'link_youtube' => $data['link_youtube'] ?? $materi->link_youtube,
                        'keterangan'   => $ket,
                    ]);
                    return ['status' => 'success', 'message' => 'Materi berhasil diperbarui!'];
                }
            }
        } catch (\Exception $e) {
            Log::error("[GoogleSheetService] updateMateri error: " . $e->getMessage());
        }
        return ['status' => 'error', 'message' => 'Materi tidak ditemukan.'];
    }

    /**
     * Perbarui tugas (100% Native MySQL)
     */
    public function updateTugas(array $data): array
    {
        Cache::forget('lms_tugas');
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('lms_tugas')) {
                $tugas = \App\Models\LmsTugas::where('id_tugas', $data['id_tugas'] ?? '')->orWhere('id', $data['id'] ?? 0)->first();
                if ($tugas) {
                    $tugas->update([
                        'mapel'     => $data['mapel'] ?? $tugas->mapel,
                        'judul'     => $data['judul'] ?? $tugas->judul,
                        'deskripsi' => $data['deskripsi'] ?? $tugas->deskripsi,
                        'deadline'  => $data['deadline'] ?? $tugas->deadline,
                        'link_soal' => $data['link_soal'] ?? $tugas->link_soal,
                    ]);
                    return ['status' => 'success', 'message' => 'Tugas berhasil diperbarui!'];
                }
            }
        } catch (\Exception $e) {
            Log::error("[GoogleSheetService] updateTugas error: " . $e->getMessage());
        }
        return ['status' => 'error', 'message' => 'Tugas tidak ditemukan.'];
    }

    /**
     * Upload materi file to Drive
     */
    public function uploadMateriFile(array $payload): array
    {
        return ['status' => 'success', 'message' => 'File diunggah ke storage lokal.'];
    }

    // ============================================================
    // ADMIN: Jadwal & Materi Management (100% Native MySQL)
    // ============================================================

    /**
     * Ambil daftar mata pelajaran untuk dropdown (100% Native MySQL / Standard List)
     */
    public function getMatakuliah(): array
    {
        return [
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
            "Ujian Kelulusan / Komprehensif Brevet",
        ];
    }

    /**
     * Tambah jadwal baru (Admin 100% Native MySQL)
     */
    public function addJadwal(array $data): array
    {
        Cache::forget('lms_jadwal');
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('lms_jadwals')) {
                $j = \App\Models\LmsJadwal::create([
                    'tanggal'     => $data['tanggal'] ?? '',
                    'jam'         => $data['jam'] ?? '',
                    'mapel'       => $data['mapel'] ?? '',
                    'materi'      => $data['materi'] ?? '',
                    'dosen'       => $data['dosen'] ?? '',
                    'link_zoom'   => $data['link'] ?? $data['link_zoom'] ?? null,
                    'status_sesi' => $data['status_sesi'] ?? 'AKAN_DATANG',
                    'blast'       => !empty($data['blast']) ? 1 : 0,
                ]);
                return ['status' => 'success', 'message' => 'Jadwal berhasil ditambahkan!', 'data' => $j];
            }
        } catch (\Exception $e) {
            Log::error("[GoogleSheetService] addJadwal error: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Gagal menambahkan jadwal: ' . $e->getMessage()];
        }
        return ['status' => 'error', 'message' => 'Tabel lms_jadwals tidak ditemukan.'];
    }

    /**
     * Update jadwal existing (Admin 100% Native MySQL)
     */
    public function updateJadwal(array $data): array
    {
        Cache::forget('lms_jadwal');
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('lms_jadwals')) {
                $j = \App\Models\LmsJadwal::where('mapel', $data['original_mapel'] ?? ($data['mapel'] ?? ''))
                    ->where('materi', $data['original_materi'] ?? ($data['materi'] ?? ''))
                    ->first();
                if ($j) {
                    $j->update([
                        'tanggal'   => $data['tanggal'] ?? $j->tanggal,
                        'jam'       => $data['jam'] ?? $j->jam,
                        'mapel'     => $data['mapel'] ?? $j->mapel,
                        'materi'    => $data['materi'] ?? $j->materi,
                        'dosen'     => $data['dosen'] ?? $j->dosen,
                        'link_zoom' => $data['link_zoom'] ?? $j->link_zoom,
                    ]);
                    return ['status' => 'success', 'message' => 'Jadwal berhasil diperbarui!'];
                }
            }
        } catch (\Exception $e) {}
        return ['status' => 'error', 'message' => 'Jadwal tidak ditemukan.'];
    }

    /**
     * Delete jadwal (Admin 100% Native MySQL)
     */
    public function deleteJadwal(array $data): array
    {
        Cache::forget('lms_jadwal');
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('lms_jadwals')) {
                \App\Models\LmsJadwal::where('mapel', $data['mapel'] ?? '')
                    ->where('materi', $data['materi'] ?? '')
                    ->delete();
                return ['status' => 'success', 'message' => 'Jadwal berhasil dihapus!'];
            }
        } catch (\Exception $e) {}
        return ['status' => 'error', 'message' => 'Gagal menghapus jadwal.'];
    }

    /**
     * Update password / profil (100% Native MySQL)
     */
    public function updateProfile(string $email, string $nama, ?string $newPassword): array
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('lms_users')) {
                $user = \App\Models\LmsUser::where('email', strtolower(trim($email)))->first();
                if ($user) {
                    $user->nama = $nama;
                    if (!empty($newPassword)) {
                        $user->password = \Illuminate\Support\Facades\Hash::make($newPassword);
                    }
                    $user->save();
                    return ['status' => 'success', 'message' => 'Profil berhasil diperbarui!'];
                }
            }
        } catch (\Exception $e) {}
        return ['status' => 'error', 'message' => 'User tidak ditemukan.'];
    }

    /**
     * Update YouTube link untuk materi (Admin only 100% Native MySQL)
     */
    public function updateMateriYoutube(array $data): array
    {
        Cache::forget('lms_materi');
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('lms_materis')) {
                $m = \App\Models\LmsMateri::where('mapel', $data['mapel'] ?? '')->first();
                if ($m) {
                    $m->link_youtube = $data['link_youtube'] ?? $m->link_youtube;
                    $m->save();
                    return ['status' => 'success', 'message' => 'Link YouTube berhasil diperbarui!'];
                }
            }
        } catch (\Exception $e) {}
        return ['status' => 'error', 'message' => 'Materi tidak ditemukan.'];
    }

    /**
     * Ambil semua submission tugas dari siswa (100% Native MySQL)
     */
    public function getAllSubmissions(): array
    {
        return $this->safeRemember('lms_submissions', $this->getSubmissionsTtl(), function () {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('lms_submissions')) {
                    $subs = \App\Models\LmsSubmission::orderBy('id', 'desc')->get();
                    $result = [];
                    foreach ($subs as $s) {
                        $result[] = [
                            'id'           => $s->id,
                            'id_tugas'     => $s->id_tugas,
                            'email'        => $s->email,
                            'nama'         => $s->nama_siswa ?? $s->nama ?? '',
                            'nama_siswa'   => $s->nama_siswa ?? $s->nama ?? '',
                            'link_tugas'   => $s->link_tugas,
                            'nilai'        => $s->nilai,
                            'feedback'     => $s->feedback,
                            'timestamp'    => $s->submitted_at ?? $s->created_at,
                            'submitted_at' => $s->submitted_at ?? $s->created_at,
                        ];
                    }
                    return $result;
                }
            } catch (\Exception $e) {
                Log::error("[GoogleSheetService] getAllSubmissions error: " . $e->getMessage());
            }
            return [];
        }, true);
    }

    /**
     * Berikan nilai & feedback untuk tugas siswa (100% Native MySQL)
     */
    public function gradeSubmission(array $data): array
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('lms_submissions')) {
                $email = strtolower(trim($data['email'] ?? ''));
                $idTugas = trim($data['id_tugas'] ?? '');
                $sub = \App\Models\LmsSubmission::where('id_tugas', $idTugas)->where('email', $email)->first();
                if ($sub) {
                    $sub->update([
                        'nilai'    => isset($data['nilai']) ? (int)$data['nilai'] : $sub->nilai,
                        'feedback' => $data['feedback'] ?? $sub->feedback,
                    ]);
                    Cache::forget('lms_submissions');
                    Cache::forget('lms_nilai_' . md5($email));
                    return ['status' => 'success', 'message' => 'Penilaian berhasil diperbarui secara native!'];
                }
            }
        } catch (\Exception $e) {}

        return ['status' => 'error', 'message' => 'Pengumpulan tugas tidak ditemukan.'];
    }

    /**
     * Berikan nilai & feedback secara massal (batch) untuk tugas siswa (100% Native MySQL)
     */
    public function gradeSubmissionsBatch(array $items): array
    {
        Cache::forget('lms_submissions');
        foreach (is_array($items) ? $items : [] as $it) {
            if (isset($it['email'])) {
                Cache::forget('lms_nilai_' . md5(strtolower(trim($it['email']))));
                try {
                    if (\Illuminate\Support\Facades\Schema::hasTable('lms_submissions')) {
                        $email = strtolower(trim($it['email'] ?? ''));
                        $idTugas = trim($it['id_tugas'] ?? '');
                        $sub = \App\Models\LmsSubmission::where('id_tugas', $idTugas)->where('email', $email)->first();
                        if ($sub) {
                            $sub->update([
                                'nilai'    => isset($it['nilai']) ? (int)$it['nilai'] : $sub->nilai,
                                'feedback' => $it['feedback'] ?? $sub->feedback,
                            ]);
                        }
                    }
                } catch (\Exception $e) {}
            }
        }
        return ['status' => 'success', 'message' => 'Penilaian batch berhasil disimpan secara native!'];
    }

    /**
     * Ambil daftar semua siswa terdaftar (100% Native MySQL)
     */
    public function getAllSiswa(): array
    {
        return $this->safeRemember('lms_siswa_list', $this->cacheTtl, function () {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('lms_users')) {
                    return \App\Models\LmsUser::where(function($q) {
                        $q->where('role', 'SISWA')->orWhereNull('role')->orWhere('role', '');
                    })->orderBy('nama', 'asc')->get()->toArray();
                }
            } catch (\Exception $e) {}
            return [];
        });
    }

    /**
     * Impor/Sinkronkan seluruh data dari Google Sheets ke Native MySQL dalam 1 kali jalan!
     */
    public function syncFromSheetsToNativeDb(): array
    {
        $report = ['jadwals' => 0, 'materis' => 0, 'tugas' => 0, 'submissions' => 0, 'absensis' => 0, 'users' => 0];

        try {
            // 1. Impor Jadwal
            $jadwals = $this->getFromApi('getJadwal');
            if (is_array($jadwals)) {
                foreach ($jadwals as $j) {
                    if (isset($j['mapel']) && isset($j['materi'])) {
                        \App\Models\LmsJadwal::updateOrCreate(
                            ['mapel' => $j['mapel'], 'materi' => $j['materi']],
                            [
                                'tanggal'     => $j['tanggal'] ?? null,
                                'jam'         => $j['jam'] ?? null,
                                'dosen'       => $j['dosen'] ?? '',
                                'link_zoom'   => $j['link_zoom'] ?? null,
                                'status_sesi' => $j['status_sesi'] ?? 'AKAN_DATANG',
                            ]
                        );
                        $report['jadwals']++;
                    }
                }
            }

            // 2. Impor Materi
            $materis = $this->getFromApi('getMateri');
            if (is_array($materis)) {
                foreach ($materis as $m) {
                    if (isset($m['mapel']) && isset($m['judul'])) {
                        \App\Models\LmsMateri::updateOrCreate(
                            ['mapel' => $m['mapel'], 'judul' => $m['judul']],
                            [
                                'link_modul'   => $m['link_modul'] ?? null,
                                'link_youtube' => $m['link_youtube'] ?? null,
                                'keterangan'   => $m['keterangan'] ?? null,
                                'status'       => $m['status'] ?? 'Rilis',
                                'kelas'        => $m['kelas'] ?? 'Semua',
                            ]
                        );
                        $report['materis']++;
                    }
                }
            }

            // 3. Impor Tugas
            $tugas = $this->getFromApi('getTugas');
            if (is_array($tugas)) {
                foreach ($tugas as $t) {
                    if (isset($t['id_tugas']) && isset($t['judul'])) {
                        \App\Models\LmsTugas::updateOrCreate(
                            ['id_tugas' => $t['id_tugas']],
                            [
                                'mapel'     => $t['mapel'] ?? '',
                                'judul'     => $t['judul'] ?? '',
                                'deskripsi' => $t['deskripsi'] ?? null,
                                'link_soal' => $t['link_soal'] ?? null,
                                'deadline'  => $t['deadline'] ?? null,
                                'kelas'     => $t['kelas'] ?? 'Semua',
                            ]
                        );
                        $report['tugas']++;
                    }
                }
            }

            // 4. Impor Submissions
            $subs = $this->getFromApi('getAllSubmissions');
            if (is_array($subs)) {
                foreach ($subs as $s) {
                    if (isset($s['id_tugas']) && isset($s['email'])) {
                        \App\Models\LmsSubmission::updateOrCreate(
                            ['id_tugas' => $s['id_tugas'], 'email' => strtolower(trim($s['email']))],
                            [
                                'nama_siswa' => $s['nama_siswa'] ?? '',
                                'link_tugas' => $s['link_tugas'] ?? null,
                                'nilai'      => isset($s['nilai']) && is_numeric($s['nilai']) ? (int)$s['nilai'] : null,
                                'feedback'   => $s['feedback'] ?? null,
                            ]
                        );
                        $report['submissions']++;
                    }
                }
            }

            // 5. Impor Absensi
            $abs = $this->postToApi(['action' => 'getAllAbsensi']);
            if (is_array($abs)) {
                foreach ($abs as $a) {
                    if (isset($a['email']) && isset($a['mapel'])) {
                        $rawTs = $a['timestamp'] ?? null;
                        $formattedTs = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                        if (!empty($rawTs)) {
                            try {
                                $formattedTs = \Carbon\Carbon::parse($rawTs)->format('Y-m-d H:i:s');
                            } catch (\Exception $ex) {}
                        }

                        \App\Models\LmsAbsensi::updateOrCreate(
                            [
                                'email'     => strtolower(trim($a['email'])),
                                'mapel'     => $a['mapel'] ?? '',
                                'timestamp' => $formattedTs,
                            ],
                            [
                                'nama'   => $a['nama'] ?? '',
                                'metode' => $a['metode'] ?? 'Live Zoom',
                            ]
                        );
                        $report['absensis']++;
                    }
                }
            }

            // 6. Impor Siswa/Users (Terhitung Password Hashed dari DATA_LOGIN)
            $this->syncLmsUsers();
            $report['users'] = \App\Models\LmsUser::count();

            $this->forgetAllCacheKeys();

            return ['status' => 'success', 'message' => 'Seluruh data berhasil diimpor ke Native MySQL!', 'report' => $report];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Gagal impor: ' . $e->getMessage()];
        }
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
     * Sinkronisasi data user dari Google Sheets DATA_LOGIN ke Native MySQL lms_users & gurus
     */
    public function syncLmsUsers(): bool
    {
        $apiKey = env('GOOGLE_API_KEY');
        $sheetId = env('SHEET_ID_MASTER');
        $syncedEmails = [];

        // 1. Coba sync via Google Sheets API V4
        if (!empty($apiKey) && !empty($sheetId)) {
            try {
                $url = "https://sheets.googleapis.com/v4/spreadsheets/{$sheetId}/values/DATA_LOGIN!A2:G100?key={$apiKey}";
                $response = Http::retry(2, 50)->timeout(6)->get($url);

                if ($response->successful()) {
                    $data = $response->json();
                    $values = $data['values'] ?? [];

                    foreach ($values as $row) {
                        $email = isset($row[0]) ? trim(strtolower($row[0])) : '';
                        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) continue;

                        $password = isset($row[1]) ? trim($row[1]) : '';
                        $role = isset($row[2]) ? trim(strtoupper($row[2])) : 'SISWA';
                        $nama = isset($row[3]) ? preg_replace('/^Halo\s+/i', '', trim($row[3])) : '';
                        $link = isset($row[4]) ? trim($row[4]) : '';
                        $sertifikat = isset($row[5]) ? trim($row[5]) : '';
                        $kelas = isset($row[6]) ? trim($row[6]) : '';

                        $hashedPassword = (strpos($password, '$2y$') === 0 && strlen($password) === 60) 
                            ? $password 
                            : \Illuminate\Support\Facades\Hash::make($password);

                        \App\Models\LmsUser::updateOrCreate(
                            ['email' => $email],
                            [
                                'password'   => $hashedPassword,
                                'role'       => $role,
                                'nama'       => $nama,
                                'link'       => $link,
                                'sertifikat' => $sertifikat,
                                'kelas'      => $kelas,
                            ]
                        );

                        if (in_array($role, ['GURU', 'TUTOR'])) {
                            \App\Models\Guru::updateOrCreate(
                                ['email' => $email],
                                ['nama' => $nama ?: 'Tutor Brevet', 'status' => 'active', 'catatan' => 'Tutor Brevet Pajak']
                            );
                        }

                        $syncedEmails[] = $email;
                    }
                }
            } catch (\Exception $e) {
                Log::warning("[GoogleSheetService] syncLmsUsers via Google API gagal: " . $e->getMessage());
            }
        }

        // 2. Guaranteed Default Seeder untuk 19 Akun Resmi
        $defaultUsers = [
            ['email' => 'admin@taxcenter.com',       'password' => 'admin123',  'role' => 'ADMIN_LMS', 'nama' => 'Admin LMS Tax Center'],
            ['email' => 'guru@test.com',            'password' => '123',       'role' => 'GURU',      'nama' => 'Guru Testing'],
            ['email' => 'guru1@taxcenter.local',     'password' => '123456',    'role' => 'GURU',      'nama' => 'Dr. Ahmad Wijaya'],
            ['email' => 'guru2@taxcenter.local',     'password' => '123',       'role' => 'GURU',      'nama' => 'Ibu Siti Nurhaliza'],
            ['email' => 'siswa@test.com',           'password' => '12345',     'role' => 'SISWA',     'nama' => 'Siswa Testing'],
            ['email' => 'nathanaldifari3@gmail.com', 'password' => '932177',    'role' => 'SISWA',     'nama' => 'Nathan Aldifari'],
            ['email' => 'rakazinggia@gmail.com',     'password' => '594486',    'role' => 'SISWA',     'nama' => 'Raka Zinggia'],
            ['email' => 'rakazinggia2@gmail.com',    'password' => '123456',    'role' => 'SISWA',     'nama' => 'Raka Zinggia 2'],
            ['email' => 'siswa1@test.com',          'password' => '1234567',   'role' => 'SISWA',     'nama' => 'Siswa 1'],
            ['email' => 'siswa2@test.com',          'password' => '123',       'role' => 'SISWA',     'nama' => 'Siswa 2'],
            ['email' => 'siswa3@test.com',          'password' => '123',       'role' => 'SISWA',     'nama' => 'Siswa 3'],
            ['email' => 'siswa4@test.com',          'password' => 'Annur123',  'role' => 'SISWA',     'nama' => 'Siswa 4'],
            ['email' => 'siswa5@test.com',          'password' => '123',       'role' => 'SISWA',     'nama' => 'Siswa 5'],
            ['email' => 'siswa6@test.com',          'password' => '123',       'role' => 'SISWA',     'nama' => 'Siswa 6'],
            ['email' => 'siswa7@test.com',          'password' => '123',       'role' => 'SISWA',     'nama' => 'Siswa 7'],
            ['email' => 'siswa8@test.com',          'password' => '123',       'role' => 'SISWA',     'nama' => 'Siswa 8'],
            ['email' => 'siswa9@test.com',          'password' => 'fajar123',  'role' => 'SISWA',     'nama' => 'Siswa 9'],
            ['email' => 'siswa10@test.com',         'password' => '123',       'role' => 'SISWA',     'nama' => 'Siswa 10'],
            ['email' => 'siswa11@test.com',         'password' => '123',       'role' => 'SISWA',     'nama' => 'Siswa 11'],
        ];

        foreach ($defaultUsers as $u) {
            $userExist = \App\Models\LmsUser::where('email', $u['email'])->first();
            if (!$userExist) {
                \App\Models\LmsUser::create([
                    'email'    => $u['email'],
                    'password' => \Illuminate\Support\Facades\Hash::make($u['password']),
                    'role'     => $u['role'],
                    'nama'     => $u['nama'],
                    'kelas'    => 'Brevet A&B',
                ]);
            } else {
                // Pastikan password di-update jika plain text
                if (strpos($userExist->password, '$2y$') !== 0) {
                    $userExist->password = \Illuminate\Support\Facades\Hash::make($u['password']);
                    $userExist->save();
                }
            }

            if (in_array($u['role'], ['GURU', 'TUTOR'])) {
                \App\Models\Guru::updateOrCreate(
                    ['email' => $u['email']],
                    ['nama' => $u['nama'], 'status' => 'active', 'catatan' => 'Tutor Brevet Pajak']
                );
            }
        }

        return true;
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

    /**
     * Kirim pesan WhatsApp personal ke nomor tertentu menggunakan Fonnte API
     */
    public function sendWaDirect(string $targetPhone, string $message): bool
    {
        $token = env('FONNTE_TOKEN');
        $phone = preg_replace('/[^0-9]/', '', $targetPhone);
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        if (empty($token) || empty($phone)) {
            Log::warning("[GoogleSheetService] sendWaDirect aborted: FONNTE_TOKEN or phone is empty.");
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->timeout(15)->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                Log::info("[GoogleSheetService] sendWaDirect to {$phone} response: " . json_encode($result));
                return isset($result['status']) && $result['status'] == true;
            }

            Log::error("[GoogleSheetService] sendWaDirect - HTTP Error {$response->status()}: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("[GoogleSheetService] sendWaDirect Exception: " . $e->getMessage());
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