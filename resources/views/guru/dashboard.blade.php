@extends('layouts.main')

@section('title', 'Dashboard Tutor')
@section('page_title', 'Panel Kerja Tutor')

@section('content')
<div class="grid md:grid-cols-4 gap-6">
    <!-- Stat Card 1 -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border-t-4 border-uin-blue">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-50 text-uin-blue rounded-2xl flex items-center justify-center">
                <i class="fas fa-users text-xl"></i>
            </div>
            <span class="text-xs font-bold text-green-500 bg-green-50 px-2 py-1 rounded-lg">+12%</span>
        </div>
        <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Siswa</h3>
        <p class="text-3xl font-bold text-gray-800 mt-1">128</p>
    </div>

    <!-- Stat Card 2 -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border-t-4 border-uin-gold">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-yellow-50 text-uin-gold rounded-2xl flex items-center justify-center">
                <i class="fas fa-book-reader text-xl"></i>
            </div>
        </div>
        <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">Materi Aktif</h3>
        <p class="text-3xl font-bold text-gray-800 mt-1">14</p>
    </div>
</div>

<!-- Main Section -->
<div class="mt-8 grid md:grid-cols-3 gap-8">
    <div class="md:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-sm">
        <div class="flex justify-between items-center mb-8">
            <h3 class="font-bold text-xl text-gray-800">Manajemen Materi</h3>
            <button class="bg-uin-blue text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-blue-900 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah Materi
            </button>
        </div>
        
        <!-- Table Placeholder -->
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-400 text-xs uppercase tracking-widest border-b">
                        <th class="pb-4 font-bold">Judul Materi</th>
                        <th class="pb-4 font-bold">Kategori</th>
                        <th class="pb-4 font-bold">Status</th>
                        <th class="pb-4 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <tr class="border-b last:border-0">
                        <td class="py-5 font-bold text-gray-700 text-sm">Dasar-Dasar Pajak Penghasilan</td>
                        <td class="py-5 text-gray-500">Pajak Pusat</td>
                        <td class="py-5"><span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-[10px] font-bold uppercase">Published</span></td>
                        <td class="py-5 text-right flex justify-end gap-2">
                            <button class="w-8 h-8 rounded-lg bg-gray-100 text-gray-400 hover:bg-blue-100 hover:text-uin-blue transition"><i class="fas fa-edit"></i></button>
                            <button class="w-8 h-8 rounded-lg bg-gray-100 text-gray-400 hover:bg-red-100 hover:text-red-500 transition"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="space-y-6">
        <div class="bg-gradient-to-br from-indigo-900 to-blue-800 p-8 rounded-[2.5rem] text-white shadow-xl relative overflow-hidden">
            <i class="fas fa-quote-right absolute top-4 right-4 text-white/10 text-6xl"></i>
            <h3 class="font-bold text-lg mb-2 relative z-10">Info Tutor</h3>
            <p class="text-blue-200 text-xs leading-relaxed relative z-10">Gunakan panel ini untuk memonitor perkembangan belajar mahasiswa di Tax Center UIN SGD.</p>
        </div>
    </div>
</div>
@endsection