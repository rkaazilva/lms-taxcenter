<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;

class AdminGuruController extends Controller
{
    /**
     * Display a listing of all teachers
     */
    public function index()
    {
        $gurus = Guru::orderBy('created_at', 'desc')->paginate(15);
        return view('admin-lms.guru.index', compact('gurus'));
    }

    /**
     * Show the form for creating a new teacher
     */
    public function create()
    {
        $mapelList = [
            'Ketentuan Umum dan Tata Cara Perpajakan (KUP) A & B',
            'Pajak Penghasilan (PPh) Orang Pribadi',
            'Pajak Pemotongan dan Pemungutan (PPh Pasal 21)',
            'Pajak Pemotongan dan Pemungutan (PPh Pasal 22, 23, 26, & 4(2))',
            'Pajak Penghasilan (PPh) Badan',
            'Pajak Pertambahan Nilai (PPN) dan PPnBM A & B',
            'Pajak Bumi dan Bangunan (PBB), BPHTB, & Bea Meterai',
            'Akuntansi Perpajakan',
            'Pemeriksaan dan Penyidikan Pajak',
            'Pengisian e-SPT / Aplikasi Perpajakan (e-Faktur, dll)',
            'Tax Planning (Perencanaan Pajak)',
            'Ujian Kelulusan / Komprehensif Brevet',
        ];
        return view('admin-lms.guru.create', compact('mapelList'));
    }

    /**
     * Store a newly created teacher
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:gurus,email',
            'nama' => 'required|string|max:255',
            'mapel' => 'required|array|min:1',
            'mapel.*' => 'string',
            'status' => 'required|in:active,inactive',
            'catatan' => 'nullable|string|max:1000',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email harus format yang valid',
            'email.unique' => 'Email sudah terdaftar',
            'nama.required' => 'Nama guru wajib diisi',
            'mapel.required' => 'Pilih minimal 1 mata pelajaran',
            'mapel.min' => 'Pilih minimal 1 mata pelajaran',
            'status.required' => 'Status wajib dipilih',
        ]);

        Guru::create($validated);

        return redirect()->route('admin-lms.guru.index')
            ->with('success', "Guru '{$validated['nama']}' berhasil ditambahkan ke database!");
    }

    /**
     * Show the form for editing a teacher
     */
    public function edit(Guru $guru)
    {
        $mapelList = [
            'Ketentuan Umum dan Tata Cara Perpajakan (KUP) A & B',
            'Pajak Penghasilan (PPh) Orang Pribadi',
            'Pajak Pemotongan dan Pemungutan (PPh Pasal 21)',
            'Pajak Pemotongan dan Pemungutan (PPh Pasal 22, 23, 26, & 4(2))',
            'Pajak Penghasilan (PPh) Badan',
            'Pajak Pertambahan Nilai (PPN) dan PPnBM A & B',
            'Pajak Bumi dan Bangunan (PBB), BPHTB, & Bea Meterai',
            'Akuntansi Perpajakan',
            'Pemeriksaan dan Penyidikan Pajak',
            'Pengisian e-SPT / Aplikasi Perpajakan (e-Faktur, dll)',
            'Tax Planning (Perencanaan Pajak)',
            'Ujian Kelulusan / Komprehensif Brevet',
        ];
        return view('admin-lms.guru.edit', compact('guru', 'mapelList'));
    }

    /**
     * Update a teacher's information
     */
    public function update(Request $request, Guru $guru)
    {
        $validated = $request->validate([
            'email' => "required|email|unique:gurus,email,{$guru->id}",
            'nama' => 'required|string|max:255',
            'mapel' => 'required|array|min:1',
            'mapel.*' => 'string',
            'status' => 'required|in:active,inactive',
            'catatan' => 'nullable|string|max:1000',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email harus format yang valid',
            'email.unique' => 'Email sudah terdaftar untuk guru lain',
            'nama.required' => 'Nama guru wajib diisi',
            'mapel.required' => 'Pilih minimal 1 mata pelajaran',
            'mapel.min' => 'Pilih minimal 1 mata pelajaran',
            'status.required' => 'Status wajib dipilih',
        ]);

        $guru->update($validated);

        return redirect()->route('admin-lms.guru.index')
            ->with('success', "Data guru '{$validated['nama']}' berhasil diperbarui!");
    }

    /**
     * Delete a teacher
     */
    public function destroy(Guru $guru)
    {
        $nama = $guru->nama;
        $guru->delete();

        return redirect()->route('admin-lms.guru.index')
            ->with('success', "Guru '{$nama}' berhasil dihapus dari database!");
    }

    /**
     * Toggle teacher status (active/inactive)
     */
    public function toggleStatus(Guru $guru)
    {
        $newStatus = $guru->status === 'active' ? 'inactive' : 'active';
        $guru->update(['status' => $newStatus]);

        $statusLabel = $newStatus === 'active' ? 'Diaktifkan' : 'Dinonaktifkan';
        return redirect()->back()->with('success', "Guru '{$guru->nama}' {$statusLabel}!");
    }
}
