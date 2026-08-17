@extends('layouts.main')

@section('title', 'Manajemen Tugas')
@section('page_title', 'Manajemen Tugas Pembelajaran')

@section('content')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.4);
    }
    .hover-scale {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .hover-scale:hover {
        transform: translateY(-2px);
    }
</style>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="glass-card rounded-3xl p-6 mb-8 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold text-amber-600 uppercase tracking-widest">Task Management</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Manajemen Tugas Pembelajaran 📝</h2>
            <p class="text-gray-500 text-xs mt-1">Pantau rincian tugas pelatihan yang dibuat oleh tutor/dosen beserta batas waktu deadline.</p>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            <button onclick="exportTugasToExcel()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl transition text-xs font-bold flex items-center justify-center gap-2">
                <i class="fas fa-file-excel"></i> Export Excel (.xlsx)
            </button>
            <a href="{{ route('admin-lms.index') }}" class="w-full sm:w-auto text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-xl transition text-xs font-bold flex items-center justify-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Statistik Ringkas Tugas -->
    @if(!empty($tugas) && count($tugas) > 0)
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="glass-card p-5 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Total Tugas</p>
                <p class="text-2xl font-extrabold text-violet-950 mt-0.5">{{ count($tugas) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-violet-50 text-violet-700 flex items-center justify-center">
                <i class="fas fa-clipboard-list text-sm"></i>
            </div>
        </div>
        
        <div class="glass-card p-5 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Memiliki Deadline</p>
                <p class="text-2xl font-extrabold text-emerald-950 mt-0.5">
                    {{ count(array_filter($tugas, fn($t) => !empty($t['deadline']))) }}
                </p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                <i class="far fa-clock text-sm"></i>
            </div>
        </div>

        <div class="glass-card p-5 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Dengan Berkas Soal</p>
                <p class="text-2xl font-extrabold text-amber-950 mt-0.5">
                    {{ count(array_filter($tugas, fn($t) => !empty($t['link_soal']))) }}
                </p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                <i class="fas fa-file-signature text-sm"></i>
            </div>
        </div>
    </div>
    @endif

    <!-- Daftar Tugas -->
    <div class="glass-card rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        @if(!empty($tugas) && count($tugas) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100/80">
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Kode Tugas</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Judul & Deskripsi Tugas</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Batas Waktu (Deadline)</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Berkas Soal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100/60">
                        @foreach($tugas as $t)
                        <tr class="hover:bg-gray-50/40 transition">
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 rounded-lg bg-gray-100 text-gray-800 text-[10px] font-mono font-bold border border-gray-200">
                                    {{ $t['id_tugas'] ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 text-[9px] font-bold border border-amber-100">
                                    {{ $t['mapel'] ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-xs font-bold text-gray-800 leading-tight">{{ $t['judul'] ?? '-' }}</p>
                                @if(!empty($t['deskripsi']))
                                <p class="text-[10px] text-gray-400 mt-1 leading-normal font-medium max-w-md line-clamp-2">{{ $t['deskripsi'] }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if(!empty($t['deadline']))
                                    <p class="text-xs font-semibold text-rose-600 bg-rose-50 border border-rose-100 px-2.5 py-1 rounded-xl inline-block text-[10px] font-bold"><i class="far fa-calendar-times mr-1"></i>{{ $t['deadline'] }}</p>
                                @else
                                    <span class="text-[10px] text-gray-400 font-medium"><i class="fas fa-infinity mr-1"></i>Tanpa batas waktu</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if(!empty($t['link_soal']))
                                    <a href="{{ $t['link_soal'] }}" target="_blank" class="inline-flex items-center gap-1.5 text-[10px] bg-violet-50 text-violet-650 border border-violet-100 px-3 py-1 rounded-xl font-bold hover:bg-violet-100 hover:text-violet-700 transition">
                                        <i class="fas fa-external-link-alt text-[9px]"></i> Buka Soal
                                    </a>
                                @else
                                    <span class="text-[10px] text-gray-400 font-medium"><i class="fas fa-slash text-[8px] mr-1"></i>Tidak ada</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-gray-50 text-gray-300 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clipboard-list-check text-2xl"></i>
                </div>
                <p class="text-gray-500 text-xs md:text-sm font-medium">Belum ada tugas pelatihan terdata.</p>
                <p class="text-gray-400 text-[10px] mt-1">Tugas dibuat dan dikelola oleh guru/tutor secara langsung melalui Panel Guru.</p>
            </div>
        @endif
    </div>
</div>

<script>
function exportTugasToExcel() {
    const table = document.querySelector('table');
    if (!table) return;

    const dataRows = [
        ["ID Tugas", "Mata Pelajaran", "Judul Tugas", "Deskripsi", "Batas Waktu", "Jumlah Pengumpulan"]
    ];

    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(tr => {
        const tds = tr.querySelectorAll('td');
        if (tds.length >= 6) {
            dataRows.push([
                tds[0].innerText.trim(),
                tds[1].innerText.trim(),
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
        XLSX.utils.book_append_sheet(wb, ws, "Daftar Tugas");
        XLSX.writeFile(wb, `Data_Tugas_Pelatihan_${dateStr}.xlsx`);
    } else {
        alert('Library Excel belum siap.');
    }
}
</script>
@endsection
