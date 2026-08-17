<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    // Tampilkan form login
    public function showLoginForm()
    {
        // Kalau sudah login sebagai admin, langsung redirect
        if (Auth::check()) {
            return redirect()->route('admin.articles.index');
        }
        return view('admin.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.articles.index'))
                             ->with('success', 'Selamat datang kembali, Admin!');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();

        // Regenerate session supaya aman, tapi jangan menghapus data session Google Sheets jika ada
        // Untuk amannya di Laravel 11+, invalidate session:
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Berhasil keluar dari Admin Panel.');
    }
}
