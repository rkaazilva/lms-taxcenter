@extends('layouts.main')

@section('title', 'Manajemen Siswa - Admin LMS')
@section('page_title', 'Kelola & Pendaftaran Akun Siswa')

@section('content')

<!-- Notifikasi Success/Error -->
@if(session('success'))
<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl shadow-sm mb-6 flex items-center gap-3">
    <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center flex-shrink-0">
        <i class="fas fa-check"></i>
    </div>
    <div class="flex-1">
        <p class="font-bold text-sm">Berhasil!</p>
        <p class="text-xs">{!! session('success') !!}</p>
    </div>
</div>
@endif

@if(session('error'))
<div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl shadow-sm mb-6 flex items-center gap-3">
    <div class="w-8 h-8 rounded-full bg-rose-500 text-white flex items-center justify-center flex-shrink-0">
        <i class="fas fa-exclamation-triangle"></i>
    </div>
    <div>
        <p class="font-bold text-sm">Gagal!</p>
        <p class="text-xs">{{ session('error') }}</p>
    </div>
</div>
@endif

<!-- Header & Action Buttons -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Kelola Akun & Pendaftaran Siswa 👨‍🎓</h2>
        <p class="text-xs text-gray-500 mt-1">Daftarkan akun siswa baru. Kredensial login (Email & Password) otomatis dikirimkan via Email & WA.</p>
    </div>
    <div class="flex flex-wrap gap-2 w-full md:w-auto">
        <button onclick="exportSiswaToExcel()" class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-md transition text-xs">
            <i class="fas fa-file-excel"></i> Export Excel (.xlsx)
        </button>
        <a href="{{ route('admin-lms.siswa.create') }}" class="inline-flex items-center justify-center gap-2 bg-violet-600 hover:bg-violet-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-md transition text-xs">
            <i class="fas fa-user-plus"></i> Tambah Siswa Baru
        </a>
    </div>
</div>

<!-- Search & Filter -->
<div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6">
    <form action="{{ route('admin-lms.siswa.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-3.5 top-3 text-gray-400 text-xs"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, kelas, atau no WA..." class="w-full bg-slate-50 border border-gray-200 rounded-xl pl-9 pr-4 py-2.5 text-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
        </div>
        <button type="submit" class="bg-violet-600 hover:bg-violet-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs transition">
            Cari Siswa
        </button>
        @if(request('search'))
        <a href="{{ route('admin-lms.siswa.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2.5 rounded-xl font-bold text-xs transition flex items-center justify-center">
            Reset
        </a>
        @endif
    </form>
</div>

<!-- Tabel Daftar Siswa -->
<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-xs text-left">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-bold uppercase text-[10px] tracking-wider">
                    <th class="px-6 py-4">No</th>
                    <th class="px-6 py-4">Nama Siswa</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Kelas / Angkatan</th>
                    <th class="px-6 py-4">No. WhatsApp</th>
                    <th class="px-6 py-4">Tanggal Daftar</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @forelse($siswas as $idx => $s)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4 font-bold text-gray-400">
                        {{ $siswas->firstItem() + $idx }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-violet-100 text-violet-700 font-bold flex items-center justify-center text-xs">
                                {{ strtoupper(substr($s->nama ?? 'S', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">{{ $s->nama ?? 'Siswa' }}</p>
                                <span class="inline-block bg-blue-50 text-blue-600 text-[9px] font-bold px-2 py-0.5 rounded-full">SISWA</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-600">
                        {{ $s->email }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-block bg-violet-50 text-violet-700 text-[10px] font-bold px-2.5 py-1 rounded-lg">
                            {{ $s->kelas ?? 'Brevet Gelombang 1' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-600">
                        @if(!empty($s->telepon))
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $s->telepon) }}" target="_blank" class="text-emerald-600 hover:text-emerald-800 font-bold inline-flex items-center gap-1">
                                <i class="fab fa-whatsapp text-sm"></i> {{ $s->telepon }}
                            </a>
                        @else
                            <span class="text-gray-400 text-[10px] italic">Tidak diisi</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-500 text-[11px]">
                        {{ $s->created_at ? $s->created_at->format('d M Y') : '-' }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <!-- Reset Password Form -->
                            <form action="{{ route('admin-lms.siswa.reset_password', $s->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mereset password siswa {{ $s->nama }}? Password baru akan dibuat dan dikirimkan via Email & WA.')">
                                @csrf
                                <button type="submit" title="Reset Password" class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white flex items-center justify-center transition">
                                    <i class="fas fa-key text-xs"></i>
                                </button>
                            </form>

                            <!-- Delete Form -->
                            <form action="{{ route('admin-lms.siswa.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus permanen akun siswa {{ $s->nama }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus Akun Siswa" class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white flex items-center justify-center transition">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                        <i class="fas fa-user-graduate text-3xl mb-3 text-gray-300"></i>
                        <p class="font-medium text-xs">Belum ada akun siswa yang terdaftar.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if($siswas->hasPages())
<div class="mt-6">
    {{ $siswas->links('pagination::tailwind') }}
</div>
@endif

<!-- Summary Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-8">
    <div class="bg-gradient-to-br from-violet-50 to-violet-100 p-6 rounded-2xl border border-violet-200">
        <p class="text-xs text-violet-700 font-bold uppercase">Total Siswa Terdaftar</p>
        <p class="text-3xl font-bold text-violet-900 mt-2">{{ $totalSiswa }}</p>
    </div>
    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-2xl border border-blue-200">
        <p class="text-xs text-blue-700 font-bold uppercase">Total Kelas / Angkatan</p>
        <p class="text-3xl font-bold text-blue-900 mt-2">{{ $kelasCounts }}</p>
    </div>
</div>

<script>
function exportSiswaToExcel() {
    const table = document.querySelector('table');
    if (!table) return;

    const dataRows = [
        ["No", "Nama Siswa", "Email Siswa", "Kelas", "Nomor WhatsApp", "Tanggal Daftar"]
    ];

    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(tr => {
        const tds = tr.querySelectorAll('td');
        if (tds.length >= 6) {
            dataRows.push([
                tds[0].innerText.trim(),
                tds[1].innerText.replace(/\n/g, ' ').trim(),
                tds[2].innerText.trim(),
                tds[3].innerText.trim(),
                tds[4].innerText.trim(),
                tds[5].innerText.trim()
            ]);
        }
    });

    const dateStr = new Date().toISOString().slice(0,10);

    if (typeof XLSX !== 'undefined') {
        const ws = XLSX.utils.aoa_to_sheet(dataRows);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Daftar Siswa");
        XLSX.writeFile(wb, `Daftar_Siswa_Brevet_${dateStr}.xlsx`);
    } else {
        alert('Library Excel belum siap.');
    }
}
</script>
@endsection
