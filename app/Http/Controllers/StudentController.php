<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        // Proteksi: Kalau gak ada session email (belum login), tendang ke login
        if (!session()->has('email') || session('role') !== 'SISWA') {
            return redirect()->route('login')->with('error', 'Silahkan login terlebih dahulu.');
        }

        return view('siswa.dashboard');
    }
}