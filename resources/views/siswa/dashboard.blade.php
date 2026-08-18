@extends('layouts.main')

@section('title', 'Dashboard Siswa')
@section('page_title', 'Dashboard Pembelajaran')

@section('content')

<!-- NOTIFIKASI FLASH -->
@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-2xl shadow-sm mb-6 flex items-center gap-3 transition">
    <div class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center flex-shrink-0">
        <i class="fas fa-check"></i>
    </div>
    <div>
        <p class="font-bold text-sm">Berhasil!</p>
        <p class="text-xs text-green-700">{{ session('success') }}</p>
    </div>
</div>
@endif

@if(session('error'))
<div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-2xl shadow-sm mb-6 flex items-center gap-3 transition">
    <div class="w-8 h-8 rounded-full bg-red-500 text-white flex items-center justify-center flex-shrink-0">
        <i class="fas fa-exclamation-triangle"></i>
    </div>
    <div>
        <p class="font-bold text-sm">Gagal!</p>
        <p class="text-xs text-red-700">{{ session('error') }}</p>
    </div>
</div>
@endif


<!-- loading state skeleton -->
<div id="lmsLoader" class="flex flex-col items-center justify-center py-20">
    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
    <p class="text-gray-500 font-medium text-xs">Sinkronisasi data dengan server...</p>
</div>

<!-- MAIN CONTAINER CONTENT -->
<div id="lmsContent" class="hidden">

    <!-- 1. TAB: OVERVIEW (BERANDA) -->
    <div id="tab-content-overview" class="tab-pane space-y-6">
        <!-- A. BANNER & PROGRESS SECTION -->
        <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-800 rounded-3xl p-8 text-white relative overflow-hidden shadow-xl border border-blue-400/20">
            <!-- Background Blob Decoration -->
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-40 animate-blob"></div>
            <div class="absolute right-40 -bottom-20 w-64 h-64 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-40 animate-blob animation-delay-2000"></div>
            
            <div class="relative z-10 flex flex-col lg:flex-row justify-between items-center gap-8">
                <div class="mb-6 lg:mb-0 max-w-xl">
                    <h2 class="text-2xl md:text-3xl font-black mb-2 tracking-tight">Selamat datang, {{ session('nama', 'Siswa') }}! 👋</h2>
                    <p class="text-blue-100 text-xs md:text-sm font-medium">Semangat belajar! Selesaikan seluruh materi Brevet Pajak dan kumpulkan tugas Anda untuk kelulusan.</p>
                    
                    <div id="nextClassContainer" class="mt-6 hidden">
                        <div class="inline-block bg-white/10 backdrop-blur-md border border-white/20 rounded-3xl p-5 shadow-lg max-w-sm hover:bg-white/15 transition-all duration-300">
                            <div class="flex items-center justify-between gap-4 mb-3">
                                <p class="text-[9px] text-blue-100 font-black uppercase tracking-widest">Jadwal Kelas Terdekat</p>
                                <span id="liveBadge" class="inline-flex items-center gap-1 bg-red-500/25 text-red-200 border border-red-500/35 text-[8px] font-bold uppercase px-2.5 py-0.5 rounded-full tracking-wider animate-pulse hidden">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Live
                                </span>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-amber-400 text-slate-900 flex items-center justify-center font-bold text-md shadow-sm">
                                    <i class="fas fa-video animate-pulse"></i>
                                </div>
                                <div>
                                    <p id="nextClassTitle" class="font-bold text-white text-xs leading-snug">Memuat jadwal...</p>
                                    <p id="nextClassTime" class="text-[10px] text-blue-100 mt-0.5"><i class="far fa-clock mr-1 text-amber-400"></i> - </p>
                                </div>
                                <a id="btnJoinZoom" href="#" target="_blank" onclick="catatAbsenLive(event, this.dataset.mapel, this.href)" class="ml-4 bg-amber-400 hover:bg-amber-500 text-slate-900 text-[10px] font-black py-2.5 px-4 rounded-xl shadow transition duration-200 hidden">
                                    Join Zoom
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Circular Progress -->
                <div class="flex items-center gap-6 bg-white/10 backdrop-blur-md p-6 rounded-3xl border border-white/20 w-full lg:w-auto hover:bg-white/15 transition-all duration-300 shadow-inner">
                    <div class="relative w-16 h-16 flex-shrink-0">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-blue-900/30" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3.5"></path>
                            <path id="progressCircle" class="text-white transition-all duration-1000 ease-out" stroke-dasharray="0, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3.5"></path>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span id="progressText" class="text-xs font-black text-white">0%</span>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xs font-black text-white uppercase tracking-wider">Progres Belajar</h3>
                        <p class="text-blue-100 text-[10px] font-medium mt-1"><span id="mapelSelesai">0</span> dari <span id="totalMapel">12</span> mapel selesai</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- CERTIFICATE BOARD CONTAINER (DYNAMIC HYBRID) -->
        <div id="certificateBoardContainer" class="hidden"></div>

        <!-- QUICK ANNOUNCEMENT & STATS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm md:col-span-2">
                <h4 class="font-bold text-gray-800 text-md mb-4"><i class="fas fa-bullhorn text-blue-600 mr-2"></i> Pengumuman Kelas</h4>
                
                <div class="space-y-4">
                    @forelse($announcements as $ann)
                        <div class="bg-gray-50 border border-gray-150 p-5 rounded-2xl relative shadow-sm hover:shadow-md transition duration-200">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex items-center gap-3">
                                    <!-- Avatar lingkaran dengan inisial pembuat -->
                                    <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs uppercase tracking-wider shadow">
                                        {{ substr($ann->author_name, 0, 2) }}
                                    </div>
                                    <div>
                                        <h5 class="text-xs font-black text-gray-800">{{ $ann->author_name }}</h5>
                                        <p class="text-[9px] text-gray-400 font-semibold">
                                            {{ $ann->created_at->diffForHumans() }} 
                                            @if($ann->target_kelas !== 'ALL')
                                                • <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-bold border border-blue-200 text-[8px] ml-1">{{ $ann->target_kelas }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                
                                @if(session('email') === $ann->author_email || in_array(session('role'), ['ADMIN', 'ADMIN_LMS']))
                                    <form action="{{ route('announcements.delete', $ann->id) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini beserta semua komentarnya?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-650 transition p-1" title="Hapus Pengumuman">
                                            <i class="far fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                            
                            <h4 class="text-xs font-black text-gray-800 mb-1 tracking-tight">{{ $ann->title }}</h4>
                            <div class="text-[11px] text-gray-650 whitespace-pre-line leading-relaxed mb-4 font-medium">
                                {{ $ann->content }}
                            </div>
                            
                            <hr class="border-gray-200 my-3">
                            
                            <!-- Bagian Komentar / Diskusi -->
                            <div class="mt-2">
                                <button onclick="toggleComments({{ $ann->id }})" class="text-[11px] font-black text-blue-700 hover:text-blue-805 transition flex items-center gap-1">
                                    <i class="far fa-comments text-xs"></i> 
                                    <span>Diskusi Kelas ({{ count($ann->comments) }} komentar)</span>
                                    <i id="comment-icon-{{ $ann->id }}" class="fas fa-chevron-down text-[8px] transition-transform duration-200 ml-0.5"></i>
                                </button>
                                
                                <div id="comments-container-{{ $ann->id }}" class="hidden space-y-2 pl-3 border-l-2 border-blue-100 mt-3 mb-2">
                                    @foreach($ann->comments as $comment)
                                        <div class="flex items-start justify-between gap-3 text-[11px] bg-white border border-gray-100 p-2.5 rounded-xl shadow-sm">
                                            <div class="space-y-0.5">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="font-bold text-gray-800 text-[10px]">{{ $comment->name }}</span>
                                                    <span class="text-[8px] text-gray-400 font-semibold">{{ $comment->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="text-gray-650 leading-relaxed font-medium">{{ $comment->content }}</p>
                                            </div>
                                            
                                            @if(session('email') === $comment->email || session('email') === $ann->author_email || in_array(session('role'), ['ADMIN', 'ADMIN_LMS']))
                                                <form action="{{ route('comments.delete', $comment->id) }}" method="POST" onsubmit="return confirm('Hapus komentar ini?')" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-gray-400 hover:text-red-650 transition p-0.5" title="Hapus Komentar">
                                                        <i class="far fa-trash-alt text-[10px]"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endforeach
                                    
                                    <!-- Form Tulis Komentar -->
                                    <form action="{{ route('announcements.comments.store', $ann->id) }}" method="POST" class="mt-3 flex gap-2">
                                        @csrf
                                        <input type="text" name="content" placeholder="Tulis tanggapan atau pertanyaan..." required class="flex-grow bg-white border border-gray-200 rounded-xl px-3 py-1.5 text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-medium">
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] px-4 py-1.5 rounded-xl font-bold shadow transition duration-200">
                                            Kirim
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 bg-gray-50 border border-dashed border-gray-200 rounded-2xl text-gray-400">
                            <i class="fas fa-bullhorn text-2xl mb-2 text-gray-300 animate-bounce"></i>
                            <p class="text-[11px] font-bold">Belum ada pengumuman kelas saat ini.</p>
                            <p class="text-[9px] text-gray-400 mt-1">Dosen atau admin belum mempublikasikan informasi apa pun.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-center">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Akun Terdaftar</h4>
                <p class="font-bold text-gray-800 text-md leading-tight">{{ session('nama') }}</p>
                <p class="text-xs text-gray-500 mt-1 mb-4">{{ session('email') }}</p>
                <hr class="border-gray-100 mb-3">
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>Grup Kelas</span>
                    <span class="font-semibold text-blue-700">Brevet Pajak</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. TAB: JADWAL KELAS -->
    <div id="tab-content-jadwal" class="tab-pane hidden">
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 mb-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Jadwal Kelas Brevet</h3>
                    <p class="text-xs text-gray-450 mt-0.5">Daftar sesi kelas live Zoom pembelajaran perpajakan Anda</p>
                </div>
                <span class="text-xs font-semibold bg-blue-50 text-blue-700 px-3 py-1 rounded-full border border-blue-100" id="jadwal-count">0 Sesi</span>
            </div>
            
            <!-- Cards Container -->
            <div id="jadwal-cards-container" class="space-y-6">
                <!-- Di-render oleh JS -->
            </div>
        </div>
    </div>

    <!-- 3. TAB: MATERI KULIAH -->
    <div id="tab-content-materi" class="tab-pane hidden space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold text-gray-800">Materi & Modul Pelatihan</h3>
                <p class="text-xs text-gray-405 mt-0.5">Unduh PDF materi dan tonton rekaman pembelajaran kapan saja</p>
            </div>
            <span class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-bold">12 Mata Pelajaran</span>
        </div>

        <!-- Container Grid Mapel -->
        <div id="mapelGrid" class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            <!-- Card akan di-render pakai JS -->
        </div>
    </div>

    <!-- 4. TAB: TUGAS & UJIAN -->
    <div id="tab-content-tugas" class="tab-pane hidden space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold text-gray-800">Tugas & Ujian Brevet</h3>
                <p class="text-xs text-gray-405 mt-0.5">Kumpulkan tugas Anda sebelum batas waktu yang ditentukan</p>
            </div>
            <span class="text-xs font-semibold bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full border border-yellow-200" id="tugas-count">0 Tugas</span>
        </div>
        
        <div id="tugasListContainer" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Renders dynamically -->
        </div>
    </div>

    <!-- 5. TAB: REKAP NILAI -->
    <div id="tab-content-nilai" class="tab-pane hidden">
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Rekap Nilai Siswa</h3>
                    <p class="text-xs text-gray-405 mt-0.5">Rekapitulasi hasil pengumpulan dan evaluasi oleh Dosen</p>
                </div>
                <span class="text-xs font-semibold bg-green-50 text-green-700 px-3 py-1 rounded-full border border-green-100" id="nilai-count">0 Tugas Selesai</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-400 text-xs font-bold uppercase tracking-wider">
                            <th class="pb-3 pl-4">ID Tugas</th>
                            <th class="pb-3">Tugas</th>
                            <th class="pb-3">File Upload</th>
                            <th class="pb-3">Nilai</th>
                            <th class="pb-3 pr-4">Catatan Dosen</th>
                        </tr>
                    </thead>
                    <tbody id="nilai-table-body" class="divide-y divide-gray-50 text-sm text-gray-700">
                        <!-- Di-render oleh JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 5. TAB: KEHADIRAN (ABSENSI) -->
    <div id="tab-content-kehadiran" class="tab-pane hidden space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Panel: Summary Kehadiran -->
            <div class="lg:col-span-1 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div>
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Persentase Kehadiran</h4>
                    <div class="flex items-baseline gap-2 mb-4">
                        <span id="presencePercentText" class="text-3xl font-black text-blue-700">0%</span>
                        <span class="text-xs text-gray-400 font-semibold">dari total kelas</span>
                    </div>
                    <hr class="border-gray-100 mb-4">
                    <p class="text-[10px] text-gray-500 leading-relaxed font-medium">Sistem menghitung absensi secara otomatis ketika Anda mengeklik <strong>Join Zoom</strong> atau <strong>Konfirmasi Nonton Rekaman</strong>.</p>
                </div>
                
                <div class="mt-6 space-y-3.5 text-xs">
                    <div class="flex justify-between items-center text-gray-500">
                        <span>Total Kehadiran</span>
                        <span class="font-bold text-gray-800" id="totalPresenceCount">0 Sesi</span>
                    </div>
                    <hr class="border-gray-100">
                    <h5 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Detail Kehadiran Per Mata Pelajaran</h5>
                    <div id="subjectPresenceList" class="space-y-3 max-h-[220px] overflow-y-auto pr-1">
                        <!-- Renders dynamically via JS -->
                    </div>
                </div>
            </div>

            <!-- Right Panel: Tabel Log Kehadiran -->
            <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-base font-bold text-gray-800">Riwayat Presensi</h3>
                        <p class="text-xs text-gray-450 mt-0.5">Catatan kehadiran belajar perpajakan Anda</p>
                    </div>
                </div>
                
                <div class="overflow-x-auto max-h-[400px] scrollbar-none">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 text-gray-400 text-[10px] font-bold uppercase tracking-wider">
                                <th class="pb-3 pl-4">Tanggal Absen</th>
                                <th class="pb-3">Mata Pelajaran</th>
                                <th class="pb-3">Metode</th>
                                <th class="pb-3 pr-4">Status</th>
                            </tr>
                        </thead>
                        <tbody id="absensi-table-body" class="divide-y divide-gray-50 text-xs text-gray-700">
                            <!-- Di-render oleh JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. TAB: PROFILE SETTINGS -->
    <div id="tab-content-profil" class="tab-pane hidden space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold text-gray-800">Pengaturan Akun & Profil</h3>
                <p class="text-xs text-gray-450 mt-0.5">Perbarui nama tampilan atau ganti password keamanan Anda</p>
            </div>
        </div>

        @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-100 flex flex-col gap-1.5 shadow-sm">
            <div class="flex items-center gap-3 text-rose-800 font-bold text-xs">
                <i class="fas fa-exclamation-triangle text-rose-500"></i>
                <span>Gagal Validasi Profil!</span>
            </div>
            <ul class="text-[10px] text-rose-700 list-disc list-inside pl-6 font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Side: Profile Summary Card -->
            <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm flex flex-col items-center text-center">
                <div class="w-20 h-20 rounded-full bg-blue-600 text-white flex items-center justify-center font-black text-2xl uppercase tracking-wider shadow-md mb-4 ring-4 ring-blue-50">
                    {{ strtoupper(substr(session('nama', 'U'), 0, 2)) }}
                </div>
                <h4 class="font-bold text-gray-800 text-base leading-tight">{{ session('nama') }}</h4>
                <p class="text-xs text-gray-400 mt-1 font-semibold">{{ session('email') }}</p>
                <span class="mt-4 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-100">
                    Siswa / Peserta
                </span>
                
                <hr class="w-full border-gray-100 my-6">
                
                <div class="w-full space-y-3.5 text-xs text-left">
                    <div class="flex justify-between items-center text-gray-500">
                        <span>Grup Kelas</span>
                        <span class="font-semibold text-gray-800">{{ session('kelas') ?: 'Brevet Pajak' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-gray-500">
                        <span>Status Akun</span>
                        <span class="font-semibold text-emerald-600 flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Aktif</span>
                    </div>
                </div>
            </div>

            <!-- Right Side: Edit Form -->
            <div class="lg:col-span-2 bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
                <h4 class="font-bold text-gray-800 text-md mb-6"><i class="fas fa-user-cog text-blue-600 mr-2"></i> Perbarui Detail Profil & Keamanan</h4>
                
                <form action="{{ route('profil.update') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Nama Lengkap Anda</label>
                        <input type="text" name="nama" required value="{{ old('nama', session('nama')) }}" class="w-full rounded-xl border-gray-300 shadow-sm p-3 border text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-medium">
                        <p class="text-[10px] text-gray-400 mt-1.5">Gunakan nama lengkap yang benar untuk pencatatan sertifikat kelulusan.</p>
                    </div>
                    
                    <hr class="border-gray-100 my-6">
                    
                    <div class="bg-gray-50/50 p-6 rounded-2xl border border-gray-150 space-y-4">
                        <h5 class="text-xs font-black text-gray-800 uppercase tracking-wider mb-2 flex items-center gap-2 text-blue-600">
                            <i class="fas fa-lock"></i> Ganti Password <span class="text-[9px] text-gray-400 font-normal uppercase tracking-normal ml-1">(Kosongkan jika tidak ingin mengubah)</span>
                        </h5>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Password Lama</label>
                            <input type="password" name="old_password" placeholder="Masukkan password Anda saat ini" class="w-full rounded-xl bg-white border-gray-300 shadow-sm p-3 border text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-medium">
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2">Password Baru</label>
                                <input type="password" name="new_password" placeholder="Minimal 6 karakter" class="w-full rounded-xl bg-white border-gray-300 shadow-sm p-3 border text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-medium">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2">Konfirmasi Password Baru</label>
                                <input type="password" name="new_password_confirmation" placeholder="Ulangi password baru" class="w-full rounded-xl bg-white border-gray-300 shadow-sm p-3 border text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-medium">
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end pt-4">
                        <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-blue-500/10 transition text-xs flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i> Simpan Perubahan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<!-- MODAL YOUTUBE -->
<div id="youtubeModal" onclick="closeYoutubeModal()" class="fixed inset-0 bg-black/80 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl w-full max-w-4xl overflow-hidden shadow-2xl scale-95 transform transition-transform duration-300" id="youtubeModalContent" onclick="event.stopPropagation()">
        <div class="bg-blue-600 p-4 flex justify-between items-center text-white">
            <h3 class="font-bold text-sm" id="ytModalTitle">Rekaman Kelas</h3>
            <button onclick="closeYoutubeModal()" class="text-white hover:text-red-400 transition"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div class="p-0 bg-black aspect-video relative">
            <iframe id="ytIframe" class="w-full h-full" src="" frameborder="0" allowfullscreen></iframe>
        </div>
        <div class="p-5 bg-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <p class="text-sm font-bold text-gray-700">Apakah Anda berhalangan hadir saat live Zoom dan belajar lewat rekaman?</p>
                <p class="text-xs text-gray-500">Konfirmasikan kehadiran Anda dengan mengklik tombol emas di sebelah kanan.</p>
            </div>
            <button id="btnConfirmNonton" class="bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-600 hover:to-yellow-600 text-blue-950 font-black py-2.5 px-6 rounded-xl shadow-lg transition duration-200 flex items-center gap-2 whitespace-nowrap text-xs">
                <i class="fas fa-award"></i> Konfirmasi Telah Menonton Rekaman
            </button>
        </div>
    </div>
</div>

<!-- MODAL UPLOAD TUGAS -->
<div id="tugasModal" onclick="closeTugasModal()" class="fixed inset-0 bg-gray-900/60 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl flex flex-col max-h-[90vh]" onclick="event.stopPropagation()">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
            <h3 class="font-bold text-md" id="tugasModalTitle">Upload Tugas</h3>
            <button onclick="closeTugasModal()" class="text-gray-400 hover:text-red-500 transition"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <p id="tugasDesc" class="text-xs text-gray-650 mb-4 bg-gray-50 p-4 rounded-xl border border-gray-200 leading-relaxed">Deskripsi tugas...</p>
            <a id="tugasLinkSoal" href="#" target="_blank" class="hidden mb-4 text-xs text-blue-600 hover:text-blue-800 font-semibold"><i class="fas fa-external-link-alt mr-1"></i> Lihat Soal Lengkap</a>
            
            <form id="formUploadTugas" class="space-y-4">
                <input type="hidden" id="inputIdTugas">
                          <!-- OPSI 1: UPLOAD FILE -->
                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-150">
                    <label class="block text-xs font-bold text-blue-955 mb-2">📌 Opsi A: Unggah Berkas File</label>
                    
                    <!-- Custom Drag & Drop Dropzone -->
                    <label for="inputFileTugas" class="border-2 border-dashed border-blue-200 hover:border-blue-550 bg-blue-50/10 hover:bg-blue-50/30 rounded-2xl p-6 flex flex-col items-center justify-center gap-2 cursor-pointer hover:scale-[1.02] transition-all duration-300 ease-in-out">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-md transition-transform duration-300 group-hover:scale-110">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="text-center">
                            <p class="text-xs font-bold text-slate-700">Tarik & Lepas file di sini</p>
                            <p class="text-[9px] text-slate-500">atau klik untuk memilih file dari komputer</p>
                        </div>
                        <span id="selectedFileNameDisplay" class="text-xs font-semibold text-blue-700 hidden"></span>
                    </label>
                    <input type="file" id="inputFileTugas" class="hidden" onchange="displaySelectedFile(this, 'selectedFileNameDisplay')">
                    
                    <p class="text-[9px] text-gray-400 mt-1.5">PDF, Excel, Word, ZIP, dll (Maksimal 30MB)</p>
                </div>

                <div class="relative flex py-1 items-center">
                    <div class="flex-grow border-t border-gray-200"></div>
                    <span class="flex-shrink mx-4 text-gray-400 font-bold text-[10px] uppercase">Atau</span>
                    <div class="flex-grow border-t border-gray-200"></div>
                </div>

                <!-- OPSI 2: TAUTAN / LINK -->
                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-150">
                    <label class="block text-xs font-bold text-blue-950 mb-2">🔗 Opsi B: Masukkan Tautan / Link Tugas</label>
                    <input type="url" id="inputLinkTugas" placeholder="https://drive.google.com/..."
                        class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-medium">
                    <p class="text-[9px] text-gray-400 mt-1.5">Link Google Drive, Canva, GitHub, dll. (Pastikan akses diset Publik/Bisa Dilihat).</p>
                </div>

                <p class="text-[10px] text-red-500 mt-1" id="fileError"></p>

                <button type="button" onclick="submitTugasProcess()" id="btnSubmitTugas" class="w-full bg-blue-600 hover:bg-indigo-800 text-white font-bold py-3 px-4 rounded-xl shadow-lg transition flex items-center justify-center gap-2 mt-4 text-xs">
                    <i class="fas fa-cloud-upload-alt"></i> Kirim Tugas
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // 12 Mata Pelajaran Brevet Pajak
    const LIST_MAPEL = [
        "Ketentuan Umum dan Tata Cara Perpajakan (KUP) A & B",
        "Pajak Penghasilan (PPh) Orang Pribadi",
        "Pajak Pemotongan dan Pemungutan (PPh Pasal 21)",
        "Pajak Pemotongan dan Pemungutan (PPh Pasal 22, 23, 26, & 4(2))",
        "Pajak Penghasilan (PPh) Badan",
        "Pajak Pertambahan Nilai (PPN) dan PPnBM A & B",
        "Pajak Bumi dan Bangunan (PBB), BPHTB, & Bea Meterai",
        "Akuntansi Perpajakan",
        "Pemeriksaan dan Penyidikan Pajak",
        "Pengisian e-SPT / Aplikasi Perpajakan (e-Faktur, dll)",
        "Tax Planning (Perencanaan Pajak)",
        "Ujian Kelulusan / Komprehensif Brevet"
    ];

    let lmsData = null;
    let lastDashboardLoad = 0;
    const dashboardRefreshInterval = 90 * 1000; // refresh ulang jika tab kembali aktif setelah 90 detik

    // Retry control to avoid infinite reload loop when server unreachable
    let dashboardRetryCount = 0;
    const dashboardMaxRetries = 3;
    const dashboardRetryDelay = 5000; // ms

    document.addEventListener("DOMContentLoaded", () => {
        loadDashboardData();
    });

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            const now = Date.now();
            if (now - lastDashboardLoad >= dashboardRefreshInterval) {
                loadDashboardData();
            }
        }
    });

    async function loadDashboardData() {
        try {
            const response = await fetch("{{ route('siswa.api.dashboard') }}");
            // Jika bukan OK, tangani khusus
            if (!response.ok) {
                if (response.status === 401) {
                    // Tidak terautentikasi -> redirect ke login
                    Swal.fire({
                        title: 'Sesi Berakhir',
                        text: 'Sesi Anda telah berakhir. Silakan login ulang.',
                        icon: 'warning',
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        window.location.href = '{{ route('login') }}';
                    });
                    return;
                }
                throw new Error('HTTP ' + response.status);
            }
            lmsData = await response.json();
            lastDashboardLoad = Date.now();
            // Reset retry counter on success
            dashboardRetryCount = 0;
            
            document.getElementById('lmsLoader').style.display = 'none';
            document.getElementById('lmsContent').style.display = 'block';

            // Renders
            renderProgress(lmsData.progress_persen, lmsData.mapel_selesai, lmsData.total_mapel);
            renderCertificateBoard(lmsData.progress_persen, "{{ session('sertifikat', '') }}");
            renderNextClass(lmsData.jadwal);
            renderJadwal(lmsData.jadwal);
            renderMapelGrid(lmsData);
            renderTugasTab(lmsData);
            renderNilaiTab(lmsData);
            renderAbsensiTab(lmsData);

            // Handle Tab Initial dari URL
            const urlParams = new URLSearchParams(window.location.search);
            const initialTab = urlParams.get('tab') || 'overview';
            switchTab(initialTab);

        } catch (error) {
            console.error("Gagal load data", error);
            dashboardRetryCount++;

            // Jika belum melewati batas retry, coba ulang otomatis setelah delay
            if (dashboardRetryCount <= dashboardMaxRetries) {
                Swal.fire({
                    title: 'Koneksi Lambat',
                    text: `Gagal memuat data dari server. Mencoba ulang (${dashboardRetryCount}/${dashboardMaxRetries}) dalam ${dashboardRetryDelay/1000} detik.`,
                    icon: 'warning',
                    confirmButtonColor: '#2563eb'
                });
                setTimeout(() => {
                    loadDashboardData();
                }, dashboardRetryDelay);
            } else {
                // Tampilkan opsi manual setelah beberapa kali gagal
                Swal.fire({
                    title: 'Koneksi Bermasalah',
                    text: 'Gagal memuat data dari server berkali-kali. Klik "Coba Lagi" untuk mencoba kembali secara manual atau periksa koneksi server Anda.',
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonText: 'Coba Lagi',
                    cancelButtonText: 'Tutup',
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#6b7280'
                }).then((result) => {
                    if (result.isConfirmed) {
                        dashboardRetryCount = 0;
                        document.getElementById('lmsLoader').style.display = 'flex';
                        loadDashboardData();
                    }
                });
            }
        }
    }

    // --- TAB SWITCHER LOGIC ---
    function switchTab(tabId) {
        // Sembunyikan semua section
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));
        
        // Tampilkan section terpilih
        const activePane = document.getElementById('tab-content-' + tabId);
        if (activePane) activePane.classList.remove('hidden');

        // Reset class semua tombol tab
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.className = "tab-btn flex-shrink-0 px-5 py-2.5 rounded-xl font-bold text-sm transition duration-200 text-slate-500 hover:text-blue-600 hover:bg-slate-100";
        });

        // Set class aktif ke tombol terpilih
        const activeBtn = document.getElementById('tab-' + tabId);
        if (activeBtn) {
            activeBtn.className = "tab-btn flex-shrink-0 px-5 py-2.5 rounded-xl font-bold text-sm transition duration-200 bg-blue-600 text-white shadow-md";
        }

        // Simpan State URL tanpa mereload halaman
        const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=' + tabId;
        window.history.pushState({path: newUrl}, '', newUrl);

        // Sync sidebar active visual if the sync function is exposed
        if (typeof window.syncSidebarActiveState === 'function') {
            window.syncSidebarActiveState(tabId);
        }
    }

    // --- RENDERS ---
    function renderProgress(persen, selesai, total) {
        document.getElementById('progressText').innerText = persen + '%';
        document.getElementById('progressCircle').setAttribute('stroke-dasharray', `${persen}, 100`);
        document.getElementById('mapelSelesai').innerText = selesai;
        document.getElementById('totalMapel').innerText = total;
    }

    function renderCertificateBoard(persen, sertifikatUrl) {
        const container = document.getElementById('certificateBoardContainer');
        if (!container) return;

        container.innerHTML = '';
        container.classList.add('hidden');

        if (sertifikatUrl && sertifikatUrl.trim() !== "") {
            // Case 1: Certificate link is ready (show Golden Premium card)
            container.classList.remove('hidden');
            container.innerHTML = `
                <div class="bg-gradient-to-r from-amber-500 to-yellow-500 text-slate-900 p-6 rounded-3xl shadow-xl flex flex-col md:flex-row items-center justify-between gap-4 border border-yellow-350/30 hover:scale-[1.01] transition-all duration-300 mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-2xl shadow-inner text-slate-900">
                            <i class="fas fa-award animate-bounce"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg leading-tight">Selamat, Program Brevet Selesai! 🏆</h4>
                            <p class="text-xs text-slate-800 font-medium">Sertifikat kelulusan resmi Anda sudah diterbitkan dan siap diunduh.</p>
                        </div>
                    </div>
                    <a href="${sertifikatUrl}" target="_blank" class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-black py-3 px-6 rounded-xl shadow transition duration-200 flex items-center gap-2 whitespace-nowrap">
                        <i class="fas fa-download"></i> Unduh Sertifikat (PDF)
                    </a>
                </div>
            `;
        } else if (persen === 100) {
            // Case 2: Progress is 100% but certificate not uploaded yet (show Blue Info card)
            container.classList.remove('hidden');
            container.innerHTML = `
                <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-700 text-white p-6 rounded-3xl shadow-xl flex flex-col md:flex-row items-center justify-between gap-4 border border-blue-400/20 hover:scale-[1.01] transition-all duration-300 mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center text-2xl shadow-inner text-amber-400">
                            <i class="fas fa-certificate animate-pulse"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg leading-tight">Progres Belajar Selesai! 🚀</h4>
                            <p class="text-xs text-blue-100 font-medium">Selamat, Anda telah menyelesaikan 100% materi & tugas. Sertifikat kelulusan Anda sedang dalam proses verifikasi & penerbitan oleh Admin.</p>
                        </div>
                    </div>
                    <span class="bg-white/10 border border-white/20 text-white text-xs font-bold py-2.5 px-5 rounded-xl flex items-center gap-2 whitespace-nowrap">
                        <i class="fas fa-spinner fa-spin text-xs"></i> Menunggu Penerbitan
                    </span>
                </div>
            `;
        }
    }

    function renderNextClass(jadwalList) {
        if (!jadwalList || jadwalList.length === 0) return;
        
        const next = jadwalList[0];
        
        document.getElementById('nextClassContainer').classList.remove('hidden');
        document.getElementById('nextClassTitle').innerText = next.materi || '-';
        
        let displayJam = next.jam ?? '-';
        if (displayJam !== '-') {
            displayJam = String(displayJam).replace(/WIB/gi, '').trim().replace(/\./g, ':');
            if (displayJam.includes('T')) {
                const match = displayJam.match(/T(\d{2}:\d{2})/);
                if (match) displayJam = match[1];
            }
            const match = displayJam.match(/(\d{1,2}):(\d{2})\s*(AM|PM)?/i);
            if (match) {
                let hours = parseInt(match[1]);
                const minutes = match[2];
                const ampm = match[3];
                if (ampm) {
                    if (ampm.toUpperCase() === 'PM' && hours < 12) hours += 12;
                    else if (ampm.toUpperCase() === 'AM' && hours === 12) hours = 0;
                }
                displayJam = String(hours).padStart(2, '0') + ':' + minutes;
            }
        }
        
        const tanggalText = next.tanggal || '-';
        document.getElementById('nextClassTime').innerHTML = `<i class="far fa-clock mr-1"></i> ${tanggalText} | ${displayJam}`;
        
        if (next.link) {
            const btn = document.getElementById('btnJoinZoom');
            btn.classList.remove('hidden');
            btn.href = next.link;
            btn.dataset.mapel = next.materi;
            const badge = document.getElementById('liveBadge');
            if (badge) badge.classList.remove('hidden');
        } else {
            const badge = document.getElementById('liveBadge');
            if (badge) badge.classList.add('hidden');
        }
    }

    function renderJadwal(jadwalList) {
        const container = document.getElementById('jadwal-cards-container');
        const count = document.getElementById('jadwal-count');
        container.innerHTML = '';
        
        if (!jadwalList || jadwalList.length === 0) {
            container.innerHTML = `
                <div class="text-center py-12 text-gray-400 border-2 border-dashed border-gray-200 rounded-3xl bg-gray-50/30">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400 shadow-inner">
                        <i class="far fa-calendar-times text-2xl"></i>
                    </div>
                    <p class="text-xs font-bold text-gray-700">Belum ada jadwal sesi kuliah aktif.</p>
                    <p class="text-[10px] text-gray-400 mt-1">Dosen atau admin belum menjadwalkan sesi kuliah saat ini.</p>
                </div>
            `;
            count.innerText = '0 Sesi';
            return;
        }

        count.innerText = `${jadwalList.length} Sesi`;

        jadwalList.forEach((item, index) => {
            let displayJam = item.jam ?? '-';
            if (displayJam !== '-') {
                displayJam = String(displayJam).replace(/WIB/gi, '').trim().replace(/\./g, ':');
                if (displayJam.includes('T')) {
                    const match = displayJam.match(/T(\d{2}:\d{2})/);
                    if (match) displayJam = match[1];
                }
                const match = displayJam.match(/(\d{1,2}):(\d{2})\s*(AM|PM)?/i);
                if (match) {
                    let hours = parseInt(match[1]);
                    const minutes = match[2];
                    const ampm = match[3];
                    if (ampm) {
                        if (ampm.toUpperCase() === 'PM' && hours < 12) hours += 12;
                        else if (ampm.toUpperCase() === 'AM' && hours === 12) hours = 0;
                    }
                    displayJam = String(hours).padStart(2, '0') + ':' + minutes;
                }
            }

            let cardHtml = '';
            
            if (index === 0) {
                // Featured Active Class Card
                let linkHtml = '';
                if (item.link) {
                    linkHtml = `
                        <a href="${item.link}" target="_blank" onclick="catatAbsenLive(event, '${item.mapel}', '${item.link}')" class="mt-4 inline-flex items-center justify-center gap-2 bg-amber-400 hover:bg-amber-500 text-slate-900 text-xs font-black py-3 px-6 rounded-2xl shadow-lg hover:shadow-amber-400/20 hover:scale-[1.02] transition-all duration-200 w-full sm:w-auto">
                            <i class="fas fa-video text-xs animate-pulse"></i> Hubungkan ke Live Zoom
                        </a>
                    `;
                } else {
                    linkHtml = `
                        <span class="mt-4 inline-flex items-center gap-1.5 bg-white/10 text-white/60 text-xs font-bold py-2.5 px-4 rounded-xl border border-white/5">
                            <i class="fas fa-link-slash text-xs"></i> Tautan Belum Tersedia
                        </span>
                    `;
                }

                cardHtml = `
                    <div class="bg-gradient-to-br from-blue-600 via-indigo-600 to-indigo-800 rounded-3xl p-6 md:p-8 text-white relative overflow-hidden shadow-xl border border-blue-400/20 hover:scale-[1.01] transition-all duration-300">
                        <div class="absolute -right-20 -top-20 w-64 h-64 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-40 animate-blob"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between gap-3 mb-4 flex-wrap">
                                <span class="inline-flex items-center gap-1.5 bg-red-500 text-white text-[9px] font-black uppercase tracking-widest px-3 py-1 rounded-full animate-pulse shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span> Live Sesi Terdekat
                                </span>
                                <span class="text-[10px] text-blue-100 font-bold bg-white/15 px-3 py-1 rounded-full border border-white/10"><i class="far fa-user mr-1 text-amber-400"></i> Dosen: ${item.dosen || '-'}</span>
                            </div>
                            
                            <div class="mb-5">
                                <span class="text-[10px] font-extrabold uppercase tracking-widest text-amber-400">${item.mapel || 'Brevet Pajak'}</span>
                                <h4 class="text-lg md:text-xl font-bold mt-1 text-white leading-tight">${item.materi || '-'}</h4>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-white/15 pt-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-amber-400 text-sm">
                                        <i class="far fa-calendar-alt"></i>
                                    </div>
                                    <div>
                                        <p class="text-[9px] text-blue-100 font-bold uppercase tracking-wider">Hari / Tanggal</p>
                                        <p class="text-xs font-bold">${item.tanggal || '-'}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-amber-400 text-sm">
                                        <i class="far fa-clock"></i>
                                    </div>
                                    <div>
                                        <p class="text-[9px] text-blue-100 font-bold uppercase tracking-wider">Waktu Kuliah</p>
                                        <p class="text-xs font-bold">${displayJam} WIB</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-2 flex">
                                ${linkHtml}
                            </div>
                        </div>
                    </div>
                `;
            } else {
                // Secondary class card (listed below) with faded/blurred look to match Teacher theme
                let linkHtml = '';
                if (item.link) {
                    linkHtml = `
                        <a href="${item.link}" target="_blank" onclick="catatAbsenLive(event, '${item.mapel}', '${item.link}')" class="inline-flex items-center gap-1.5 bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 text-xs font-bold py-2 px-4 rounded-xl border border-blue-100 transition duration-200 shadow-sm whitespace-nowrap">
                            <i class="fas fa-video"></i> Join Class
                        </a>
                    `;
                } else {
                    linkHtml = `<span class="text-[10px] text-gray-400 font-bold"><i class="fas fa-link-slash mr-1"></i>Belum diset</span>`;
                }

                cardHtml = `
                    <div class="bg-white border border-gray-100 rounded-3xl p-5 shadow-sm filter blur-[0.8px] opacity-60 hover:blur-none hover:opacity-100 hover:scale-[1.01] hover:shadow-md transition-all duration-300 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl flex-shrink-0 flex items-center justify-center text-blue-600 text-md font-bold">
                                <i class="far fa-calendar-alt"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                    <span class="text-[9px] font-black text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-100">${item.mapel || 'Brevet'}</span>
                                    <span class="text-[9px] text-gray-400 font-bold"><i class="far fa-clock mr-1"></i>${displayJam} WIB</span>
                                    <span class="text-[9px] text-gray-400 font-bold">• Dosen: ${item.dosen || '-'}</span>
                                </div>
                                <h4 class="font-bold text-gray-800 text-xs md:text-sm leading-snug">${item.materi || '-'}</h4>
                                <p class="text-[9px] text-gray-400 mt-1 font-bold"><i class="far fa-calendar-alt mr-1"></i>${item.tanggal || '-'}</p>
                            </div>
                        </div>
                        <div class="w-full sm:w-auto flex justify-end">
                            ${linkHtml}
                        </div>
                    </div>
                `;
            }
            container.insertAdjacentHTML('beforeend', cardHtml);
        });
    }

    function renderMapelGrid(data) {
        const grid = document.getElementById('mapelGrid');
        grid.innerHTML = '';

        LIST_MAPEL.forEach((mapelName, index) => {
            const materiList = data.materi.filter(m => m.mapel === mapelName);
            const tugasList = data.tugas.filter(t => t.mapel === mapelName);
            
            let statusWarna = "bg-gray-100 text-gray-500 border-gray-200";
            let statusText = "Aktif";

            if (tugasList.length > 0) {
                const nilaiSiswa = data.nilai.find(n => n.id_tugas === tugasList[0].id_tugas);
                if (nilaiSiswa) {
                    if (nilaiSiswa.nilai !== "-") {
                        statusText = `Selesai (${nilaiSiswa.nilai})`;
                        statusWarna = "bg-green-50 text-green-700 border-green-200";
                    } else if (nilaiSiswa.link_tugas !== "") {
                        statusText = "Dinilai";
                        statusWarna = "bg-yellow-50 text-yellow-700 border-yellow-200";
                    }
                }
            } else if (materiList.length > 0) {
                statusText = "Materi Aktif";
                statusWarna = "bg-blue-50 text-blue-700 border-blue-200";
            } else {
                statusText = "Kosong";
            }

            let cardHtml = `
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-lg hover:border-blue-200/60 hover:scale-[1.01] transition-all duration-300 flex flex-col h-full">
                    <!-- Card Header -->
                    <div class="p-5 border-b border-gray-50 flex justify-between items-start gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 text-blue-600 flex items-center justify-center font-extrabold flex-shrink-0 text-sm">
                            ${index + 1}
                        </div>
                        <h4 class="font-bold text-gray-800 text-xs flex-1 leading-snug">${mapelName}</h4>
                        <div class="px-2.5 py-0.5 rounded-lg border text-[9px] font-black tracking-wide uppercase ${statusWarna} whitespace-nowrap">
                            ${statusText}
                        </div>
                    </div>
                    
                    <!-- Card Body -->
                    <div class="p-5 flex-1 flex flex-col justify-center space-y-3">
            `;

            if (materiList.length > 0) {
                materiList.forEach(m => {
                    if (m.link_modul) {
                        cardHtml += `
                            <a href="${m.link_modul}" target="_blank" class="flex items-center p-3 rounded-2xl bg-gray-55 hover:bg-blue-50 border border-gray-100 hover:border-blue-100 transition group">
                                <div class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center mr-3 group-hover:bg-blue-600 group-hover:text-white transition">
                                    <i class="far fa-file-pdf"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-gray-700 truncate">${m.judul}</p>
                                    <p class="text-[9px] text-gray-400">Unduh PDF Modul</p>
                                </div>
                                <i class="fas fa-download text-gray-300 group-hover:text-blue-600 text-xs pl-2"></i>
                            </a>
                        `;
                    }
                    if (m.link_youtube) {
                        cardHtml += `
                            <button onclick="openYoutubeModal('${m.link_youtube}', '${mapelName}')" class="w-full flex items-center p-3 rounded-2xl bg-gray-55 hover:bg-blue-50 border border-gray-100 hover:border-blue-100 transition group text-left">
                                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center mr-3 group-hover:bg-rose-600 group-hover:text-white transition shadow-inner">
                                    <i class="fas fa-play text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-gray-700 truncate">Video Rekaman Kelas</p>
                                    <p class="text-[9px] text-gray-400">Tonton & Absen Mandiri</p>
                                </div>
                                <i class="fas fa-chevron-right text-gray-300 group-hover:text-blue-600 text-xs"></i>
                            </button>
                        `;
                    }
                });
            } else {
                cardHtml += `
                    <div class="text-center py-6">
                        <div class="w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-2 text-gray-300">
                            <i class="fas fa-box-open text-sm"></i>
                        </div>
                        <p class="text-[10px] text-gray-455 font-semibold">Materi belum diunggah</p>
                    </div>
                `;
            }

            cardHtml += `</div></div>`;
            grid.insertAdjacentHTML('beforeend', cardHtml);
        });
    }

    function renderTugasTab(data) {
        const container = document.getElementById('tugasListContainer');
        const countBadge = document.getElementById('tugas-count');
        container.innerHTML = '';
        
        let totalTugas = 0;

        LIST_MAPEL.forEach((mapelName, index) => {
            const mapelTugas = data.tugas.filter(t => t.mapel === mapelName);
            totalTugas += mapelTugas.length;

            let mapelBlockHtml = `
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:border-blue-200/60 hover:scale-[1.01] hover:shadow-md transition-all duration-300 flex flex-col h-full">
                    <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-50">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 text-blue-600 border border-blue-150 flex items-center justify-center font-black text-xs flex-shrink-0">
                            ${index + 1}
                        </div>
                        <h4 class="font-bold text-gray-800 text-xs leading-snug flex-1">${mapelName}</h4>
                    </div>
                    <div class="space-y-4 flex-1">
            `;

            if (mapelTugas.length === 0) {
                mapelBlockHtml += `
                    <div class="flex flex-col items-center justify-center py-6 text-gray-400 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                        <i class="far fa-clipboard text-lg mb-1 text-gray-300"></i>
                        <p class="text-[11px] font-medium text-gray-400">Belum ada tugas</p>
                    </div>
                `;
            } else {
                mapelTugas.forEach(tugas => {
                    const nilaiSiswa = data.nilai.find(n => n.id_tugas === tugas.id_tugas);
                    const hasLink = nilaiSiswa && nilaiSiswa.link_tugas && nilaiSiswa.link_tugas.trim() !== '';
                    const hasNilai = nilaiSiswa && nilaiSiswa.nilai && nilaiSiswa.nilai !== '-';

                    let statusBadge = '';
                    let actionHtml = '';

                    if (hasNilai) {
                        statusBadge = `
                            <span class="inline-flex items-center gap-1 bg-green-50 text-green-700 px-2.5 py-1 rounded-lg border border-green-150 text-[10px] font-black">
                                <i class="fas fa-check-circle"></i> Nilai: ${nilaiSiswa.nilai}
                            </span>
                        `;
                        actionHtml = `
                            <div class="flex flex-wrap items-center justify-between gap-2 mt-3 pt-3 border-t border-gray-100/70 text-xs">
                                <span class="text-gray-400 font-semibold">Status: Dinilai</span>
                                <a href="${nilaiSiswa.link_tugas}" target="_blank" class="text-blue-600 hover:text-indigo-800 font-bold flex items-center gap-1">
                                    <i class="fas fa-external-link-alt"></i> Lihat Tugas Saya
                                </a>
                            </div>
                        `;
                    } else if (hasLink) {
                        statusBadge = `
                            <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 px-2.5 py-1 rounded-lg border border-amber-150 text-[10px] font-black animate-pulse">
                                <i class="fas fa-history"></i> Proses Penilaian
                            </span>
                        `;
                        actionHtml = `
                            <div class="flex flex-wrap items-center justify-between gap-2 mt-3 pt-3 border-t border-gray-100/70 text-xs">
                                <span class="text-gray-400 font-semibold">Terkumpul</span>
                                <a href="${nilaiSiswa.link_tugas}" target="_blank" class="text-blue-600 hover:text-indigo-800 font-bold flex items-center gap-1">
                                    <i class="fas fa-external-link-alt"></i> Lihat File
                                </a>
                            </div>
                        `;
                    } else {
                        statusBadge = `
                            <span class="inline-flex items-center gap-1 bg-rose-50 text-rose-700 px-2.5 py-1 rounded-lg border border-rose-150 text-[10px] font-black">
                                <i class="fas fa-exclamation-circle"></i> Belum Mengumpulkan
                            </span>
                        `;
                        actionHtml = `
                            <button onclick="openTugasModal('${tugas.id_tugas}', '${tugas.judul}', '${tugas.deskripsi}', '${tugas.link_soal}')" class="w-full mt-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-md transition flex items-center justify-center gap-1.5">
                                <i class="fas fa-cloud-upload-alt animate-bounce"></i> Unggah File Tugas (PDF)
                            </button>
                        `;
                    }

                    let deadlineText = tugas.deadline || 'Tidak ada';
                    if (tugas.deadline && tugas.deadline.includes('T')) {
                        const d = new Date(tugas.deadline);
                        if (!isNaN(d.getTime())) {
                            deadlineText = d.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                        }
                    }

                    let linkSoalHtml = '';
                    if (tugas.link_soal) {
                        linkSoalHtml = `
                            <a href="${tugas.link_soal}" target="_blank" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-indigo-800 font-bold mt-2">
                                <i class="fas fa-external-link-alt"></i> Unduh Berkas Soal
                            </a>
                        `;
                    }

                    mapelBlockHtml += `
                        <div class="p-4 rounded-2xl bg-gray-50/50 border border-gray-105 hover:bg-white hover:border-blue-100 hover:shadow-sm transition-all duration-205">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h5 class="font-bold text-gray-800 text-xs truncate">${tugas.judul}</h5>
                                    <p class="text-[9px] text-gray-400 mt-0.5"><i class="far fa-calendar-alt mr-1"></i> Batas Pengumpulan: ${deadlineText}</p>
                                </div>
                                ${statusBadge}
                            </div>
                            
                            ${tugas.deskripsi ? `<p class="text-[10px] text-gray-500 leading-relaxed bg-white p-2.5 rounded-xl border border-gray-100 mt-2.5">${tugas.deskripsi}</p>` : ''}
                            
                            ${linkSoalHtml}
                            ${actionHtml}
                        </div>
                    `;
                });
            }

            mapelBlockHtml += `
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', mapelBlockHtml);
        });

        countBadge.innerText = `${totalTugas} Tugas`;
    }

    function renderNilaiTab(data) {
        const body = document.getElementById('nilai-table-body');
        const countBadge = document.getElementById('nilai-count');
        body.innerHTML = '';
        
        let completedCount = 0;
        
        if (!data.nilai || data.nilai.length === 0) {
            body.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-10 text-gray-400">
                        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-2 text-gray-300">
                            <i class="fas fa-receipt text-xl"></i>
                        </div>
                        <p class="text-xs">Belum ada data nilai yang tercatat di sistem.</p>
                    </td>
                </tr>
            `;
            countBadge.innerText = '0 Tugas Selesai';
            return;
        }

        data.nilai.forEach(item => {
            if (item.nilai !== '-') {
                completedCount++;
            }

            const tugasDetail = data.tugas.find(t => t.id_tugas === item.id_tugas);
            const mapelName = tugasDetail ? tugasDetail.mapel : 'Brevet Program';
            const tugasTitle = tugasDetail ? tugasDetail.judul : item.id_tugas;

            let fileLinkHtml = '<span class="text-gray-400 text-xs">-</span>';
            if (item.link_tugas) {
                fileLinkHtml = `
                    <a href="${item.link_tugas}" target="_blank" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-805 font-bold text-xs">
                        <i class="fas fa-file-pdf"></i> Lihat File
                    </a>
                `;
            }

            let nilaiBadgeClass = 'bg-gray-50 text-gray-500 border-gray-150';
            let displayNilai = item.nilai;

            if (item.nilai !== '-') {
                const score = parseInt(item.nilai);
                if (score >= 80) {
                    nilaiBadgeClass = 'bg-green-50 text-green-700 border-green-200';
                } else if (score >= 70) {
                    nilaiBadgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                } else {
                    nilaiBadgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                }
            } else if (item.link_tugas) {
                nilaiBadgeClass = 'bg-yellow-50 text-yellow-700 border-yellow-250 animate-pulse';
                displayNilai = 'Proses';
            }

            const row = `
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="py-4 pl-4 font-semibold text-gray-800 text-xs">${item.id_tugas}</td>
                    <td class="py-4">
                        <p class="font-bold text-blue-700 text-xs">${tugasTitle}</p>
                        <p class="text-[9px] text-gray-400">${mapelName}</p>
                    </td>
                    <td class="py-4">${fileLinkHtml}</td>
                    <td class="py-4">
                        <span class="inline-block px-3 py-0.5 rounded-lg border text-[10px] font-black ${nilaiBadgeClass}">
                            ${displayNilai}
                        </span>
                    </td>
                    <td class="py-4 pr-4 text-xs text-gray-500 italic max-w-xs truncate" title="${item.komentar || item.feedback || '-'}">
                        ${item.komentar || item.feedback || '-'}
                    </td>
                </tr>
            `;
            body.insertAdjacentHTML('beforeend', row);
        });

        countBadge.innerText = `${completedCount} Tugas Selesai`;
    }

    function normalizeMapelJS(rawName) {
        if (!rawName) return "";
        const name = String(rawName).trim().toLowerCase();
        
        if (name.includes("kup")) return "Ketentuan Umum dan Tata Cara Perpajakan (KUP) A & B";
        if (name.includes("orang pribadi") || name.includes("pph op")) return "Pajak Penghasilan (PPh) Orang Pribadi";
        if (name.includes("21")) return "Pajak Pemotongan dan Pemungutan (PPh Pasal 21)";
        if (name.includes("22") || name.includes("23") || name.includes("26") || name.includes("4(2)") || name.includes("4 (2)") || name.includes("potput")) return "Pajak Pemotongan dan Pemungutan (PPh Pasal 22, 23, 26, & 4(2))";
        if (name.includes("badan")) return "Pajak Penghasilan (PPh) Badan";
        if (name.includes("ppn") || name.includes("ppnbm")) return "Pajak Pertambahan Nilai (PPN) dan PPnBM A & B";
        if (name.includes("pbb") || name.includes("bphtb") || name.includes("meterai") || name.includes("metrai")) return "Pajak Bumi dan Bangunan (PBB), BPHTB, & Bea Meterai";
        if (name.includes("akuntansi")) return "Akuntansi Perpajakan";
        if (name.includes("pemeriksaan") || name.includes("penyidikan")) return "Pemeriksaan dan Penyidikan Pajak";
        if (name.includes("spt") || name.includes("faktur") || name.includes("efaktur") || name.includes("aplikasi")) return "Pengisian e-SPT / Aplikasi Perpajakan (e-Faktur, dll)";
        if (name.includes("planning") || name.includes("perencanaan")) return "Tax Planning (Perencanaan Pajak)";
        if (name.includes("ujian") || name.includes("komprehensif") || name.includes("kelulusan") || name.includes("simulasi")) return "Ujian Kelulusan / Komprehensif Brevet";

        return rawName;
    }

    function renderAbsensiTab(data) {
        const body = document.getElementById('absensi-table-body');
        const countText = document.getElementById('totalPresenceCount');
        const percentText = document.getElementById('presencePercentText');
        const subjectListContainer = document.getElementById('subjectPresenceList');
        if (!body || !countText || !percentText) return;
        body.innerHTML = '';
        if (subjectListContainer) subjectListContainer.innerHTML = '';

        const defaultTargets = {
            "Ketentuan Umum dan Tata Cara Perpajakan (KUP) A & B": 6,
            "Pajak Penghasilan (PPh) Orang Pribadi": 6,
            "Pajak Pemotongan dan Pemungutan (PPh Pasal 21)": 4,
            "Pajak Pemotongan dan Pemungutan (PPh Pasal 22, 23, 26, & 4(2))": 4,
            "Pajak Penghasilan (PPh) Badan": 6,
            "Pajak Pertambahan Nilai (PPN) dan PPnBM A & B": 6,
            "Pajak Bumi dan Bangunan (PBB), BPHTB, & Bea Meterai": 4,
            "Akuntansi Perpajakan": 6,
            "Pemeriksaan dan Penyidikan Pajak": 4,
            "Pengisian e-SPT / Aplikasi Perpajakan (e-Faktur, dll)": 4,
            "Tax Planning (Perencanaan Pajak)": 4,
            "Ujian Kelulusan / Komprehensif Brevet": 1
        };

        const mapelList = Object.keys(defaultTargets);

        // Compute session targets per mapel (from data.materi)
        const mapelTargets = {};
        mapelList.forEach(m => {
            mapelTargets[m] = defaultTargets[m];
        });

        const rawMateri = Array.isArray(data.materi) ? data.materi : [];
        const mapelMateriCounts = {};
        rawMateri.forEach(m => {
            if (m && m.mapel) {
                const normName = normalizeMapelJS(m.mapel);
                if (normName) {
                    if (!mapelMateriCounts[normName]) {
                        mapelMateriCounts[normName] = 0;
                    }
                    mapelMateriCounts[normName]++;
                }
            }
        });
        Object.keys(mapelMateriCounts).forEach(name => {
            if (mapelMateriCounts[name] > 0 && mapelTargets[name] !== undefined) {
                mapelTargets[name] = mapelMateriCounts[name];
            }
        });

        // Initialize hadir count per mapel
        const mapelHadirCounts = {};
        mapelList.forEach(m => {
            mapelHadirCounts[m] = 0;
        });

        if (!data.absensi || data.absensi.length === 0) {
            body.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-10 text-gray-400">
                        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-2 text-gray-300">
                            <i class="fas fa-user-check text-xl"></i>
                        </div>
                        <p class="text-xs">Belum ada riwayat kehadiran tercatat.</p>
                    </td>
                </tr>
            `;
            countText.innerText = '0 Sesi';
            percentText.innerText = '0%';
            
            // Still render subject list with 0 check-ins
            renderSubjectProgress(mapelTargets, mapelHadirCounts);
            return;
        }

        // Deduplicate attendance by date (YYYY-MM-DD) per mapel
        const seenDates = new Set();
        let totalHadirCount = 0;

        data.absensi.forEach(item => {
            if (!item || !item.timestamp || !item.mapel) return;
            
            const mapel = normalizeMapelJS(item.mapel);

            const dateStr = item.timestamp.substring(0, 10); // YYYY-MM-DD
            const key = `${dateStr}_${mapel}`;
            if (!seenDates.has(key)) {
                seenDates.add(key);
                if (!mapelHadirCounts[mapel]) {
                    mapelHadirCounts[mapel] = 0;
                }
                mapelHadirCounts[mapel]++;
                totalHadirCount++;
            }

            // Format date nicely
            let formattedDate = item.timestamp;
            try {
                const d = new Date(item.timestamp);
                if (!isNaN(d.getTime())) {
                    formattedDate = d.toLocaleDateString('id-ID', {
                        weekday: 'short',
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    }) + ' - ' + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                }
            } catch (e) {}

            const row = `
                <tr class="hover:bg-gray-50/50 transition border-b border-gray-50">
                    <td class="py-3.5 pl-4 font-semibold text-gray-800">${formattedDate}</td>
                    <td class="py-3.5">
                        <p class="font-bold text-blue-700">${item.mapel}</p>
                    </td>
                    <td class="py-3.5 text-gray-500 font-medium">${item.metode}</td>
                    <td class="py-3.5 pr-4">
                        <span class="inline-block px-2.5 py-0.5 rounded-full bg-green-50 text-green-700 border border-green-150 text-[9px] font-black">
                            ${item.status || 'HADIR'}
                        </span>
                    </td>
                </tr>
            `;
            body.insertAdjacentHTML('beforeend', row);
        });

        // Compute total target sessions from subjects
        let totalTargetSesi = 0;
        mapelList.forEach(m => {
            totalTargetSesi += mapelTargets[m];
        });

        countText.innerText = `${totalHadirCount} Sesi`;
        if (totalTargetSesi > 0) {
            const pct = Math.round((totalHadirCount / totalTargetSesi) * 100);
            percentText.innerText = `${pct}%`;
        } else {
            percentText.innerText = '0%';
        }

        renderSubjectProgress(mapelTargets, mapelHadirCounts);

        function renderSubjectProgress(targets, presence) {
            if (!subjectListContainer) return;
            
            const abbreviations = {
                "Ketentuan Umum dan Tata Cara Perpajakan (KUP) A & B": "KUP",
                "Pajak Penghasilan (PPh) Orang Pribadi": "PPh OP",
                "Pajak Pemotongan dan Pemungutan (PPh Pasal 21)": "PPh 21",
                "Pajak Pemotongan dan Pemungutan (PPh Pasal 22, 23, 26, & 4(2))": "PPh 22-26",
                "Pajak Penghasilan (PPh) Badan": "PPh Badan",
                "Pajak Pertambahan Nilai (PPN) dan PPnBM A & B": "PPN",
                "Pajak Bumi dan Bangunan (PBB), BPHTB, & Bea Meterai": "PBB",
                "Akuntansi Perpajakan": "Akuntansi",
                "Pemeriksaan dan Penyidikan Pajak": "Pemeriksaan",
                "Pengisian e-SPT / Aplikasi Perpajakan (e-Faktur, dll)": "e-SPT",
                "Tax Planning (Perencanaan Pajak)": "Tax Planning",
                "Ujian Kelulusan / Komprehensif Brevet": "Ujian"
            };

            mapelList.forEach(m => {
                const target = targets[m] || 0;
                const hadir = presence[m] || 0;
                const abbrev = abbreviations[m] || m;
                const percentage = target > 0 ? Math.round((hadir / target) * 100) : 0;

                const cardHtml = `
                    <div class="flex flex-col gap-1.5 pb-2 border-b border-gray-50 last:border-0 last:pb-0">
                        <div class="flex justify-between items-center text-[10px] font-semibold text-gray-700">
                            <span class="truncate max-w-[150px]" title="${m}">${abbrev}</span>
                            <span class="text-blue-700 font-bold bg-blue-50 px-1.5 py-0.5 rounded text-[9px]">${hadir}/${target}</span>
                        </div>
                        <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-blue-600 h-1.5 rounded-full" style="width: ${percentage}%"></div>
                        </div>
                    </div>
                `;
                subjectListContainer.insertAdjacentHTML('beforeend', cardHtml);
            });
        }
    }

    // --- ABSENSI & YOUTUBE ---
    
    function getIdYT(url) {
        let regex = /(youtu.*be.*)\/(watch\?v=|embed\/|v|shorts|)(.*?((?=[&#?])|$))/gm;
        let found = regex.exec(url);
        return (found && found.length > 3) ? found[3] : url;
    }

    let currentMapelYT = "";
    
    function openYoutubeModal(link, mapel) {
        currentMapelYT = mapel;
        const ytId = getIdYT(link);
        document.getElementById('ytIframe').src = `https://www.youtube.com/embed/${ytId}?autoplay=1`;
        document.getElementById('ytModalTitle').innerText = "Rekaman Kelas: " + mapel;
        document.getElementById('youtubeModal').classList.remove('hidden');
        document.getElementById('youtubeModalContent').classList.remove('scale-95');
        document.getElementById('youtubeModalContent').classList.add('scale-100');
    }

    function closeYoutubeModal() {
        document.getElementById('ytIframe').src = "";
        document.getElementById('youtubeModalContent').classList.remove('scale-100');
        document.getElementById('youtubeModalContent').classList.add('scale-95');
        setTimeout(() => document.getElementById('youtubeModal').classList.add('hidden'), 200);
    }

    document.getElementById('btnConfirmNonton').addEventListener('click', async function() {
        const btn = this;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses Absensi...';
        btn.disabled = true;

        try {
            const res = await fetch("{{ route('siswa.api.absen') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ mapel: currentMapelYT, metode: "Nonton Rekaman YouTube" })
            });
            const result = await res.json();
            
            if (result.status === 'success') {
                Swal.fire({
                    title: 'Berhasil Absen!',
                    text: 'Kehadiran Anda menonton rekaman kelas berhasil dicatat.',
                    icon: 'success',
                    confirmButtonColor: '#2563eb'
                });
                closeYoutubeModal();
                loadDashboardData();
            } else {
                Swal.fire({
                    title: 'Gagal',
                    text: result.message || 'Terjadi kesalahan teknis.',
                    icon: 'error',
                    confirmButtonColor: '#2563eb'
                });
            }
        } catch (error) {
            Swal.fire({
                title: 'Error',
                text: 'Koneksi ke server terputus.',
                icon: 'error',
                confirmButtonColor: '#2563eb'
            });
        } finally {
            btn.innerHTML = '<i class="fas fa-award"></i> Konfirmasi Telah Menonton Rekaman';
            btn.disabled = false;
        }
    });

    async function catatAbsenLive(e, mapel, linkUrl) {
        console.log("Mencatat absensi background untuk Live Zoom mapel:", mapel);
        try {
            await fetch("{{ route('siswa.api.absen') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ mapel: mapel, metode: "Live Zoom" })
            });
            setTimeout(() => {
                loadDashboardData();
            }, 600);
        } catch (error) {
            console.warn("Gagal mencatat absensi Live Zoom di background:", error);
        }
    }

    // --- UPLOAD TUGAS PROCESS ---
    let lastOpenedTaskId = "";

    function openTugasModal(idTugas, judul, desc, linkSoal) {
        // Jika membuka tugas yang berbeda, reset form
        if (lastOpenedTaskId !== idTugas) {
            document.getElementById('formUploadTugas').reset();
            const nameDisplay = document.getElementById('selectedFileNameDisplay');
            if (nameDisplay) {
                nameDisplay.innerText = "";
                nameDisplay.classList.add('hidden');
            }
            document.getElementById('fileError').innerText = "";
        }
        lastOpenedTaskId = idTugas;

        document.getElementById('tugasModal').classList.remove('hidden');
        document.getElementById('inputIdTugas').value = idTugas;
        document.getElementById('tugasModalTitle').innerText = judul;
        document.getElementById('tugasDesc').innerText = desc || "Silakan kerjakan tugas sesuai instruksi dan unggah file tugas (PDF, Excel, Word, ZIP, atau file lain).";
        
        const link = document.getElementById('tugasLinkSoal');
        if (linkSoal && linkSoal.trim() !== '') {
            link.href = linkSoal;
            link.classList.remove('hidden');
            link.classList.add('inline-block');
        } else {
            link.classList.add('hidden');
        }
    }

    function closeTugasModal() {
        document.getElementById('tugasModal').classList.add('hidden');
    }

    function displaySelectedFile(input, displayId) {
        const display = document.getElementById(displayId);
        if (input.files && input.files.length > 0) {
            display.innerText = "File terpilih: " + input.files[0].name;
            display.classList.remove('hidden');
        } else {
            display.innerText = "";
            display.classList.add('hidden');
        }
    }

    function submitTugasProcess() {
        const fileInput = document.getElementById('inputFileTugas');
        const linkInput = document.getElementById('inputLinkTugas');
        
        const hasFile = fileInput.files.length > 0;
        const hasLink = linkInput.value.trim() !== "";

        if (!hasFile && !hasLink) {
            document.getElementById('fileError').innerText = "Harap unggah file atau masukkan link tugas terlebih dahulu.";
            return;
        }

        document.getElementById('fileError').innerText = "";
        const btn = document.getElementById('btnSubmitTugas');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mengirim Tugas...';
        btn.disabled = true;

        const idTugas = document.getElementById('inputIdTugas').value;

        // Callback sukses kirim tugas
        const sendPayload = async (payload) => {
            try {
                const res = await fetch("{{ route('siswa.api.submit_tugas') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(payload)
                });
                
                const result = await res.json();
                
                if (result.status === 'success') {
                    Swal.fire({
                        title: 'Tugas Terkirim!',
                        text: 'Tugas Anda berhasil diserahkan dan tercatat di Sheets.',
                        icon: 'success',
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        closeTugasModal();
                        
                        // Reload data
                        document.getElementById('lmsContent').style.display = 'none';
                        document.getElementById('lmsLoader').style.display = 'flex';
                        loadDashboardData();
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal Kirim',
                        text: result.message || 'Terjadi kesalahan sistem.',
                        icon: 'error',
                        confirmButtonColor: '#2563eb'
                    });
                }
            } catch (error) {
                Swal.fire({
                    title: 'Time Out / Error',
                    text: 'Gagal mengirim tugas. Coba lagi dalam beberapa saat.',
                    icon: 'error',
                    confirmButtonColor: '#2563eb'
                });
            } finally {
                btn.innerHTML = '<i class="fas fa-cloud-upload-alt mr-2"></i> Kirim Tugas';
                btn.disabled = false;
            }
        };

        if (hasFile) {
            const file = fileInput.files[0];
            
            // Validate file extension against blacklist
            const blacklist = ['php', 'phtml', 'php3', 'php4', 'php5', 'html', 'htm', 'js', 'jsp', 'asp', 'aspx', 'sh', 'exe', 'pl', 'cgi', 'htaccess'];
            const fileExt = file.name.split('.').pop().toLowerCase();
            if (blacklist.includes(fileExt)) {
                document.getElementById('fileError').innerText = "Format file tidak diperbolehkan demi keamanan sistem.";
                btn.innerHTML = '<i class="fas fa-cloud-upload-alt mr-2"></i> Kirim Tugas';
                btn.disabled = false;
                return;
            }
            
            // Limit size ke 30MB
            if (file.size > 30 * 1024 * 1024) {
                document.getElementById('fileError').innerText = "Ukuran file terlalu besar! Maksimal 30MB.";
                btn.innerHTML = '<i class="fas fa-cloud-upload-alt mr-2"></i> Kirim Tugas';
                btn.disabled = false;
                return;
            }

            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = async function() {
                const base64Data = reader.result.split(',')[1];
                const payload = {
                    id_tugas: idTugas,
                    base64: base64Data,
                    fileName: file.name,
                    mimeType: file.type,
                    link_tugas: linkInput.value.trim()
                };
                sendPayload(payload);
            };
        } else {
            // Hanya kirim link saja
            const payload = {
                id_tugas: idTugas,
                base64: "",
                fileName: "",
                mimeType: "",
                link_tugas: linkInput.value.trim()
            };
            sendPayload(payload);
        }
    }

    // Toggle tampilan komentar pengumuman
    function toggleComments(id) {
        const container = document.getElementById(`comments-container-${id}`);
        const icon = document.getElementById(`comment-icon-${id}`);
        if (container.classList.contains('hidden')) {
            container.classList.remove('hidden');
            icon.classList.add('rotate-180');
        } else {
            container.classList.add('hidden');
            icon.classList.remove('rotate-180');
        }
    }
</script>

<style>
    .animate-blob { animation: blob 7s infinite; }
    .animation-delay-2000 { animation-delay: 2s; }
    @keyframes blob {
        0% { transform: translate(0px, 0px) scale(1); }
        33% { transform: translate(25px, -40px) scale(1.05); }
        66% { transform: translate(-15px, 15px) scale(0.95); }
        100% { transform: translate(0px, 0px) scale(1); }
    }
    
    .bg-gray-150 {
        background-color: rgba(243, 244, 246, 0.7);
    }
</style>

@endsection