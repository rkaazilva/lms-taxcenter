@extends('layouts.main')

@section('title', 'Edit Guru - Admin LMS')
@section('page_title', 'Edit Akun Guru')

@section('content')

<!-- Back Link -->
<a href="{{ route('admin-lms.guru.index') }}" class="inline-flex items-center gap-2 text-violet-650 hover:text-violet-800 font-bold mb-6">
    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Guru
</a>

<!-- Form Card -->
<div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 max-w-2xl">
    <h2 class="text-2xl font-bold text-gray-800 mb-1">Edit Data Guru</h2>
    <p class="text-sm text-gray-500 mb-6">Update informasi akun guru: {{ $guru->email }}</p>

    <form action="{{ route('admin-lms.guru.update', $guru) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Email -->
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">
                <i class="fas fa-envelope mr-2"></i> Email Guru (Login)
            </label>
            <input type="email" name="email" required value="{{ old('email', $guru->email) }}" 
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
            <input type="text" name="nama" required value="{{ old('nama', $guru->nama) }}" 
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
                        @if(in_array($mapel, old('mapel', $guru->mapel))) checked @endif
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
                <option value="active" @if(old('status', $guru->status) === 'active') selected @endif>Aktif</option>
                <option value="inactive" @if(old('status', $guru->status) === 'inactive') selected @endif>Nonaktif</option>
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
                placeholder="Contoh: Dosen Tetap - Program Brevet">{{ old('catatan', $guru->catatan) }}</textarea>
            @error('catatan')
            <p class="text-xs text-red-600 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
            @enderror
        </div>

        <!-- Metadata -->
        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 text-xs text-gray-600 space-y-1">
            <p><span class="font-bold">Created:</span> {{ $guru->created_at->format('d M Y H:i') }}</p>
            <p><span class="font-bold">Last Updated:</span> {{ $guru->updated_at->format('d M Y H:i') }}</p>
        </div>

        <!-- Button Group -->
        <div class="flex gap-3 pt-4 border-t border-gray-200">
            <a href="{{ route('admin-lms.guru.index') }}" class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-bold hover:bg-gray-50 transition text-center">
                <i class="fas fa-times mr-2"></i> Batal
            </a>
            <button type="submit" class="flex-1 px-4 py-3 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-bold transition text-center">
                <i class="fas fa-save mr-2"></i> Simpan Perubahan
            </button>
        </div>
    </form>

    <!-- Danger Zone -->
    <div class="mt-8 pt-8 border-t border-red-200">
        <h3 class="text-sm font-bold text-red-700 mb-4">
            <i class="fas fa-exclamation-triangle mr-2"></i> Zona Berbahaya
        </h3>
        <p class="text-xs text-gray-600 mb-4">Aksi berikut tidak dapat dibatalkan. Gunakan dengan hati-hati.</p>
        <form action="{{ route('admin-lms.guru.destroy', $guru) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition" 
                onclick="return confirm('Yakin akan menghapus guru ini dan semua data terkaitnya? Aksi ini tidak dapat dibatalkan!')">
                <i class="fas fa-trash mr-2"></i> Hapus Akun Guru Ini
            </button>
        </form>
    </div>
</div>

@endsection
