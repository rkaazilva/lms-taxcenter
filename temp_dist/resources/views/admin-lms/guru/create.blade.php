@extends('layouts.main')

@section('title', 'Tambah Guru - Admin LMS')
@section('page_title', 'Tambah Akun Guru Baru')

@section('content')

<!-- Back Link -->
<a href="{{ route('admin-lms.guru.index') }}" class="inline-flex items-center gap-2 text-violet-650 hover:text-violet-800 font-bold mb-6">
    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Guru
</a>

<!-- Form Card -->
<div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 max-w-2xl">
    <h2 class="text-2xl font-bold text-gray-800 mb-1">Tambah Guru Baru</h2>
    <p class="text-sm text-gray-500 mb-6">Isi form di bawah untuk menambahkan akun guru ke database</p>

    <form action="{{ route('admin-lms.guru.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Email -->
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">
                <i class="fas fa-envelope mr-2"></i> Email Guru (Login)
            </label>
            <input type="email" name="email" required value="{{ old('email') }}" 
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-violet-500 focus:ring-2 focus:ring-violet-200 transition @error('email') border-red-500 @enderror"
                placeholder="guru@taxcenter.local">
            @error('email')
            <p class="text-xs text-red-600 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
            @enderror
        </div>

        <!-- Nama -->
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">
                <i class="fas fa-user mr-2"></i> Nama Lengkap Guru
            </label>
            <input type="text" name="nama" required value="{{ old('nama') }}" 
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-violet-500 focus:ring-2 focus:ring-violet-200 transition @error('nama') border-red-500 @enderror"
                placeholder="Contoh: Dr. Ahmad Wijaya">
            @error('nama')
            <p class="text-xs text-red-600 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
            @enderror
        </div>

        <!-- Mata Pelajaran -->
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-3">
                <i class="fas fa-book mr-2"></i> Mata Pelajaran yang Diajar
            </label>
            <div class="grid grid-cols-1 gap-3 max-h-96 overflow-y-auto p-3 border-2 border-gray-200 rounded-xl">
                @foreach($mapelList as $mapel)
                <label class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg cursor-pointer transition">
                    <input type="checkbox" name="mapel[]" value="{{ $mapel }}" 
                        @if(in_array($mapel, old('mapel', []))) checked @endif
                        class="w-4 h-4 text-violet-650 rounded border-gray-300">
                    <span class="text-sm text-gray-700">{{ $mapel }}</span>
                </label>
                @endforeach
            </div>
            @error('mapel')
            <p class="text-xs text-red-600 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
            @enderror
        </div>

        <!-- Status -->
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">
                <i class="fas fa-toggle-on mr-2"></i> Status
            </label>
            <select name="status" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-violet-500 focus:ring-2 focus:ring-violet-200 transition">
                <option value="">-- Pilih Status --</option>
                <option value="active" @if(old('status') === 'active') selected @endif>Aktif</option>
                <option value="inactive" @if(old('status') === 'inactive') selected @endif>Nonaktif</option>
            </select>
            @error('status')
            <p class="text-xs text-red-600 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
            @enderror
        </div>

        <!-- Catatan -->
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">
                <i class="fas fa-sticky-note mr-2"></i> Catatan (Opsional)
            </label>
            <textarea name="catatan" rows="3" 
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-violet-500 focus:ring-2 focus:ring-violet-200 transition @error('catatan') border-red-500 @enderror"
                placeholder="Contoh: Dosen Tetap - Program Brevet">{{ old('catatan') }}</textarea>
            @error('catatan')
            <p class="text-xs text-red-600 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
            @enderror
        </div>

        <!-- Button Group -->
        <div class="flex gap-3 pt-4 border-t border-gray-200">
            <a href="{{ route('admin-lms.guru.index') }}" class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-bold hover:bg-gray-50 transition text-center">
                <i class="fas fa-times mr-2"></i> Batal
            </a>
            <button type="submit" class="flex-1 px-4 py-3 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-bold transition text-center">
                <i class="fas fa-save mr-2"></i> Simpan Guru Baru
            </button>
        </div>
    </form>
</div>

@endsection
