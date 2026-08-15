@extends('layouts.main')

@section('title', 'Manajemen Guru - Admin LMS')
@section('page_title', 'Manajemen Akun Guru & Dosen')

@section('content')

<!-- Notifikasi Success/Error -->
@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-2xl shadow-sm mb-6 flex items-center gap-3">
    <div class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center flex-shrink-0">
        <i class="fas fa-check"></i>
    </div>
    <div>
        <p class="font-bold text-sm">Sukses!</p>
        <p class="text-xs">{{ session('success') }}</p>
    </div>
</div>
@endif

@if(session('error'))
<div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-2xl shadow-sm mb-6 flex items-center gap-3">
    <div class="w-8 h-8 rounded-full bg-red-500 text-white flex items-center justify-center flex-shrink-0">
        <i class="fas fa-exclamation-triangle"></i>
    </div>
    <div>
        <p class="font-bold text-sm">Gagal!</p>
        <p class="text-xs">{{ session('error') }}</p>
    </div>
</div>
@endif

<!-- Header dengan Tombol Tambah -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Kelola Akun Guru & Dosen</h2>
        <p class="text-sm text-gray-500 mt-1">Tambah, edit, hapus, atau nonaktifkan akun guru langsung dari database</p>
    </div>
    <a href="{{ route('admin-lms.guru.create') }}" class="inline-flex items-center gap-2 bg-violet-600 hover:bg-violet-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition">
        <i class="fas fa-plus"></i> Tambah Guru Baru
    </a>
</div>

<!-- Tabel Daftar Guru -->
<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-4 text-left font-bold text-gray-700">Email</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-700">Nama Guru</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-700">Mata Pelajaran</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-700">Status</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-700">Catatan</th>
                    <th class="px-6 py-4 text-center font-bold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gurus as $guru)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <!-- Email -->
                    <td class="px-6 py-4">
                        <code class="text-xs bg-gray-100 px-2 py-1 rounded border border-gray-200">{{ $guru->email }}</code>
                    </td>

                    <!-- Nama -->
                    <td class="px-6 py-4">
                        <p class="font-bold text-gray-800">{{ $guru->nama }}</p>
                    </td>

                    <!-- Mapel -->
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-2">
                            @foreach($guru->mapel as $m)
                            <span class="inline-flex items-center gap-1 text-[10px] bg-violet-50 text-violet-700 px-2.5 py-1 rounded-lg border border-violet-200 font-bold">
                                <i class="fas fa-book"></i> {{ Str::limit($m, 30) }}
                            </span>
                            @endforeach
                        </div>
                    </td>

                    <!-- Status -->
                    <td class="px-6 py-4">
                        @if($guru->status === 'active')
                        <span class="inline-flex items-center gap-1 text-xs bg-green-50 text-green-700 px-2.5 py-1 rounded-lg border border-green-200 font-bold">
                            <i class="fas fa-circle-check"></i> Aktif
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 text-xs bg-gray-50 text-gray-600 px-2.5 py-1 rounded-lg border border-gray-200 font-bold">
                            <i class="fas fa-circle-xmark"></i> Nonaktif
                        </span>
                        @endif
                    </td>

                    <!-- Catatan -->
                    <td class="px-6 py-4 text-xs text-gray-600">
                        {{ $guru->catatan ? Str::limit($guru->catatan, 40) : '-' }}
                    </td>

                    <!-- Aksi -->
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            <!-- Edit Button -->
                            <a href="{{ route('admin-lms.guru.edit', $guru) }}" class="inline-flex items-center gap-1 text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-2 rounded-lg border border-blue-200 font-bold transition">
                                <i class="fas fa-edit"></i> Edit
                            </a>

                            <!-- Toggle Status Button -->
                            <form action="{{ route('admin-lms.guru.toggle_status', $guru) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1 text-xs @if($guru->status === 'active') bg-yellow-50 hover:bg-yellow-100 text-yellow-700 border-yellow-200 @else bg-green-50 hover:bg-green-100 text-green-700 border-green-200 @endif px-3 py-2 rounded-lg border font-bold transition" onclick="return confirm('Yakin ingin mengubah status guru ini?')">
                                    @if($guru->status === 'active')
                                    <i class="fas fa-pause"></i> Nonaktifkan
                                    @else
                                    <i class="fas fa-play"></i> Aktifkan
                                    @endif
                                </button>
                            </form>

                            <!-- Delete Button -->
                            <form action="{{ route('admin-lms.guru.destroy', $guru) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1 text-xs bg-red-50 hover:bg-red-100 text-red-700 px-3 py-2 rounded-lg border border-red-200 font-bold transition" onclick="return confirm('Yakin akan menghapus guru ini? Aksi ini tidak dapat dibatalkan!')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                        <i class="fas fa-inbox text-3xl mb-2 block"></i>
                        <p class="text-sm">Belum ada guru yang terdaftar. <a href="{{ route('admin-lms.guru.create') }}" class="text-violet-650 hover:text-violet-800 font-bold">Tambah guru baru</a></p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if($gurus->hasPages())
<div class="mt-6">
    {{ $gurus->links('pagination::tailwind') }}
</div>
@endif

<!-- Summary Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
    <div class="bg-gradient-to-br from-violet-50 to-violet-100 p-6 rounded-2xl border border-violet-200">
        <p class="text-xs text-violet-700 font-bold uppercase">Total Guru Terdaftar</p>
        <p class="text-3xl font-bold text-violet-900 mt-2">{{ $gurus->total() }}</p>
    </div>
    <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-2xl border border-green-200">
        <p class="text-xs text-green-700 font-bold uppercase">Guru Aktif</p>
        <p class="text-3xl font-bold text-green-900 mt-2">{{ count($gurus->where('status', 'active')) }}</p>
    </div>
    <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-6 rounded-2xl border border-gray-200">
        <p class="text-xs text-gray-700 font-bold uppercase">Guru Nonaktif</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ count($gurus->where('status', 'inactive')) }}</p>
    </div>
</div>

@endsection
