@extends('layouts.main')

@section('title', 'Manajemen Materi')
@section('page_title', 'Manajemen Materi Pembelajaran')

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
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Material Management</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Manajemen Rekaman & Materi 📚</h2>
            <p class="text-gray-500 text-xs mt-1">Kelola modul PDF yang diunggah tutor dan perbarui link rekaman video pembelajaran YouTube.</p>
        </div>
        <div>
            <a href="{{ route('admin-lms.index') }}" class="w-full sm:w-auto text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-xl transition text-xs font-bold flex items-center justify-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-500"></i>
        <p class="text-emerald-800 font-semibold text-xs">✅ {{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-100 flex items-center gap-3">
        <i class="fas fa-times-circle text-rose-500"></i>
        <p class="text-rose-800 font-semibold text-xs">❌ {{ session('error') }}</p>
    </div>
    @endif

    <!-- Daftar Materi -->
    <div class="glass-card rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        @if(!empty($materi) && count($materi) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100/80">
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Judul Bahasan / Materi</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Modul PDF</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Rekaman Video YT</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100/60">
                        @foreach($materi as $m)
                        <tr class="hover:bg-gray-50/40 transition">
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 text-[9px] font-bold border border-emerald-100">
                                    {{ $m['mapel'] ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-xs font-bold text-gray-800">{{ $m['judul'] ?? '-' }}</p>
                                @if(!empty($m['keterangan']))
                                <p class="text-[10px] text-gray-400 mt-1 leading-normal font-medium">{{ $m['keterangan'] }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if(!empty($m['link_modul']))
                                    <a href="{{ $m['link_modul'] }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] bg-rose-50 text-rose-600 border border-rose-100 px-3 py-1 rounded-xl font-bold hover:bg-rose-100 hover:text-rose-700 transition">
                                        <i class="fas fa-file-pdf"></i> Modul
                                    </a>
                                @else
                                    <span class="text-[10px] text-gray-400 font-medium"><i class="fas fa-slash text-[8px] mr-1"></i>Belum ada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if(!empty($m['link_youtube']))
                                    <a href="{{ $m['link_youtube'] }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] bg-red-50 text-red-600 border border-red-100 px-3 py-1 rounded-xl font-bold hover:bg-red-100 hover:text-red-700 transition">
                                        <i class="fab fa-youtube text-[11px]"></i> Video YT
                                    </a>
                                @else
                                    <span class="text-[10px] text-gray-400 font-medium"><i class="fas fa-video-slash text-[9px] mr-1"></i>Belum diset</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button onclick="openEditYtModal({{ json_encode($m) }})" class="text-[11px] bg-emerald-50 text-emerald-700 border border-emerald-100 hover:bg-emerald-100 px-3 py-1 rounded-lg font-bold transition">
                                    Edit Link YT
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-gray-50 text-gray-300 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-book-open text-2xl"></i>
                </div>
                <p class="text-gray-500 text-xs md:text-sm font-medium">Belum ada materi pembelajaran terdata.</p>
                <p class="text-gray-400 text-[10px] mt-1">Materi dikelola oleh guru/tutor secara langsung melalui Panel Guru.</p>
            </div>
        @endif
    </div>

    <!-- Info Box -->
    <div class="mt-6 p-4 rounded-3xl bg-blue-50/50 border border-blue-100/60 flex items-start gap-3">
        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
        <div>
            <p class="text-blue-800 text-xs font-semibold">💡 Pengaturan File Modul</p>
            <p class="text-blue-700 text-[10px] leading-relaxed mt-1">File modul PDF diunggah langsung oleh tutor/dosen saat memasukkan materi di Panel Guru. Admin hanya berwenang memantau modul dan memperbarui rekaman video YouTube hasil kelas online.</p>
        </div>
    </div>
</div>

<!-- MODAL EDIT YOUTUBE LINK -->
<div id="editYtModal" onclick="closeEditYtModal()" class="fixed inset-0 bg-violet-950/60 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm transition duration-300">
    <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl transform scale-95 opacity-0 transition-all duration-300 flex flex-col max-h-[90vh]" id="modalContainer" onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 flex-shrink-0">
            <div>
                <h3 class="font-bold text-md text-violet-950">Update Link Rekaman YouTube</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Edit link video rekaman pertemuan kelas</p>
            </div>
            <button onclick="closeEditYtModal()" class="w-8 h-8 rounded-full bg-gray-100 text-gray-400 hover:bg-rose-50 hover:text-rose-500 flex items-center justify-center transition focus:outline-none">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <!-- Modal Form -->
        <form id="editYtForm" action="{{ route('admin-lms.materi.update_youtube') }}" method="POST" class="p-6 space-y-4 overflow-y-auto flex-1" onsubmit="showSubmitLoading()">
            @csrf
            <input type="hidden" name="mapel" id="materi_mapel">
            <input type="hidden" name="judul" id="materi_judul">

            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Mata Pelajaran</p>
                <p class="text-xs font-bold text-violet-950 mb-3" id="display_mapel"></p>
                
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Bahasan Sesi</p>
                <p class="text-xs text-gray-700 leading-normal mb-4" id="display_judul"></p>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1.5">Link Video YouTube (URL Lengkap)</label>
                <input type="url" name="link_youtube" id="materi_youtube" required placeholder="https://www.youtube.com/watch?v=..." class="w-full rounded-xl border-gray-200 shadow-sm p-3 border text-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                <p class="text-[9px] text-gray-400 mt-1">Pastikan format link YouTube valid agar video tersemat di dashboard siswa.</p>
            </div>

            <div class="flex gap-3 pt-4 border-t border-gray-50">
                <button type="submit" class="flex-1 bg-violet-600 hover:bg-violet-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition text-xs flex items-center justify-center gap-1.5">
                    <i class="fas fa-save"></i> Perbarui Link
                </button>
                <button type="button" onclick="closeEditYtModal()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-4 rounded-xl transition text-xs">Batal</button>
            </div>
        </form>
    </div>
</div>



<script>
    function openEditYtModal(materi) {
        const modal = document.getElementById('editYtModal');
        const container = document.getElementById('modalContainer');
        
        document.getElementById('materi_mapel').value = materi.mapel ?? '';
        document.getElementById('materi_judul').value = materi.judul ?? '';
        document.getElementById('materi_youtube').value = materi.link_youtube ?? '';
        
        document.getElementById('display_mapel').innerText = materi.mapel ?? '';
        document.getElementById('display_judul').innerText = materi.judul ?? '';
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            container.classList.remove('scale-95', 'opacity-0');
            container.classList.add('scale-100', 'opacity-100');
        }, 50);
    }

    function closeEditYtModal() {
        const modal = document.getElementById('editYtModal');
        const container = document.getElementById('modalContainer');
        
        container.classList.remove('scale-100', 'opacity-100');
        container.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function showSubmitLoading() {
        document.getElementById('loadingOverlay').classList.remove('hidden');
        closeEditYtModal();
    }
</script>
@endsection
