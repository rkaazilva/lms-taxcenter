<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
  public function index()
{
    $url = env('API_GOOGLE_SHEET');

    try {
        // Ambil data dari Google Apps Script
        $response = Http::timeout(10)->get($url, ['action' => 'getJadwal']);
        $data = $response->json();

        if ($data && is_array($data)) {
            // MENGHILANGKAN HEADER (Jika baris pertama di sheet adalah judul kolom)
            // array_shift($data); 

            // MEMBALIK DATA: Supaya inputan terbaru di Google Sheet muncul paling atas
            $semua_jadwal = array_reverse($data);
        } else {
            $semua_jadwal = [];
        }
    } catch (\Exception $e) {
        $semua_jadwal = [];
    }

    // Kirim data ke view dashboard siswa
    return view('siswa.dashboard', compact('semua_jadwal'));
}
}