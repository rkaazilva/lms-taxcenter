<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index()
{
    // Cek session
    if (!session()->has('email') || session('role') !== 'TUTOR') {
        return redirect()->route('login')->with('error', 'Akses khusus Tutor.');
    }

    return view('guru.dashboard'); // <--- Ini harus manggil file yang di atas
}
}