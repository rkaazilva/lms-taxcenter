<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JadwalController extends Controller
{
    public function index()
    {
        $url = env('API_GOOGLE_SHEET');
        $semua_jadwal = [];
        return view('siswa.dashboard');

        try {
            $response = Http::timeout(15)->get($url, [
                'action' => 'getJadwal'
            ]);

            // Tangkap raw response dulu sebelum di-parse
            $raw = $response->body();
            $data = $response->json();

            // --- DEBUG MODE ---
            // Aktifkan baris ini kalau data tiba-tiba kosong:
            // dd(['raw' => $raw, 'parsed' => $data, 'type' => gettype($data)]);
// Di JadwalController::index(), setelah $data = $response->json();
            dd([
                'status_http' => $response->status(),
                'raw_body' => $response->body(),
                'parsed' => $data,
                'tipe' => gettype($data),
                'jumlah_baris' => is_array($data) ? count($data) : 'bukan array'
            ]);
            // Apps Script sekarang return array of objects {materi, dosen, jam, link}
            // Kita normalisasi ke format konsisten
            if (is_array($data) && count($data) > 0) {
                $semua_jadwal = array_map(function ($item) {
                    // Handle DUA kemungkinan format sekaligus:
                    // Format 1: Object dari getJadwal baru → {materi, dosen, jam, link}
                    // Format 2: Array mentah dari Sheets → [0=>tanggal, 1=>materi, ...]
                    if (isset($item['materi'])) {
                        // Format object (Apps Script yang sudah diperbaiki)
                        return [
                            'tanggal' => $item['tanggal'] ?? '-',
                            'materi' => $item['materi'] ?? '-',
                            'jam' => $item['jam'] ?? '-',
                            'dosen' => $item['dosen'] ?? '-',
                            'link' => $item['link'] ?? '',
                        ];
                    } else {
                        // Format array mentah (fallback)
                        return [
                            'tanggal' => $item[0] ?? '-',
                            'materi' => $item[1] ?? '-',
                            'jam' => $item[2] ?? '-',
                            'dosen' => $item[3] ?? '-',
                            'link' => $item[4] ?? '',
                        ];
                    }
                }, $data);

                // Data terbaru di atas (array_reverse aman karena
                // kita sudah normalisasi ke key nama, bukan index angka)
                $semua_jadwal = array_reverse($semua_jadwal);
            }

        } catch (\Exception $e) {
            Log::error('JadwalController error: ' . $e->getMessage());
            // Kirim pesan error ke view, bukan die langsung
            $error_msg = $e->getMessage();
            return view('siswa.dashboard', compact('semua_jadwal'))
                ->with('error_koneksi', $error_msg);
        }

        return view('siswa.dashboard', compact('semua_jadwal'));
    }
}