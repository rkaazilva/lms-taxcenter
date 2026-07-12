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
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = trim(strtolower($request->email));
        $password = trim($request->password);

        try {
            // Cek di database lokal terlebih dahulu
            $user = \App\Models\LmsUser::findByEmail($email);

            // Cek apakah password benar (aman terhadap plain text transisi)
            $isCorrect = false;
            if ($user) {
                if (strpos($user->password, '$2y$') === 0 && strlen($user->password) === 60) {
                    $isCorrect = \Illuminate\Support\Facades\Hash::check($password, $user->password);
                } else {
                    $isCorrect = ($user->password === $password);
                    if ($isCorrect) {
                        // Upgrade ke bcrypt di SQLite lokal
                        $user->password = \Illuminate\Support\Facades\Hash::make($password);
                        $user->save();
                    }
                }
            }

            // Jika tidak ditemukan secara lokal atau password salah, lakukan sinkronisasi on-demand dari Google Sheets
            if (!$user || !$isCorrect) {
                $gs = new \App\Services\GoogleSheetService();
                $gs->syncLmsUsers();
                
                // Cek ulang database lokal setelah sinkronisasi
                $user = \App\Models\LmsUser::findByEmail($email);
                
                if ($user) {
                    if (strpos($user->password, '$2y$') === 0 && strlen($user->password) === 60) {
                        $isCorrect = \Illuminate\Support\Facades\Hash::check($password, $user->password);
                    } else {
                        $isCorrect = ($user->password === $password);
                        if ($isCorrect) {
                            $user->password = \Illuminate\Support\Facades\Hash::make($password);
                            $user->save();
                        }
                    }
                }
            }

            // 2. Verifikasi final credentials
            if ($user && $isCorrect) {
                $role = strtoupper($user->role);
                $nama = $user->nama;

                // Validasi akun Guru/Tutor di SQLite lokal
                if (in_array($role, ['TUTOR', 'GURU'])) {
                    $guru = \App\Models\Guru::findByEmail($email);
                    if (!$guru) {
                        return back()
                            ->withInput($request->only('email'))
                            ->with('error', 'Akses ditolak. Akun guru Anda belum terdaftar di sistem lokal.');
                    }
                    if ($guru->status !== 'active') {
                        return back()
                            ->withInput($request->only('email'))
                            ->with('error', 'Akses ditolak. Akun guru Anda dinonaktifkan oleh Administrator.');
                    }
                    $nama = $guru->nama;
                }
                
                // Hapus session lama biar gak bentrok
                $request->session()->forget(['isLoggedIn', 'email', 'role', 'nama', 'link', 'sertifikat', 'kelas']);

                // SIMPAN DATA KE SESSION BARU
                session([
                    'isLoggedIn' => true,
                    'email'      => $email,
                    'role'       => $role, 
                    'nama'       => $nama,
                    'link'       => $user->link ?? '',
                    'sertifikat' => $user->sertifikat ?? '',
                    'kelas'      => $user->kelas ?? ''
                ]);

                // PAKSA SIMPAN KE SESSION
                session()->save(); 

                // Redirect ke Dashboard yang sesuai
                return $this->redirectUser($role);
            }

            // Kalau gagal (Email/Password salah)
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Email atau Password salah!');

        } catch (\Exception $e) {
            \Log::error('Login connection error: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Terjadi kesalahan sistem saat memproses login: ' . $e->getMessage());
        }
    }

    /**
     * Helper untuk handle arah redirect biar rapih
     */
    private function redirectUser($role)
    {
        if ($role == 'SISWA') {
            return redirect()->route('siswa.dashboard');
        } elseif (in_array($role, ['ADMIN_LMS', 'ADMIN'])) {
            return redirect()->route('admin-lms.index');
        } elseif ($role == 'TUTOR' || $role == 'GURU') {
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