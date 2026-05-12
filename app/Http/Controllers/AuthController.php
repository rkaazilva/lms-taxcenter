<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Models\Article;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function index()
    {
        if (session()->has('email')) {
            return $this->redirectUser(session('role'));
        }
        $articles = Article::published()->latest('published_at')->take(3)->get();
        return view('welcome', compact('articles'));
    }

    /**
     * Proses Login
     */
    public function login(Request $request)
    {
        // 1. Ambil URL dari .env
        $url = env('API_GOOGLE_SHEET');

        // Validasi input dulu biar gak kosong
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            // 2. Tembak API Google Apps Script
            $response = Http::timeout(15)->get($url, [
                'action' => 'login',
                'email'  => trim($request->email),
                'pass'   => trim($request->password) 
            ]);

            $result = $response->json();

            // 3. Cek Respon Sukses dari Google
            if (isset($result['status']) && $result['status'] == 'success') {
                
                // Hapus session lama biar gak bentrok
                $request->session()->forget(['isLoggedIn', 'email', 'role', 'nama']);

                // SIMPAN DATA KE SESSION BARU
                session([
                    'isLoggedIn' => true,
                    'email'      => trim($request->email),
                    'role'       => strtoupper($result['role']), 
                    'nama'       => $result['message'],
                    'link'       => $result['url'] ?? ''
                ]);

                // PAKSA SIMPAN KE DISK (Ini kunci biar gak mental)
                session()->save(); 

                // 4. Redirect ke Dashboard
                return $this->redirectUser(strtoupper($result['role']));
            }

            // Kalau gagal dari API (Password/Email Salah)
            return back()
                ->withInput($request->only('email'))
                ->with('error', $result['message'] ?? 'Email atau Password salah!');

        } catch (\Exception $e) {
            // Kalau koneksi/server Google bermasalah
            return back()->with('error', 'Koneksi ke server pusat bermasalah. Cek internet lu atau URL API.');
        }
    }

    /**
     * Helper untuk handle arah redirect biar rapih
     */
    private function redirectUser($role)
    {
        if ($role == 'SISWA') {
            return redirect()->route('siswa.dashboard');
        } elseif ($role == 'TUTOR' || $role == 'ADMIN' || $role == 'GURU') {
            return redirect()->route('guru.dashboard');
        }

        return redirect()->route('login')->with('error', 'Role user tidak dikenali!');
    }

    /**
     * Proses Logout
     */
    public function logout()
    {
        session()->flush();
        return redirect()->route('login')->with('success', 'Berhasil keluar!');
    }
}