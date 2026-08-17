@extends('layouts.main')

@section('title', 'Pendaftaran Siswa Baru - Admin LMS')
@section('page_title', 'Form Pendaftaran Akun Siswa Baru')

@section('content')

<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftarkan Siswa Baru 🎓</h2>
            <p class="text-xs text-gray-500 mt-1">Sistem akan secara otomatis membuat password acak dan mengirimmkannya via Email & WhatsApp.</p>
        </div>
        <a href="{{ route('admin-lms.siswa.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-xl transition text-xs font-bold flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Alert Errors -->
    @if($errors->any())
    <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl shadow-sm flex items-start gap-3">
        <i class="fas fa-exclamation-circle text-rose-500 mt-0.5"></i>
        <div>
            <p class="font-bold text-xs">Periksa kembali data masukan Anda:</p>
            <ul class="text-xs text-rose-700 list-disc list-inside mt-1 space-y-0.5">
                @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- Form Pendaftaran -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">
        <form action="{{ route('admin-lms.siswa.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Nama Lengkap -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Nama Lengkap Siswa <span class="text-rose-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: Nathan Aldifari" class="w-full rounded-2xl border-gray-200 p-3.5 border text-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
            </div>

            <!-- Email Siswa -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Email Siswa (Untuk Login) <span class="text-rose-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="Contoh: nathan@gmail.com" class="w-full rounded-2xl border-gray-200 p-3.5 border text-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                <p class="text-[10px] text-gray-400 mt-1">Gunakan email aktif siswa yang valid.</p>
            </div>

            <!-- Kelas / Angkatan -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Kelas / Angkatan</label>
                <input type="text" name="kelas" value="{{ old('kelas', 'Brevet Gelombang 1') }}" placeholder="Contoh: Brevet Gelombang 1" class="w-full rounded-2xl border-gray-200 p-3.5 border text-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
            </div>

            <!-- Nomor WhatsApp -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-2">Nomor WhatsApp (Opsional)</label>
                <input type="text" name="telepon" value="{{ old('telepon') }}" placeholder="Contoh: 081234567890" class="w-full rounded-2xl border-gray-200 p-3.5 border text-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                <p class="text-[10px] text-gray-400 mt-1">Jika diisi, pesan pengantar & password login akan otomatis terkirim ke WhatsApp siswa.</p>
            </div>

            <!-- Info Kredensial Otomatis -->
            <div class="bg-violet-50/60 border border-violet-100 p-4 rounded-2xl text-violet-900 text-xs flex items-start gap-3">
                <i class="fas fa-magic text-violet-600 text-base mt-0.5"></i>
                <div class="space-y-1">
                    <p class="font-bold">Generasi Password Otomatis & Notifikasi</p>
                    <p class="text-[11px] text-violet-800 leading-relaxed">
                        Sistem akan membuatkan password acak yang aman (contoh: <code class="bg-violet-100 px-1.5 py-0.5 rounded font-mono text-[10px]">Tax8491!</code>). Pengantar akun & kredensial login langsung dikirimkan ke email dan nomor WhatsApp siswa.
                    </p>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('admin-lms.siswa.index') }}" class="px-5 py-3 rounded-2xl text-xs font-bold text-gray-600 hover:bg-gray-100 transition">
                    Batal
                </a>
                <button type="submit" class="bg-violet-600 hover:bg-violet-700 text-white font-bold px-6 py-3 rounded-2xl text-xs shadow-md transition flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    <span>Simpan & Kirim Akun</span>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
