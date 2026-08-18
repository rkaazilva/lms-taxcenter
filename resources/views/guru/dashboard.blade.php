@extends('layouts.main')

@section('title', 'Panel Guru')
@section('page_title', 'Panel Kerja Guru & Dosen')

@section('content')

<!-- NOTIFIKASI SUKSES / GAGAL -->
@if(session('success'))
<div class="bg-green-55 hover:bg-green-100 border border-green-200 text-green-800 p-4 rounded-2xl shadow-sm mb-6 flex items-center gap-3 transition">
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
<div class="bg-red-55 hover:bg-red-100 border border-red-200 text-red-800 p-4 rounded-2xl shadow-sm mb-6 flex items-center gap-3 transition">
    <div class="w-8 h-8 rounded-full bg-red-500 text-white flex items-center justify-center flex-shrink-0">
        <i class="fas fa-exclamation-triangle"></i>
    </div>
    <div>
        <p class="font-bold text-sm">Gagal!</p>
        <p class="text-xs text-red-700">{{ session('error') }}</p>
    </div>
</div>
@endif

@if($errors->any())
<div class="bg-red-55 hover:bg-red-100 border border-red-200 text-red-800 p-4 rounded-2xl shadow-sm mb-6 flex items-center gap-3 transition">
    <div class="w-8 h-8 rounded-full bg-red-500 text-white flex items-center justify-center flex-shrink-0">
        <i class="fas fa-exclamation-triangle"></i>
    </div>
    <div>
        <p class="font-bold text-sm">Gagal Validasi Form!</p>
        <ul class="text-xs text-red-700 list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<!-- TAB: DASHBOARD -->
<div id="tab-dashboard" class="tab-content block space-y-6">

<!-- HEADER BANNER -->
<div class="mb-8">
    <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-3xl p-8 text-white relative overflow-hidden shadow-xl border border-emerald-400/20">
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-emerald-400 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
        <div class="relative z-10">
            <h2 class="text-2xl font-bold mb-2">Selamat datang di Panel Guru, {{ session('nama', 'Dosen') }}! 👋</h2>
            <p class="text-emerald-100 text-xs leading-relaxed max-w-2xl">Panel ini didesain khusus untuk memudahkan Bapak/Ibu Dosen dalam mengunggah modul pelatihan (PDF), membagikan link video rekaman Youtube, serta membuat & menilai daftar tugas siswa secara instan ke sistem database.</p>
        </div>
    </div>
</div>

<!-- QUICK ACTIONS & STATS -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <!-- Action 1: Tambah Materi -->
    <button onclick="openMateriModal()" class="group text-left bg-white p-6 rounded-3xl border border-gray-150 hover:border-emerald-200 shadow-sm hover:shadow-md transition duration-300 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-emerald-50 text-emerald-650 rounded-2xl flex items-center justify-center text-xl group-hover:bg-emerald-600 group-hover:text-white transition duration-300 shadow-inner">
                <i class="fas fa-file-pdf"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-800 text-sm group-hover:text-emerald-700 transition">Unggah Materi & Rekaman</h4>
                <p class="text-xs text-gray-400 mt-0.5">Tambah modul PDF atau link rekaman YouTube baru</p>
            </div>
        </div>
        <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-emerald-50 group-hover:text-emerald-600 transition">
            <i class="fas fa-plus text-xs"></i>
        </div>
    </button>

    <!-- Action 2: Buat Tugas -->
    <button onclick="openTugasModal()" class="group text-left bg-white p-6 rounded-3xl border border-gray-150 hover:border-emerald-200 shadow-sm hover:shadow-md transition duration-300 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-emerald-50 text-emerald-650 rounded-2xl flex items-center justify-center text-xl group-hover:bg-emerald-600 group-hover:text-white transition duration-300 shadow-inner">
                <i class="fas fa-tasks"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-800 text-sm group-hover:text-emerald-700 transition">Buat Tugas Baru</h4>
                <p class="text-xs text-gray-400 mt-0.5">Definisikan instruksi tugas dan tanggal deadline</p>
            </div>
        </div>
        <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-emerald-50 group-hover:text-emerald-600 transition">
            <i class="fas fa-plus text-xs"></i>
        </div>
    </button>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold text-gray-850 text-sm">Jadwal Kelas & Link Pertemuan</h3>
                <p class="text-xs text-gray-400 mt-1">Menampilkan jadwal terbaru dari Google Sheets.</p>
            </div>
            <span class="text-[10px] bg-emerald-50 text-emerald-700 px-2 py-1 rounded-full border border-emerald-100">Real-time</span>
        </div>
        <div class="space-y-3">
            @if(!empty($nextSession))
                @php
                    $nextJam = $nextSession['jam'] ?? '-';
                    if ($nextJam !== '-') {
                        $cleanedJam = trim(str_ireplace('WIB', '', $nextJam));
                        $cleanedJam = str_replace('.', ':', $cleanedJam);
                        $timeObj = strtotime($cleanedJam);
                        if ($timeObj !== false) {
                            $nextJam = date('H:i', $timeObj);
                        }
                    }
                @endphp
                <!-- Sesi Zoom Terdekat (Featured Popout Card) -->
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-800 text-white p-6 shadow-xl border border-emerald-400/20 mb-5 transform hover:scale-[1.02] transition-all duration-300 shadow-emerald-500/15">
                    <div class="absolute -right-10 -top-10 w-28 h-28 bg-emerald-500 rounded-full mix-blend-multiply filter blur-2xl opacity-45"></div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                        </span>
                        <p class="text-[9px] uppercase tracking-widest text-emerald-100 font-black">Sesi Terdekat / Sesi Aktif</p>
                    </div>
                    <h4 class="text-sm font-black leading-snug text-white drop-shadow-sm">{{ $nextSession['materi'] ?? ($nextSession['mapel'] ?? 'Pertemuan Berikutnya') }}</h4>
                    <p class="text-[10px] text-emerald-100/90 mt-2 font-medium"><i class="fas fa-calendar-alt mr-1.5 text-emerald-300"></i>{{ $nextSession['tanggal'] ?? ($nextSession['date'] ?? '-') }} • {{ $nextJam }} WIB</p>
                    <p class="text-[10px] text-emerald-200 mt-1 font-medium"><i class="fas fa-user-tie mr-1.5 text-emerald-300"></i>Dosen: {{ $nextSession['dosen'] ?? '-' }}</p>
                    <div class="mt-4 flex gap-2 flex-wrap">
                        @if(!empty($nextSession['link']))
                            <a href="{{ $nextSession['link'] }}" target="_blank" class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl bg-white hover:bg-emerald-50 text-emerald-800 py-2.5 px-3 text-xs font-black shadow-md transition duration-200"><i class="fas fa-video text-xs"></i> Join Zoom</a>
                            <button type="button" data-link="{{ $nextSession['link'] }}" onclick="copyZoomLink(this.dataset.link)" class="rounded-xl border border-emerald-500/30 bg-emerald-700/40 hover:bg-emerald-700/60 text-white py-2.5 px-3 text-xs font-semibold transition duration-200"><i class="fas fa-copy"></i> Salin Link</button>
                        @else
                            <span class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl border border-white/20 bg-white/10 text-emerald-100 py-2.5 px-3 text-xs font-bold"><i class="fas fa-video-slash"></i> Tautan Zoom Belum Tersedia</span>
                        @endif
                    </div>
                </div>
            @endif

            @if(!empty($jadwalKhusus) && count($jadwalKhusus) > 0)
                <h4 class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-4 mb-2">Jadwal Sesi Lainnya</h4>
                <div class="space-y-2.5">
                    @foreach(array_slice($jadwalKhusus, 0, 5) as $item)
                        {{-- Skip jika sama dengan sesi terdekat agar tidak duplikat --}}
                        @if(!empty($nextSession) && ($item['tanggal'] === $nextSession['tanggal'] && ($item['jam'] ?? '') === ($nextSession['jam'] ?? '') && ($item['dosen'] ?? '') === ($nextSession['dosen'] ?? '')))
                            @continue
                        @endif
                        @php
                            $itemJam = $item['jam'] ?? '-';
                            if ($itemJam !== '-') {
                                $cleanedJam = trim(str_ireplace('WIB', '', $itemJam));
                                $cleanedJam = str_replace('.', ':', $cleanedJam);
                                $timeObj = strtotime($cleanedJam);
                                if ($timeObj !== false) {
                                    $itemJam = date('H:i', $timeObj);
                                }
                            }
                        @endphp
                        <div class="p-3 bg-slate-50/70 border border-gray-100 rounded-2xl hover:bg-white hover:border-emerald-150 hover:shadow-sm transition-all duration-300 filter blur-[0.8px] opacity-45 hover:blur-none hover:opacity-100 cursor-pointer">
                            <p class="text-[9px] text-gray-400">{{ $item['tanggal'] ?? ($item['date'] ?? '-') }} • {{ $itemJam }} WIB</p>
                            <h4 class="text-xs font-bold text-gray-700 mt-0.5 truncate">{{ $item['materi'] ?? ($item['mapel'] ?? '-') }}</h4>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-[9px] text-gray-400">Dosen: {{ $item['dosen'] ?? '-' }}</span>
                                @if(!empty($item['link']))
                                <a href="{{ $item['link'] }}" target="_blank" class="inline-flex items-center gap-1 text-[9px] text-emerald-650 hover:text-emerald-700 font-semibold">Buka Link <i class="fas fa-external-link-alt text-[8px]"></i></a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                @if(count($jadwalKhusus) > 5)
                    <p class="text-[9px] text-gray-400 mt-2 italic text-center">Menampilkan 5 teratas dari {{ count($jadwalKhusus) }} entri.</p>
                @endif
            @else
                @if(empty($nextSession))
                    <div class="text-center py-12 text-gray-400">
                        <i class="fas fa-calendar-times text-2xl mb-2"></i>
                        <p class="text-xs">Tidak ada jadwal kelas yang tersedia saat ini.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold text-gray-850 text-sm">Ringkasan Materi & Tugas</h3>
                <p class="text-xs text-gray-400 mt-1">Statistik cepat untuk aktivitas bapak/ibu.</p>
            </div>
            <span class="text-[10px] bg-yellow-50 text-yellow-750 px-2 py-1 rounded-full border border-yellow-100">Update</span>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-100">
                <p class="text-[10px] text-emerald-700 uppercase tracking-wider">Materi</p>
                <p class="text-2xl font-bold text-emerald-950 mt-2">{{ count($materi) }}</p>
            </div>
            <div class="bg-yellow-50 p-4 rounded-2xl border border-yellow-100">
                <p class="text-[10px] text-yellow-750 uppercase tracking-wider">Tugas</p>
                <p class="text-2xl font-bold text-yellow-950 mt-2">{{ count($tugas) }}</p>
            </div>
        </div>
        @php
            $soonCount = 0;
            foreach ($tugas as $t) {
                if (!empty($t['deadline']) && strtotime($t['deadline']) <= strtotime('+7 days')) {
                    $soonCount++;
                }
            }
        @endphp
        <div class="mt-5 p-4 rounded-2xl bg-gray-50 border border-gray-100">
            <p class="text-[10px] text-gray-500">Tanpa notifikasi otomatis saat ini.</p>
            <p class="text-sm font-semibold text-gray-700 mt-2">{{ $soonCount }} tugas mendekati deadline dalam 7 hari</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-gray-850 text-sm">Manajemen Penilaian</h3>
                    <p class="text-xs text-gray-400 mt-1">Kelola umpan balik dan nilai tugas siswa.</p>
                </div>
                <span class="text-[10px] bg-emerald-50 text-emerald-700 px-2 py-1 rounded-full border border-emerald-100 font-semibold">LMS Portal</span>
            </div>
            <div class="space-y-3 text-[10px] text-gray-600 leading-relaxed">
                <p>✓ Rekap nilai khusus siswa ditampilkan secara otomatis di dashboard peserta.</p>
                <p>✓ Penilaian dan feedback dilakukan melalui tab <strong>Penilaian</strong> di LMS agar tersinkronisasi secara aman.</p>
            </div>
        </div>
        <div class="mt-6">
            <button onclick="switchTab('penilaian'); if (typeof window.syncSidebarActiveState === 'function') window.syncSidebarActiveState('penilaian');" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 text-xs font-bold transition duration-200 shadow-sm shadow-emerald-500/10">
                <i class="fas fa-star mr-1"></i> Buka Menu Penilaian
            </button>
        </div>
    </div>
</div>
<!-- END SUMMARY CARDS SECTION -->

<!-- DYNAMIC STATISTICS GRID (DOUGHNUT CARDS ROW) -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-8">
    <!-- Doughnut Chart Card 1 (Kehadiran) -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-between min-h-[320px]">
        <div>
            <h3 class="font-bold text-gray-850 text-sm mb-1">Status Kehadiran Siswa</h3>
            <p class="text-[11px] text-gray-400 mb-4">Proporsi siswa dengan persentase kehadiran aman vs butuh perhatian.</p>
        </div>
        <div class="relative flex items-center justify-center min-h-[180px]">
            <canvas id="attendanceDoughnutChart" class="max-w-[160px] max-h-[160px]"></canvas>
        </div>
        <div class="flex justify-around items-center mt-4 text-[10px] text-gray-600 font-medium">
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                <span>Aman (≥80%)</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span>
                <span>Perhatian (<80%)</span>
            </div>
        </div>
    </div>

    <!-- Doughnut Chart Card 2 (Pengumpulan Tugas) -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-between min-h-[320px]">
        <div>
            <h3 class="font-bold text-gray-850 text-sm mb-1">Status Pengumpulan Tugas</h3>
            <p class="text-[11px] text-gray-400 mb-4">Proporsi seluruh tugas yang sudah dikumpulkan vs belum dikumpulkan.</p>
        </div>
        <div class="relative flex items-center justify-center min-h-[180px]">
            <canvas id="tugasDoughnutChart" class="max-w-[160px] max-h-[160px]"></canvas>
        </div>
        <div class="flex justify-around items-center mt-4 text-[10px] text-gray-600 font-medium">
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-indigo-500 inline-block"></span>
                <span>Sudah Kumpul</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-slate-300 inline-block"></span>
                <span>Belum Kumpul</span>
            </div>
        </div>
    </div>

    <!-- Doughnut Chart Card 3 (Penilaian Tugas) -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-between min-h-[320px]">
        <div>
            <h3 class="font-bold text-gray-850 text-sm mb-1">Status Penilaian Tugas</h3>
            <p class="text-[11px] text-gray-400 mb-4">Proporsi tugas terkumpul yang sudah dinilai vs menunggu penilaian.</p>
        </div>
        <div class="relative flex items-center justify-center min-h-[180px]">
            <canvas id="penilaianDoughnutChart" class="max-w-[160px] max-h-[160px]"></canvas>
        </div>
        <div class="flex justify-around items-center mt-4 text-[10px] text-gray-600 font-medium">
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                <span>Sudah Dinilai (<span id="penilaianLegendSudah">-</span>)</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-rose-500 inline-block"></span>
                <span>Belum Dinilai (<span id="penilaianLegendBelum">-</span>)</span>
            </div>
        </div>
    </div>
</div>

<!-- DYNAMIC STATISTICS GRID (BAR CARDS ROW) -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    <!-- Bar Chart Card 1 (Kehadiran per Mapel) -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="font-bold text-gray-850 text-sm">Rata-Rata Kehadiran per Mata Pelajaran</h3>
                <p class="text-xs text-gray-400 mt-1">Menampilkan persentase rata-rata kehadiran siswa per mata pelajaran.</p>
            </div>
        </div>
        <div class="h-[250px] relative w-full">
            <canvas id="attendanceBarChart"></canvas>
        </div>
    </div>

    <!-- Bar Chart Card 2 (Pengumpulan per Tugas ID) -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="font-bold text-gray-850 text-sm">Tingkat Pengumpulan Per Tugas</h3>
                <p class="text-xs text-gray-400 mt-1">Jumlah siswa yang telah mengumpulkan tugas untuk masing-masing ID tugas yang dirilis.</p>
            </div>
        </div>
        <div class="h-[250px] relative w-full">
            <canvas id="tugasBarChart"></canvas>
        </div>
    </div>
</div>
</div>
<!-- END TAB: DASHBOARD -->

<!-- TAB: PENGUMUMAN -->
<div id="tab-pengumuman" class="tab-content hidden">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- KOLOM KIRI: BUAT PENGUMUMAN BARU -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 lg:col-span-1">
            <h3 class="font-bold text-md text-gray-850 mb-4 flex items-center gap-2">
                <i class="fas fa-edit text-emerald-650"></i> Buat Pengumuman Baru
            </h3>
            
            <form action="{{ route('announcements.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Judul Pengumuman</label>
                    <input type="text" name="title" required placeholder="Contoh: Jadwal Ujian KUP / Info Kelas" class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-xs focus:ring-1 focus:ring-emerald-500 outline-none transition font-medium">
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Isi Pengumuman / Pesan</label>
                    <textarea name="content" rows="6" required placeholder="Tulis rincian informasi di sini..." class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-xs focus:ring-1 focus:ring-emerald-500 outline-none transition font-medium"></textarea>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Target Kelas / Batch</label>
                    <select name="target_kelas" required class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-xs focus:ring-1 focus:ring-emerald-500 outline-none transition font-medium">
                        <option value="ALL">Semua Kelas / Batch</option>
                        <option value="Batch 5">Batch 5</option>
                        <option value="Batch 6">Batch 6</option>
                        <option value="tes">tes</option>
                    </select>
                </div>
                
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-2xl shadow transition duration-200 text-xs flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i> Publikasikan Pengumuman
                </button>
            </form>
        </div>
        
        <!-- KOLOM KANAN: FEED PENGUMUMAN AKTIF -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 lg:col-span-2">
            <div class="flex justify-between items-center mb-6 pb-2 border-b border-gray-50">
                <div>
                    <h3 class="font-bold text-md text-gray-850">Feed Pengumuman Kelas</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Daftar pesan broadcast dan interaksi diskusi siswa</p>
                </div>
                <span class="text-xs bg-emerald-50 text-emerald-755 px-2.5 py-1 rounded-lg font-bold border border-emerald-100">{{ count($announcements) }} Pengumuman</span>
            </div>
            
            <div class="space-y-6 max-h-[700px] overflow-y-auto pr-2">
                @forelse($announcements as $ann)
                    <div class="p-5 bg-gray-50/50 border border-gray-100 hover:border-gray-200 rounded-2xl transition duration-200 relative shadow-sm">
                        <div class="flex justify-between items-start mb-3 flex-wrap gap-2">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs uppercase tracking-wider shadow">
                                    {{ substr($ann->author_name, 0, 2) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 text-xs leading-none">{{ $ann->author_name }}</h4>
                                    <p class="text-[9px] text-gray-400 font-semibold mt-1">
                                        {{ $ann->created_at->diffForHumans() }} 
                                        @if($ann->target_kelas !== 'ALL')
                                            • <span class="bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded font-black border border-emerald-150 text-[8px] ml-1">{{ $ann->target_kelas }}</span>
                                        @else
                                            • <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded font-black border border-gray-200 text-[8px] ml-1">SEMUA KELAS</span>
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
                        
                        <h4 class="text-xs font-black text-gray-800 mb-2 leading-snug tracking-tight">{{ $ann->title }}</h4>
                        <div class="text-[11px] text-gray-650 whitespace-pre-line leading-relaxed mb-4 font-medium">
                            {{ $ann->content }}
                        </div>
                        
                        <hr class="border-gray-250 my-3">
                        
                        <!-- Diskusi / Komentar -->
                        <div class="mt-2">
                            <button onclick="toggleComments({{ $ann->id }})" class="text-[11px] font-black text-emerald-700 hover:text-emerald-850 transition flex items-center gap-1">
                                <i class="far fa-comments text-xs"></i> 
                                <span>Diskusi Kelas ({{ count($ann->comments) }} komentar)</span>
                                <i id="comment-icon-{{ $ann->id }}" class="fas fa-chevron-down text-[8px] transition-transform duration-200 ml-0.5"></i>
                            </button>
                            
                            <div id="comments-container-{{ $ann->id }}" class="hidden space-y-2 pl-3 border-l-2 border-emerald-100 mt-3 mb-2">
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
                                
                                <!-- Form Tulis Balasan -->
                                <form action="{{ route('announcements.comments.store', $ann->id) }}" method="POST" class="mt-3 flex gap-2">
                                    @csrf
                                    <input type="text" name="content" placeholder="Ketik balasan Anda..." required class="flex-grow bg-white border border-gray-200 rounded-xl px-3 py-1.5 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition font-medium">
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] px-4 py-1.5 rounded-xl font-bold shadow transition duration-200">
                                        Balas
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-400 bg-gray-50/50 border border-dashed border-gray-200 rounded-2xl">
                        <i class="fas fa-bullhorn text-2xl mb-2 text-gray-300"></i>
                        <p class="text-xs font-bold">Belum ada pengumuman kelas yang dipublikasikan.</p>
                    </div>
                @endforelse
            </div>
        </div>
        
    </div>
</div>

<!-- TAB: MATERI -->
<div id="tab-materi" class="tab-content hidden">

<!-- DATA LIST COLUMNS -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- KOLOM MATERI KULIAH -->
    
    <!-- KOLOM MATERI KULIAH -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-6 pb-2 border-b border-gray-50">
            <div>
                <h3 class="font-bold text-md text-gray-850">Materi yang Sudah Diunggah</h3>
                <p class="text-xs text-gray-400 mt-0.5">Daftar PDF dan rekaman YouTube yang sudah dibagikan ke siswa</p>
            </div>
            <span class="text-xs bg-emerald-50 text-emerald-755 px-2.5 py-1 rounded-lg font-bold border border-emerald-100">{{ count($materi) }} File</span>
        </div>

        <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2">
            @forelse($materi as $m)
            <div class="p-4 bg-gray-50/50 hover:bg-gray-50 border border-gray-100 hover:border-gray-200 rounded-2xl transition">
                <div class="flex justify-between items-start mb-1 flex-wrap gap-2">
                    <p class="text-[9px] font-bold text-emerald-650 uppercase tracking-wider">{{ $m['mapel'] }}</p>
                    <div class="flex items-center gap-1.5">
                        @if(!empty($m['kelas']))
                            <span class="text-[8px] bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded font-black border border-emerald-200">{{ strtoupper($m['kelas']) }}</span>
                        @else
                            <span class="text-[8px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded font-black border border-gray-200">SEMUA KELAS</span>
                        @endif

                        @if(($m['status'] ?? 'Rilis') === 'Draft')
                            <span class="text-[8px] bg-gray-200 text-gray-700 px-2 py-0.5 rounded font-black border border-gray-300">DRAFT</span>
                        @else
                            <span class="text-[8px] bg-green-100 text-green-700 px-2 py-0.5 rounded font-black border border-green-200">RILIS</span>
                        @endif
                    </div>
                </div>
                <h4 class="font-bold text-gray-800 text-xs mb-3 leading-snug">{{ $m['judul'] }}</h4>
                <div class="flex gap-2 flex-wrap">
                    @if($m['link_modul'])
                        <a href="{{ $m['link_modul'] }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] bg-red-50 text-red-650 px-2.5 py-1 rounded-lg font-black border border-red-100 hover:bg-red-100 transition"><i class="fas fa-file-pdf"></i> Modul PDF</a>
                    @endif
                    @if($m['link_youtube'])
                        <a href="{{ $m['link_youtube'] }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] bg-blue-50 text-blue-650 px-2.5 py-1 rounded-lg font-black border border-blue-100 hover:bg-blue-100 transition"><i class="fab fa-youtube"></i> Rekaman YouTube</a>
                    @endif
                </div>
                @if(!empty($m['keterangan']))
                <p class="text-[10px] text-gray-500 mt-3 bg-white p-2 rounded-lg border border-gray-100">{{ $m['keterangan'] }}</p>
                @endif
                <div class="mt-4 flex justify-end">
                    <button type="button" onclick="editMateri({{ json_encode($m) }})" class="text-[10px] font-bold text-emerald-700 hover:text-emerald-700 transition">Edit Materi</button>
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-box-open text-2xl mb-2"></i>
                <p class="text-xs">Belum ada materi yang ditambahkan.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- KOLOM TUGAS AKTIF -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-6 pb-2 border-b border-gray-50">
            <div>
                <h3 class="font-bold text-md text-gray-850">Daftar Tugas Aktif</h3>
                <p class="text-xs text-gray-400 mt-0.5">Daftar tugas yang harus dikerjakan dan dikumpulkan oleh siswa</p>
            </div>
            <span class="text-xs bg-yellow-50 text-yellow-750 px-2.5 py-1 rounded-lg font-bold border border-yellow-100">{{ count($tugas) }} Tugas</span>
        </div>

        <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2">
            @forelse($tugas as $t)
            <div class="p-4 bg-gray-50/50 hover:bg-gray-50 border border-gray-100 hover:border-gray-200 rounded-2xl transition">
                <div class="flex items-center justify-between gap-3 mb-2">
                    <p class="text-[9px] font-bold text-gray-400 bg-gray-200 px-2 py-0.5 rounded-lg border border-gray-250">{{ $t['id_tugas'] }}</p>
                    @if(isset($t['deadline']) && $t['deadline'])
                        <p class="text-[9px] text-rose-600 font-bold"><i class="far fa-clock"></i> Deadline: {{ $t['deadline'] }}</p>
                    @endif
                </div>
                <h4 class="font-bold text-gray-800 text-xs mb-1 leading-snug">{{ $t['judul'] }}</h4>
                <p class="text-[9px] font-bold text-emerald-650 mb-3">{{ $t['mapel'] }}</p>
                
                @if(isset($t['deskripsi']) && $t['deskripsi'])
                    <p class="text-[10px] text-gray-500 mb-3 bg-white p-2 rounded-lg border border-gray-100 leading-relaxed">{{ $t['deskripsi'] }}</p>
                @endif

                @if($t['link_soal'])
                    <a href="{{ $t['link_soal'] }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] text-emerald-650 hover:text-emerald-850 font-black"><i class="fas fa-external-link-alt"></i> File Soal Lengkap</a>
                @endif
                <div class="mt-4 flex justify-end">
                    <button type="button" onclick="editTugas({{ json_encode($t) }})" class="text-[10px] font-bold text-yellow-750 hover:text-yellow-950 transition">Edit Tugas</button>
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-tasks text-2xl mb-2"></i>
                <p class="text-xs">Belum ada tugas aktif.</p>
            </div>
            @endforelse
        </div>
    </div>

</div>
<!-- END DATA LIST COLUMNS -->

</div>
<!-- END TAB: MATERI -->

<!-- TAB: TUGAS -->
<div id="tab-tugas" class="tab-content hidden">
<div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
    <div class="flex justify-between items-center mb-6 pb-2 border-b border-gray-50">
        <div>
            <h3 class="font-bold text-md text-gray-850">Kelola Tugas Aktif</h3>
            <p class="text-xs text-gray-400 mt-0.5">Lihat semua tugas yang telah dibuat dan kelola perubahan</p>
        </div>
        <span class="text-xs bg-yellow-50 text-yellow-750 px-2.5 py-1 rounded-lg font-bold border border-yellow-100">{{ count($tugas) }} Tugas</span>
    </div>

    <div class="space-y-4 max-h-[600px] overflow-y-auto">
        @forelse($tugas as $t)
        <div class="p-4 bg-gray-50/50 hover:bg-gray-50 border border-gray-100 hover:border-gray-200 rounded-2xl transition">
            <div class="flex items-center justify-between gap-3 mb-2">
                <p class="text-[9px] font-bold text-gray-400 bg-gray-200 px-2 py-0.5 rounded-lg border border-gray-250">{{ $t['id_tugas'] }}</p>
                @if(isset($t['deadline']) && $t['deadline'])
                    <p class="text-[9px] text-rose-600 font-bold"><i class="far fa-clock"></i> {{ $t['deadline'] }}</p>
                @endif
            </div>
            <h4 class="font-bold text-gray-800 text-sm mb-2">{{ $t['judul'] }}</h4>
            <p class="text-[9px] font-bold text-emerald-650 mb-2">{{ $t['mapel'] }}</p>
            
            @if(isset($t['deskripsi']) && $t['deskripsi'])
                <p class="text-[10px] text-gray-500 mb-3 bg-white p-2 rounded-lg border border-gray-100">{{ $t['deskripsi'] }}</p>
            @endif

            @if($t['link_soal'])
                <a href="{{ $t['link_soal'] }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] text-emerald-650 hover:text-emerald-850 font-bold mb-3"><i class="fas fa-external-link-alt"></i> Buka File Soal</a>
            @endif
            <div class="flex justify-end pt-3 border-t border-gray-100">
                <button type="button" onclick="editTugas({{ json_encode($t) }})" class="text-[10px] font-bold text-yellow-750 hover:text-yellow-950 transition"><i class="fas fa-edit mr-1"></i> Edit Tugas</button>
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-gray-400">
            <i class="fas fa-tasks text-2xl mb-2"></i>
            <p class="text-xs">Belum ada tugas yang dibuat. Klik tombol "Buat Tugas Baru" untuk memulai.</p>
        </div>
        @endforelse
    </div>
</div>
</div>
<!-- END TAB: TUGAS -->

<!-- TAB: PENILAIAN -->
<div id="tab-penilaian" class="tab-content hidden">
<div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 mb-8">
    <div class="flex justify-between items-center mb-6 pb-2 border-b border-gray-50">
        <div>
            <h3 class="font-bold text-md text-gray-850">Manajemen Penilaian & Nilai Siswa</h3>
            <p class="text-xs text-gray-400 mt-0.5">Kelola nilai dan feedback siswa Anda secara terpusat</p>
        </div>
        <span class="text-xs bg-green-50 text-green-700 px-2.5 py-1 rounded-lg font-bold border border-green-100">Terintegrasi</span>
    </div>

    <!-- Filters -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div>
            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Cari Siswa</label>
            <input type="text" id="searchSubmissions" onkeyup="filterSubmissions()" placeholder="Nama atau email..." class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div>
            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Filter Tugas</label>
            <select id="filterTugas" onchange="filterSubmissions()" class="w-full rounded-xl border-gray-300 shadow-sm text-xs p-2.5 border focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">-- Semua Tugas --</option>
                @php
                    $uniqueTugasIds = is_array($submissions) ? array_unique(array_filter(array_column($submissions, 'id_tugas'))) : [];
                    sort($uniqueTugasIds);
                @endphp
                @foreach($uniqueTugasIds as $idT)
                    <option value="{{ $idT }}">{{ $idT }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Filter Status Nilai</label>
            <select id="filterStatus" onchange="filterSubmissions()" class="w-full rounded-xl border-gray-300 shadow-sm text-xs p-2.5 border focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">-- Semua Status --</option>
                <option value="belum">Belum Dinilai</option>
                <option value="sudah">Sudah Dinilai</option>
            </select>
        </div>
    </div>

    <!-- Bilah Aksi Massal (Bulk Actions Bar) -->
    <div id="bulkActionsContainer" class="mb-4 flex justify-between items-center bg-emerald-50 border border-emerald-100 p-4 rounded-2xl hidden transition duration-200">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
            <p class="text-xs text-emerald-950 font-bold"><span id="dirtyRowsCount">0</span> perubahan nilai belum disimpan</p>
        </div>
        <div class="flex gap-2">
            <button type="button" onclick="cancelBatchGrading()" class="bg-white hover:bg-gray-100 text-emerald-700 border border-emerald-200 font-bold py-1.5 px-3.5 rounded-xl shadow transition duration-200 text-xs">
                Batal
            </button>
            <button type="button" onclick="submitBatchGrading()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-1.5 px-4 rounded-xl shadow transition duration-200 text-xs">
                Simpan Semua Perubahan
            </button>
        </div>
    </div>

    <!-- Submissions Table -->
    <div class="overflow-x-auto rounded-2xl border border-gray-150">
        <table class="min-w-full divide-y divide-gray-150 text-xs text-left">
            <thead class="bg-gray-50 text-gray-500 uppercase font-bold text-[10px] tracking-wider">
                <tr>
                    <th class="px-6 py-4">Siswa</th>
                    <th class="px-6 py-4">Tugas / ID</th>
                    <th class="px-6 py-4 text-center">Berkas</th>
                    <th class="px-6 py-4 text-center" style="width: 120px;">Nilai</th>
                    <th class="px-6 py-4">Feedback / Catatan</th>
                    <th class="px-6 py-4 text-center" style="width: 180px;">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700 bg-white" id="submissionsTableBody">
                @forelse($submissions ?? [] as $sub)
                @php
                    $hasGrade = isset($sub['nilai']) && $sub['nilai'] !== '-' && $sub['nilai'] !== '';
                    $cleanGrade = $hasGrade ? $sub['nilai'] : '';
                    $cleanFeedback = (isset($sub['feedback']) && $sub['feedback'] !== '-') ? $sub['feedback'] : '';
                @endphp
                <tr class="submission-row hover:bg-gray-50/50 transition" 
                    data-nama="{{ strtolower($sub['nama'] ?? '') }}" 
                    data-email="{{ strtolower($sub['email'] ?? '') }}"
                    data-idtugas="{{ $sub['id_tugas'] ?? '' }}"
                    data-status="{{ $hasGrade ? 'sudah' : 'belum' }}">
                    <td class="px-6 py-4">
                        <p class="font-bold text-gray-800">{{ $sub['nama'] ?? '-' }}</p>
                        <p class="text-[10px] text-gray-400">{{ $sub['email'] ?? '-' }}</p>
                        <p class="text-[9px] text-gray-400 mt-1 italic"><i class="far fa-clock"></i> {{ $sub['timestamp'] ?? '-' }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-bold text-gray-700">{{ $sub['id_tugas'] ?? '-' }}</p>
                        <p class="text-[10px] text-emerald-650 font-semibold">{{ $sub['mapel'] ?? '-' }}</p>
                        <p class="text-[10px] text-gray-500 mt-0.5 line-clamp-1" title="{{ $sub['judul_tugas'] ?? '' }}">{{ $sub['judul_tugas'] ?? '-' }}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if(!empty($sub['link_file']))
                            <a href="{{ $sub['link_file'] }}" target="_blank" class="inline-flex items-center gap-1.5 bg-emerald-50 hover:bg-indigo-100 text-emerald-755 font-bold px-3 py-1.5 rounded-xl border border-emerald-100 transition shadow-sm text-[10px]">
                                <i class="fas fa-external-link-alt text-[9px]"></i> Buka File
                            </a>
                        @else
                            <span class="text-gray-400 italic">Tidak ada file</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex flex-col items-center gap-1">
                            <input type="number" 
                                   value="{{ $cleanGrade }}" 
                                   data-original="{{ $cleanGrade }}"
                                   min="0" 
                                   max="100" 
                                   class="grade-input w-16 text-center font-bold px-2 py-1.5 rounded-xl border border-gray-250 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-xs text-gray-800"
                                   oninput="markRowDirty(this)"
                                   placeholder="-">
                            <span class="status-badge hidden text-[8px] bg-amber-50 text-amber-700 border border-amber-250 px-1.5 py-0.5 rounded-full font-bold">Draft</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="w-full">
                            <input type="text" 
                                   value="{{ $cleanFeedback }}" 
                                   data-original="{{ $cleanFeedback }}"
                                   class="feedback-input w-full min-w-[150px] px-3 py-1.5 rounded-xl border border-gray-255 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-xs text-gray-700"
                                   oninput="markRowDirty(this)"
                                   placeholder="Tulis feedback singkat...">
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <button type="button" 
                                    onclick="saveSingleRow(this)"
                                    class="save-row-btn hidden items-center gap-1 bg-green-50 hover:bg-green-100 text-green-700 font-bold px-2.5 py-1.5 rounded-xl border border-green-200 transition text-[10px] shadow-sm">
                                <i class="fas fa-check"></i> Simpan
                            </button>
                            <button type="button" 
                                    onclick="openGradeModal('{{ $sub['email'] ?? '' }}', '{{ $sub['id_tugas'] ?? '' }}', '{{ addslashes($sub['nama'] ?? '') }}', '{{ addslashes($sub['judul_tugas'] ?? '') }}', this.closest('tr').querySelector('.grade-input').value, this.closest('tr').querySelector('.feedback-input').value)"
                                    class="inline-flex items-center gap-1 bg-yellow-50 hover:bg-yellow-100 text-yellow-750 hover:text-emerald-950 font-black px-2.5 py-1.5 rounded-xl border border-yellow-200 transition text-[10px] shadow-sm">
                                <i class="fas fa-expand-alt"></i> Detail
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-12 text-gray-400">
                        <i class="fas fa-tasks text-2xl mb-2"></i>
                        <p class="text-xs">Belum ada pengumpulan tugas dari siswa Anda.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- Catatan Integrasi Penilaian -->
    <div class="mt-8 bg-gradient-to-br from-emerald-50 to-teal-50 p-5 rounded-2xl border border-emerald-100">
        <h4 class="font-bold text-xs text-gray-800 mb-2"><i class="fas fa-star text-yellow-500 mr-2"></i> Catatan Integrasi Penilaian</h4>
        <div class="space-y-2 text-[10px] text-gray-700 leading-relaxed">
            <p>✓ Berikan nilai dan feedback langsung dari baris tabel di atas menggunakan tombol <strong>Beri Nilai</strong>.</p>
            <p>✓ Setelah dikirim, nilai akan tersimpan ke Google Sheets dan ter-refresh di dashboard siswa secara instan.</p>
        </div>
    </div>
</div>
</div>
<!-- END TAB: PENILAIAN -->

<!-- TAB: REKAP NILAI KELAS -->
<div id="tab-rekap" class="tab-content hidden">
<div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 mb-8">
    <div class="flex justify-between items-center mb-6 pb-2 border-b border-gray-50 flex-wrap gap-4">
        <div>
            <h3 class="font-bold text-md text-gray-850">Rekap Nilai Kelas (Matriks)</h3>
            <p class="text-xs text-gray-400 mt-0.5">Ringkasan nilai seluruh siswa terdaftar untuk semua tugas kelas</p>
        </div>
        <button onclick="exportRekapToCSV()" class="inline-flex items-center gap-1.5 bg-green-50 hover:bg-green-100 text-green-700 font-bold px-4 py-2 rounded-xl border border-green-200 transition shadow-sm text-xs">
            <i class="fas fa-file-excel mr-1"></i> Ekspor ke CSV / Excel
        </button>
    </div>

    <!-- Search Filter -->
    <div class="mb-6 max-w-md">
        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Cari Siswa di Matriks</label>
        <input type="text" id="searchRekap" onkeyup="filterRekapMatrix()" placeholder="Cari nama atau email..." class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
    </div>

    <!-- Matrix Table Wrapper -->
    <div class="overflow-x-auto rounded-2xl border border-gray-150">
        <table class="min-w-full divide-y divide-gray-150 text-xs text-left">
            <thead class="bg-gray-50 text-gray-500 uppercase font-bold text-[10px] tracking-wider">
                <tr id="matrixHeaderRow">
                    <th class="px-6 py-4 min-w-[200px]">Nama Siswa</th>
                    <!-- ID Tugas columns will be generated by JS -->
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700 bg-white" id="matrixBody">
                <!-- Rows will be generated dynamically by JS -->
            </tbody>
        </table>
    </div>
</div>
</div>
<!-- END TAB: REKAP NILAI KELAS -->

<!-- TAB: KEHADIRAN SISWA -->
<div id="tab-absensi" class="tab-content hidden space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Rekapitulasi Kehadiran Siswa</h3>
            <p class="text-xs text-gray-450 mt-0.5">Pantau dan kelola kehadiran belajar peserta Brevet Pajak</p>
        </div>
        <div class="flex gap-2">
            <button onclick="downloadAbsensi()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-md transition text-xs flex items-center gap-1.5">
                <i class="fas fa-download"></i> Unduh Rekap (CSV)
            </button>
            <button onclick="openAbsenModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-md transition text-xs flex items-center gap-1.5">
                <i class="fas fa-plus"></i> Input Absen Manual
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1.5">Cari Siswa</label>
            <div class="relative">
                <i class="fas fa-search absolute left-3.5 top-3 text-gray-400 text-xs"></i>
                <input type="text" id="searchAbsen" onkeyup="filterAbsen()" placeholder="Ketik nama atau email..." class="w-full bg-slate-50 border border-gray-200 rounded-xl pl-9 pr-4 py-2 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition font-medium">
            </div>
        </div>
        <div>
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1.5">Filter Mata Pelajaran</label>
            <select id="filterAbsenMapel" onchange="filterAbsen()" class="w-full bg-slate-50 border border-gray-200 rounded-xl px-3 py-2 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition font-medium">
                <option value="">-- Semua Mata Pelajaran --</option>
                @foreach($daftarMapel ?? [] as $m)
                    <option value="{{ $m }}">{{ $m }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Matriks Kehadiran -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
        <h4 class="font-bold text-gray-800 text-sm mb-4"><i class="fas fa-th-large text-emerald-600 mr-2"></i> Matriks Kehadiran Siswa</h4>
        
        <div class="overflow-x-auto rounded-2xl border border-gray-150">
            <table class="min-w-full divide-y divide-gray-150 text-xs text-left">
                <thead class="bg-gray-50 text-gray-500 uppercase font-bold text-[9px] tracking-wider border-b border-gray-150">
                    <tr>
                        <th class="px-6 py-4 min-w-[200px] border-r border-gray-150">Nama Siswa</th>
                        @foreach($daftarMapel ?? [] as $m)
                            @php
                                $abbrev = isset($mapelAbbreviations[$m]) ? $mapelAbbreviations[$m] : substr($m, 0, 8);
                                $sessionsCount = isset($mapelSessionCounts[$m]) ? $mapelSessionCounts[$m] : 0;
                            @endphp
                            <th class="px-4 py-4 text-center min-w-[100px]" title="{{ $m }}">
                                {{ $abbrev }}<br>
                                <span class="text-[8px] text-gray-400 font-normal">({{ $sessionsCount }} sesi)</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 bg-white" id="absenMatrixBody">
                    <!-- Dynamic Matrix Rows -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Log Riwayat Absensi -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
        <h4 class="font-bold text-gray-800 text-sm mb-4"><i class="fas fa-history text-emerald-600 mr-2"></i> Log Presensi Terakhir</h4>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 font-bold uppercase tracking-wider">
                        <th class="pb-3 pl-4">Tanggal & Waktu</th>
                        <th class="pb-3">Siswa</th>
                        <th class="pb-3">Mata Pelajaran</th>
                        <th class="pb-3">Metode</th>
                        <th class="pb-3">Status</th>
                        @if(in_array(session('role'), ['ADMIN', 'ADMIN_LMS']))
                            <th class="pb-3 pr-4 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-700" id="absenLogBody">
                    <!-- Dynamic Log Rows -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TAB: PROFILE SETTINGS -->
<div id="tab-profil" class="tab-content hidden space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Pengaturan Akun & Profil</h3>
            <p class="text-xs text-gray-450 mt-0.5">Perbarui nama tampilan atau ganti password keamanan Anda</p>
        </div>
    </div>

    @if($errors->any() && old('old_password') === null && old('nama') !== null)
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
            <div class="w-20 h-20 rounded-full bg-emerald-600 text-white flex items-center justify-center font-black text-2xl uppercase tracking-wider shadow-md mb-4 ring-4 ring-emerald-50">
                {{ strtoupper(substr(session('nama', 'U'), 0, 2)) }}
            </div>
            <h4 class="font-bold text-gray-800 text-base leading-tight">{{ session('nama') }}</h4>
            <p class="text-xs text-gray-400 mt-1 font-semibold">{{ session('email') }}</p>
            <span class="mt-4 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-750 border border-emerald-100">
                Guru / Dosen
            </span>
            
            <hr class="w-full border-gray-100 my-6">
            
            <div class="w-full space-y-3.5 text-xs text-left">
                <div class="flex justify-between items-center text-gray-500">
                    <span>Mata Pelajaran Diajar</span>
                    <span class="font-semibold text-gray-800 text-right max-w-[150px] truncate" title="{{ isset($guruMapel) ? implode(', ', $guruMapel) : '-' }}">
                        {{ isset($guruMapel) ? implode(', ', $guruMapel) : '-' }}
                    </span>
                </div>
                <div class="flex justify-between items-center text-gray-500">
                    <span>Status Kepegawaian</span>
                    <span class="font-semibold text-emerald-600 flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Aktif</span>
                </div>
            </div>
        </div>

        <!-- Right Side: Edit Form -->
        <div class="lg:col-span-2 bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
            <h4 class="font-bold text-gray-800 text-md mb-6"><i class="fas fa-user-cog text-emerald-600 mr-2"></i> Perbarui Detail Profil & Keamanan</h4>
            
            <form action="{{ route('profil.update') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Nama Lengkap Anda</label>
                    <input type="text" name="nama" required value="{{ old('nama', session('nama')) }}" class="w-full rounded-xl border-gray-300 shadow-sm p-3 border text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition font-medium">
                    <p class="text-[10px] text-gray-400 mt-1.5">Gunakan nama lengkap dengan gelar akademik yang ingin Anda tampilkan ke siswa.</p>
                </div>
                
                <hr class="border-gray-100 my-6">
                
                <div class="bg-gray-50/50 p-6 rounded-2xl border border-gray-150 space-y-4">
                    <h5 class="text-xs font-black text-gray-800 uppercase tracking-wider mb-2 flex items-center gap-2 text-emerald-600">
                        <i class="fas fa-lock"></i> Ganti Password <span class="text-[9px] text-gray-400 font-normal uppercase tracking-normal ml-1">(Kosongkan jika tidak ingin mengubah)</span>
                    </h5>
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Password Lama</label>
                        <input type="password" name="old_password" placeholder="Masukkan password Anda saat ini" class="w-full rounded-xl bg-white border-gray-300 shadow-sm p-3 border text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition font-medium">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Password Baru</label>
                            <input type="password" name="new_password" placeholder="Minimal 6 karakter" class="w-full rounded-xl bg-white border-gray-300 shadow-sm p-3 border text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition font-medium">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Konfirmasi Password Baru</label>
                            <input type="password" name="new_password_confirmation" placeholder="Ulangi password baru" class="w-full rounded-xl bg-white border-gray-300 shadow-sm p-3 border text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition font-medium">
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end pt-4">
                    <button type="submit" class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-emerald-500/10 transition text-xs flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Simpan Perubahan Profil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH MATERI -->
<div id="materiModal" onclick="closeMateriModal()" class="fixed inset-0 bg-gray-900/60 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl flex flex-col max-h-[90vh]" onclick="event.stopPropagation()">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
            <div>
                <h3 id="materiModalTitle" class="font-bold text-sm text-gray-800">Unggah Materi Brevet Baru</h3>
                <p class="text-[10px] text-gray-450 mt-0.5">Isi form di bawah untuk membagikan modul ke siswa</p>
            </div>
            <button onclick="closeMateriModal()" class="text-gray-400 hover:text-red-500 transition"><i class="fas fa-times text-lg"></i></button>
        </div>
        <form id="materiForm" action="{{ route('guru.materi.store') }}" method="POST" enctype="multipart/form-data" onsubmit="showLoadingOverlay(this)" class="p-6 space-y-4 overflow-y-auto flex-1">
            @csrf
            <input type="hidden" name="original_mapel" id="original_mapel" value="">
            <input type="hidden" name="original_judul" id="original_judul" value="">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Mata Pelajaran (Brevet)</label>
                <select name="mapel" required class="w-full rounded-xl border-gray-300 shadow-sm text-xs p-2.5 border focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">-- Pilih Mapel --</option>
                    @if(session('role') === 'ADMIN' || session('role') === 'ADMIN_LMS')
                        @php
                            $daftarMapel = [
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
                        @endphp
                        @foreach($daftarMapel as $mapel)
                            <option value="{{ $mapel }}">{{ $mapel }}</option>
                        @endforeach
                        @if(!empty($matakuliah))
                            @foreach($matakuliah as $mk)
                                @if(!in_array($mk, $daftarMapel))
                                    <option value="{{ $mk }}">{{ $mk }}</option>
                                @endif
                            @endforeach
                        @endif
                    @else
                        @forelse($guruMapel ?? [] as $mk)
                            <option value="{{ $mk }}">{{ $mk }}</option>
                        @empty
                            <option value="">Tidak ada mata pelajaran terdaftar</option>
                        @endforelse
                    @endif
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Judul Pertemuan / Materi</label>
                <input type="text" name="judul" required placeholder="Contoh: Pertemuan 1 - Konsep Dasar KUP" class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Link Tautan Modul PDF (Google Drive) <span class="text-gray-400 font-normal">(Opsional)</span></label>
                <input type="url" name="link_modul" placeholder="https://drive.google.com/file/d/..." class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <p class="text-[9px] text-gray-400 mt-1">Pastikan link Google Drive diset "Siapa saja yang memiliki link dapat melihat".</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Unggah File Modul (PDF/ZIP/Docx lainnya) <span class="text-gray-400 font-normal">(Opsional, maks 30MB)</span></label>
                
                <!-- Current File Display (Edit Mode) -->
                <div id="currentFileInfo" class="mb-3 hidden p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-[9px] text-blue-700 font-bold mb-1"><i class="fas fa-file-check mr-1"></i> File Saat Ini:</p>
                    <p id="currentFileName" class="text-xs font-mono text-gray-700 mb-2">-</p>
                    <div class="flex gap-2">
                        <a id="currentFileLink" href="#" target="_blank" class="text-[9px] text-blue-600 hover:text-blue-800 font-bold"><i class="fas fa-download mr-1"></i> Download File</a>
                        <button type="button" onclick="clearFileAndReplace()" class="text-[9px] text-red-600 hover:text-red-800 font-bold"><i class="fas fa-times mr-1"></i> Hapus File</button>
                    </div>
                    <label class="mt-3 flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="replaceFileCheckbox" onchange="toggleFileUpload()" class="rounded">
                        <span class="text-[9px] text-gray-700 font-bold">Upload file baru untuk mengganti file lama</span>
                    </label>
                </div>

                <!-- File Input (Hidden in Edit Mode) -->
                <!-- Custom Drag & Drop Dropzone for Modul -->
                <label for="fileModulInput" class="border-2 border-dashed border-emerald-200 hover:border-emerald-400 bg-emerald-50/10 hover:bg-emerald-50/20 rounded-2xl p-5 flex flex-col items-center justify-center gap-2 cursor-pointer transition">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-md">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div class="text-center">
                        <p class="text-xs font-bold text-slate-700">Tarik & Lepas file di sini</p>
                        <p class="text-[9px] text-slate-500">atau klik untuk memilih file</p>
                    </div>
                    <span id="selectedModulNameDisplay" class="text-xs font-semibold text-emerald-700 hidden"></span>
                </label>
                <input type="file" name="file_modul" id="fileModulInput" accept="*/*" class="hidden" onchange="displaySelectedFile(this, 'selectedModulNameDisplay')">
                <p class="text-[9px] text-gray-400 mt-1">Jika mengunggah file, sistem akan menyimpannya ke Google Drive dan menghasilkan tautan publik.</p>
                
                <!-- Warning Message -->
                <div id="replaceWarning" class="mt-3 hidden p-2 bg-yellow-50 border border-yellow-200 rounded text-[9px] text-yellow-800 font-bold">
                    <i class="fas fa-exclamation-triangle mr-1"></i> File lama akan diganti dengan file baru yang Anda upload.
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Link Video Rekaman YouTube <span class="text-gray-400 font-normal">(Opsional)</span></label>
                <input type="url" name="link_youtube" id="materi_link_youtube" placeholder="https://www.youtube.com/watch?v=..." class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Status Publikasi</label>
                <select name="status" class="w-full rounded-xl border-gray-300 shadow-sm text-xs p-2.5 border focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="Rilis">Rilis (Tampilkan ke Siswa)</option>
                    <option value="Draft">Draft (Sembunyikan dari Siswa)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Target Kelas / Batch <span class="text-gray-400 font-normal">(Opsional)</span></label>
                <select name="kelas" class="w-full rounded-xl border-gray-300 shadow-sm text-xs p-2.5 border focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Semua Kelas</option>
                    <option value="Batch 6">Batch 6</option>
                    <option value="Batch 5">Batch 5</option>
                    <option value="tes">tes (Sandbox)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Keterangan Singkat <span class="text-gray-400 font-normal">(Opsional)</span></label>
                <textarea name="keterangan" rows="3" placeholder="Contoh: Rekaman diambil pada pertemuan 1" class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
            </div>

            <button type="submit" id="materiSubmitButton" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg transition text-xs flex items-center justify-center gap-2">
                <i class="fas fa-save"></i> Simpan & Unggah Materi
            </button>
        </form>
    </div>
</div>

<!-- MODAL TAMBAH TUGAS -->
<div id="tugasModal" onclick="closeTugasModal()" class="fixed inset-0 bg-gray-900/60 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl flex flex-col max-h-[90vh]" onclick="event.stopPropagation()">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
            <div>
                <h3 id="tugasModalTitle" class="font-bold text-sm text-gray-800">Buat Tugas Baru</h3>
                <p class="text-[10px] text-gray-455 mt-0.5">Instruksikan penugasan baru kepada seluruh siswa</p>
            </div>
            <button onclick="closeTugasModal()" class="text-gray-400 hover:text-red-500 transition"><i class="fas fa-times text-lg"></i></button>
        </div>
        <form id="tugasForm" action="{{ route('guru.tugas.store') }}" method="POST" enctype="multipart/form-data" onsubmit="showLoadingOverlay(this)" class="p-6 space-y-4 overflow-y-auto flex-1">
            @csrf
            <input type="hidden" name="original_id_tugas" id="original_id_tugas" value="">
            
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Mata Pelajaran (Brevet)</label>
                <select name="mapel" required class="w-full rounded-xl border-gray-300 shadow-sm text-xs p-2.5 border focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">-- Pilih Mapel --</option>
                    @if(session('role') === 'ADMIN' || session('role') === 'ADMIN_LMS')
                        @php
                            $daftarMapel = [
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
                        @endphp
                        @foreach($daftarMapel as $mapel)
                            <option value="{{ $mapel }}">{{ $mapel }}</option>
                        @endforeach
                        @if(!empty($matakuliah))
                            @foreach($matakuliah as $mk)
                                @if(!in_array($mk, $daftarMapel))
                                    <option value="{{ $mk }}">{{ $mk }}</option>
                                @endif
                            @endforeach
                        @endif
                    @else
                        @forelse($guruMapel ?? [] as $mk)
                            <option value="{{ $mk }}">{{ $mk }}</option>
                        @empty
                            <option value="">Tidak ada mata pelajaran terdaftar</option>
                        @endforelse
                    @endif
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">ID Tugas (Unik)</label>
                    <input type="text" name="id_tugas" readonly required placeholder="Pilih mapel terlebih dahulu..." class="w-full rounded-xl bg-gray-100 border-gray-300 shadow-sm p-2.5 border text-xs cursor-not-allowed focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Batas Waktu (Deadline WIB)</label>
                    <div class="flex gap-1 items-center">
                        <input type="date" name="deadline_date" required class="flex-1 min-w-0 rounded-xl border-gray-300 shadow-sm p-2 text-xs border focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <select name="deadline_hour" required class="w-16 rounded-xl border-gray-300 shadow-sm p-2 text-xs border focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            @for ($h = 0; $h < 24; $h++)
                                <option value="{{ sprintf('%02d', $h) }}" {{ $h == 23 ? 'selected' : '' }}>{{ sprintf('%02d', $h) }}</option>
                            @endfor
                        </select>
                        <span class="text-xs font-bold text-gray-500">:</span>
                        <select name="deadline_minute" required class="w-16 rounded-xl border-gray-300 shadow-sm p-2 text-xs border focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            @for ($m = 0; $m < 60; $m++)
                                <option value="{{ sprintf('%02d', $m) }}" {{ $m == 59 ? 'selected' : '' }}>{{ sprintf('%02d', $m) }}</option>
                            @endfor
                        </select>
                        <span class="text-[10px] font-bold text-gray-500">WIB</span>
                    </div>
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Judul Tugas / Evaluasi</label>
                <input type="text" name="judul" required placeholder="Contoh: Tugas Mandiri KUP 01" class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Instruksi Deskripsi Tugas <span class="text-gray-400 font-normal">(Opsional)</span></label>
                <textarea name="deskripsi" rows="3" placeholder="Tulis instruksi pengerjaan bagi siswa..." class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Tautan Berkas Soal (Google Drive/PDF Link) <span class="text-gray-400 font-normal">(Opsional)</span></label>
                <input type="url" name="link_soal" placeholder="https://drive.google.com/..." class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Unggah Berkas Soal (PDF/Word/Lainnya) <span class="text-gray-400 font-normal">(Opsional, maks 30MB)</span></label>
                <!-- Custom Drag & Drop Dropzone for Soal -->
                <label for="fileSoalInput" class="border-2 border-dashed border-emerald-200 hover:border-emerald-400 bg-emerald-50/10 hover:bg-emerald-50/20 rounded-2xl p-5 flex flex-col items-center justify-center gap-2 cursor-pointer transition">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-md">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div class="text-center">
                        <p class="text-xs font-bold text-slate-700">Tarik & Lepas file di sini</p>
                        <p class="text-[9px] text-slate-500">atau klik untuk memilih file</p>
                    </div>
                    <span id="selectedSoalNameDisplay" class="text-xs font-semibold text-emerald-700 hidden"></span>
                </label>
                <input type="file" name="file_soal" id="fileSoalInput" accept="*/*" class="hidden" onchange="displaySelectedFile(this, 'selectedSoalNameDisplay')">
                <p class="text-[9px] text-gray-400 mt-1">Jika mengunggah file baru, sistem akan mengunggahnya ke Google Drive dan mengisi tautan berkas di atas.</p>
            </div>

            <div class="flex items-center gap-2 py-2" id="tugasBlastCheckboxContainer">
                <input type="checkbox" name="blast" id="inputTugasBlast" value="1" checked class="rounded border-gray-300 text-emerald-950 focus:ring-emerald-500">
                <label for="inputTugasBlast" class="text-xs font-bold text-gray-700 cursor-pointer">📢 Kirim Notifikasi Email Otomatis ke Siswa</label>
            </div>

            <button type="submit" id="tugasSubmitButton" class="w-full bg-yellow-450 hover:bg-yellow-500 text-emerald-950 font-black py-3 px-4 rounded-xl shadow-lg transition text-xs flex items-center justify-center gap-2">
                <i class="fas fa-save"></i> Buat & Bagikan Tugas
            </button>
        </form>
    </div>
</div>

<!-- MODAL PENILAIAN TUGAS -->
<div id="gradeModal" onclick="closeGradeModal()" class="fixed inset-0 bg-gray-900/60 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl flex flex-col max-h-[90vh]" onclick="event.stopPropagation()">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
            <div>
                <h3 class="font-bold text-sm text-gray-800">Penilaian Tugas Brevet</h3>
                <p class="text-[10px] text-gray-450 mt-0.5">Masukkan nilai dan feedback untuk pengerjaan siswa</p>
            </div>
            <button onclick="closeGradeModal()" class="text-gray-400 hover:text-red-500 transition"><i class="fas fa-times text-lg"></i></button>
        </div>
        <form id="gradeForm" onsubmit="submitGrading(event)" class="p-6 space-y-4 overflow-y-auto flex-1">
            @csrf
            <input type="hidden" name="email" id="gradeEmail">
            <input type="hidden" name="id_tugas" id="gradeIdTugas">
            
            <div class="p-3 bg-gray-50 rounded-xl border border-gray-150 text-xs space-y-1">
                <p class="text-gray-500">Siswa: <strong class="text-gray-800" id="gradeStudentName">-</strong></p>
                <p class="text-gray-500">Tugas: <strong class="text-gray-800" id="gradeTaskTitle">-</strong></p>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nilai (0 - 100)</label>
                <input type="number" name="nilai" id="gradeNilai" required min="0" max="100" placeholder="Contoh: 85" class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Feedback / Catatan Komentar <span class="text-gray-400 font-normal">(Opsional)</span></label>
                <textarea name="feedback" id="gradeFeedback" rows="4" placeholder="Tulis masukan konstruktif untuk pengerjaan siswa..." class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg transition text-xs flex items-center justify-center gap-2">
                <i class="fas fa-check-circle"></i> Simpan Penilaian
            </button>
        </form>
    </div>
</div>


<!-- MODAL INPUT ABSENSI MANUAL -->
<div id="absenModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl p-6 md:p-8 max-w-md w-full mx-4 shadow-2xl transform scale-95 transition-transform duration-300">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-user-check text-emerald-600"></i> Input Kehadiran Manual
            </h3>
            <button onclick="closeAbsenModal()" class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        <form id="absenForm" onsubmit="saveAbsenManual(event)" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Siswa</label>
                <select name="email" required class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($siswaList ?? [] as $s)
                        <option value="{{ $s['email'] }}">{{ $s['nama'] }} ({{ $s['email'] }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Mata Pelajaran</label>
                <select name="mapel" required class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach($daftarMapel ?? [] as $m)
                        <option value="{{ $m }}">{{ $m }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Jam WIB</label>
                    <input type="time" name="jam" required value="09:00" class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Metode Kehadiran</label>
                <select name="metode" required class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="Manual (Tutor)" selected>Manual (Tutor)</option>
                    <option value="Live Zoom">Live Zoom</option>
                    <option value="Nonton Rekaman YouTube">Nonton Rekaman YouTube</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg transition text-xs flex items-center justify-center gap-2">
                <i class="fas fa-check-circle"></i> Catat Kehadiran
            </button>
        </form>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // --- ABSENSI CONTROLS GLOBAL VARIABLES ---
    const siswaList = @json($siswaList ?? []);
    const rawAbsensi = @json($absensi ?? []);
    const siswaAbsensiMap = @json($siswaAbsensiMap ?? []);
    const mapelSessionCounts = @json($mapelSessionCounts ?? []);
    const daftarMapel = @json($daftarMapel ?? []);
    const mapelAbbreviations = @json($mapelAbbreviations ?? []);
    const tugasList = @json($tugas ?? []);
    const submissionsList = @json($submissions ?? []);
    
    let currentAbsensi = Array.isArray(rawAbsensi) ? [...rawAbsensi] : [];
    let currentAbsensiMap = (siswaAbsensiMap && typeof siswaAbsensiMap === 'object') ? JSON.parse(JSON.stringify(siswaAbsensiMap)) : {};

    // --- MODAL MATERI CONTROLS ---
    const materiStoreUrl = "{{ route('guru.materi.store') }}";
    const materiUpdateUrl = "{{ route('guru.materi.update') }}";
    const tugasStoreUrl = "{{ route('guru.tugas.store') }}";
    const tugasUpdateUrl = "{{ route('guru.tugas.update') }}";

    function resetMateriModal() {
        const form = document.getElementById('materiForm');
        form.action = materiStoreUrl;
        document.getElementById('materiModalTitle').innerText = 'Unggah Materi Brevet Baru';
        document.getElementById('materiSubmitButton').innerHTML = '<i class="fas fa-save"></i> Simpan & Unggah Materi';
        document.getElementById('original_mapel').value = '';
        document.getElementById('original_judul').value = '';
        if (form.link_youtube) {
            form.link_youtube.value = '';
        }
        if (form.status) {
            form.status.value = 'Rilis';
        }
        if (form.kelas) {
            form.kelas.value = '';
        }
        form.reset();
    }

    function openMateriModal() {
        const form = document.getElementById('materiForm');
        // Jika sebelumnya edit mode, reset form agar bersih.
        // Jika sebelumnya add mode, biarkan input draft.
        if (form.action === materiUpdateUrl) {
            resetMateriModal();
        }
        document.getElementById('materiModal').classList.remove('hidden');
    }

    function editMateri(materi) {
        const form = document.getElementById('materiForm');
        form.action = materiUpdateUrl;
        document.getElementById('materiModalTitle').innerText = 'Edit Materi';
        document.getElementById('materiSubmitButton').innerHTML = '<i class="fas fa-save"></i> Perbarui Materi';
        document.getElementById('original_mapel').value = materi.mapel ?? '';
        document.getElementById('original_judul').value = materi.judul ?? '';
        form.mapel.value = materi.mapel ?? '';
        form.judul.value = materi.judul ?? '';
        form.link_modul.value = materi.link_modul ?? '';
        if (form.link_youtube) {
            form.link_youtube.value = materi.link_youtube ?? '';
        }
        if (form.keterangan) {
            form.keterangan.value = materi.keterangan ?? '';
        }
        if (form.status) {
            form.status.value = materi.status ?? 'Rilis';
        }
        if (form.kelas) {
            form.kelas.value = materi.kelas ?? '';
        }
        
        // Show current file info if exists
        const currentFileInfo = document.getElementById('currentFileInfo');
        const fileModulInput = document.getElementById('fileModulInput');
        const currentFileName = document.getElementById('currentFileName');
        const currentFileLink = document.getElementById('currentFileLink');
        
        if (materi.link_modul && materi.link_modul.trim() !== '') {
            // Extract filename from URL or use generic name
            const fileName = materi.link_modul.includes('/') 
                ? materi.link_modul.split('/').pop().substring(0, 40) + '...' 
                : 'File Modul';
            
            currentFileName.innerText = fileName || 'File Modul (tidak diketahui)';
            currentFileLink.href = materi.link_modul;
            currentFileInfo.classList.remove('hidden');
            fileModulInput.classList.add('hidden');
            document.getElementById('replaceWarning').classList.add('hidden');
        } else {
            currentFileInfo.classList.add('hidden');
            fileModulInput.classList.remove('hidden');
        }
        
        document.getElementById('materiModal').classList.remove('hidden');
    }

    function toggleFileUpload() {
        const checkbox = document.getElementById('replaceFileCheckbox');
        const fileInput = document.getElementById('fileModulInput');
        const warning = document.getElementById('replaceWarning');
        
        if (checkbox.checked) {
            fileInput.classList.remove('hidden');
            warning.classList.remove('hidden');
            fileInput.required = true;
        } else {
            fileInput.classList.add('hidden');
            warning.classList.add('hidden');
            fileInput.required = false;
            fileInput.value = '';
        }
    }

    function clearFileAndReplace() {
        document.getElementById('fileModulInput').value = '';
        document.getElementById('replaceFileCheckbox').checked = true;
        toggleFileUpload();
    }

    // --- MODAL TUGAS CONTROLS ---
    function openTugasModal() {
        const form = document.getElementById('tugasForm');
        // Jika sebelumnya edit mode, reset form agar bersih.
        // Jika sebelumnya add mode, biarkan input draft.
        if (form.action === tugasUpdateUrl) {
            resetTugasModal();
        }
        document.getElementById('tugasModal').classList.remove('hidden');
    }

    function closeTugasModal() {
        document.getElementById('tugasModal').classList.add('hidden');
    }

    function resetTugasModal() {
        const form = document.getElementById('tugasForm');
        form.action = tugasStoreUrl;
        document.getElementById('tugasModalTitle').innerText = 'Buat Tugas Baru';
        document.getElementById('tugasSubmitButton').innerHTML = '<i class="fas fa-save"></i> Buat & Bagikan Tugas';
        document.getElementById('original_id_tugas').value = '';
        const fileInput = document.getElementById('fileSoalInput');
        if (fileInput) fileInput.value = '';
        form.reset();
        form.id_tugas.value = '';
    }

    function editTugas(tugas) {
        const form = document.getElementById('tugasForm');
        form.action = tugasUpdateUrl;
        document.getElementById('tugasModalTitle').innerText = 'Edit Tugas yang Ada';
        document.getElementById('tugasSubmitButton').innerHTML = '<i class="fas fa-save"></i> Perbarui Tugas';
        document.getElementById('original_id_tugas').value = tugas.id_tugas ?? '';
        form.id_tugas.value = tugas.id_tugas ?? '';
        form.mapel.value = tugas.mapel ?? '';
        form.judul.value = tugas.judul ?? '';
        form.deskripsi.value = tugas.deskripsi ?? '';
        form.link_soal.value = tugas.link_soal ?? '';
        const fileInput = document.getElementById('fileSoalInput');
        if (fileInput) fileInput.value = '';
        
        const blastInput = document.getElementById('inputTugasBlast');
        if (blastInput) blastInput.checked = true;
        
        let dl = tugas.deadline ?? '';
        dl = dl.replace(' WIB', '').trim();
        if (dl) {
            const parts = dl.split(' ');
            if (parts.length >= 1) {
                form.deadline_date.value = parts[0];
            }
            if (parts.length >= 2) {
                const timeParts = parts[1].split(':');
                if (timeParts.length >= 1) {
                    form.deadline_hour.value = timeParts[0];
                }
                if (timeParts.length >= 2) {
                    form.deadline_minute.value = timeParts[1];
                }
            }
        } else {
            form.deadline_date.value = '';
            form.deadline_hour.value = '23';
            form.deadline_minute.value = '59';
        }
        
        document.getElementById('tugasModal').classList.remove('hidden');
    }

    // --- SUBMIT LOADING ACTION ---
    function showLoadingOverlay(form) {
        // Validate file types if any (anti-RCE check)
        const blacklist = ['php', 'phtml', 'php3', 'php4', 'php5', 'html', 'htm', 'js', 'jsp', 'asp', 'aspx', 'sh', 'exe', 'pl', 'cgi', 'htaccess'];
        
        const fileModul = form.querySelector('input[name="file_modul"]');
        if (fileModul && fileModul.files && fileModul.files.length > 0) {
            const ext = fileModul.files[0].name.split('.').pop().toLowerCase();
            if (blacklist.includes(ext)) {
                Swal.fire({
                    title: 'File Tidak Diizinkan',
                    text: 'Format file modul tidak diperbolehkan demi keamanan sistem.',
                    icon: 'error',
                    confirmButtonColor: '#10b981'
                });
                return false;
            }
        }
        
        const fileSoal = form.querySelector('input[name="file_soal"]');
        if (fileSoal && fileSoal.files && fileSoal.files.length > 0) {
            const ext = fileSoal.files[0].name.split('.').pop().toLowerCase();
            if (blacklist.includes(ext)) {
                Swal.fire({
                    title: 'File Tidak Diizinkan',
                    text: 'Format file soal tidak diperbolehkan demi keamanan sistem.',
                    icon: 'error',
                    confirmButtonColor: '#10b981'
                });
                return false;
            }
        }

        // Sembunyikan modal
        closeMateriModal();
        closeTugasModal();
        // Tampilkan overlay loading
        document.getElementById('loadingOverlay').classList.remove('hidden');
        return true;
    }

    function copyZoomLink(link) {
        if (!link) {
            Swal.fire({
                title: 'Oops',
                text: 'Link tidak tersedia untuk disalin.',
                icon: 'warning',
                confirmButtonColor: '#10b981'
            });
            return;
        }
        navigator.clipboard.writeText(link).then(() => {
            Swal.fire({
                title: 'Berhasil',
                text: 'Link pertemuan Zoom disalin ke clipboard.',
                icon: 'success',
                confirmButtonColor: '#10b981'
            });
        }).catch(() => {
            Swal.fire({
                title: 'Gagal',
                text: 'Tidak dapat menyalin link. Silakan salin secara manual.',
                icon: 'error',
                confirmButtonColor: '#10b981'
            });
        });
    }

    // --- SINKRONISASI CACHE ---
    async function syncCache() {
        const btn = document.getElementById('btnSync');
        const originalText = btn.innerText;
        
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyinkronkan...';
        btn.disabled = true;

        try {
            const response = await fetch("{{ route('admin.sync_cache') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });
            
            const result = await response.json();
            
            if (result.status === 'success') {
                Swal.fire({
                    title: 'Berhasil Sinkron!',
                    text: 'Seluruh cache sistem telah diperbarui dengan data Google Sheets terbaru.',
                    icon: 'success',
                    confirmButtonColor: '#10b981'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Gagal',
                    text: result.message || 'Terjadi kesalahan sinkronisasi.',
                    icon: 'error',
                    confirmButtonColor: '#10b981'
                });
                btn.innerText = originalText;
                btn.disabled = false;
            }
        } catch (error) {
            Swal.fire({
                title: 'Error',
                text: 'Koneksi ke server terputus.',
                icon: 'error',
                confirmButtonColor: '#10b981'
            });
            btn.innerText = originalText;
            btn.disabled = false;
        }
    }

    // --- GRADE MODAL CONTROLS ---
    function openGradeModal(email, idTugas, nama, judul, nilai, feedback) {
        document.getElementById('gradeEmail').value = email;
        document.getElementById('gradeIdTugas').value = idTugas;
        document.getElementById('gradeStudentName').innerText = nama;
        document.getElementById('gradeTaskTitle').innerText = idTugas + ' - ' + judul;
        document.getElementById('gradeNilai').value = nilai;
        document.getElementById('gradeFeedback').value = feedback;
        document.getElementById('gradeModal').classList.remove('hidden');
    }

    function closeGradeModal() {
        document.getElementById('gradeModal').classList.add('hidden');
    }

    async function submitGrading(e) {
        e.preventDefault();
        
        closeGradeModal();
        document.getElementById('loadingOverlay').classList.remove('hidden');
        
        const form = document.getElementById('gradeForm');
        const formData = new FormData(form);
        const payload = {};
        formData.forEach((value, key) => payload[key] = value);
        
        try {
            const response = await fetch("{{ route('guru.submissions.grade') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });
            
            const result = await response.json();
            document.getElementById('loadingOverlay').classList.add('hidden');
            
            if (response.ok && result.status === 'success') {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Nilai dan feedback siswa berhasil disimpan.',
                    icon: 'success',
                    confirmButtonColor: '#10b981'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Gagal',
                    text: result.message || 'Terjadi kesalahan saat menyimpan nilai.',
                    icon: 'error',
                    confirmButtonColor: '#10b981'
                });
            }
        } catch (error) {
            document.getElementById('loadingOverlay').classList.add('hidden');
            Swal.fire({
                title: 'Error',
                text: 'Koneksi ke server terputus.',
                icon: 'error',
                confirmButtonColor: '#10b981'
            });
        }
    }

    // --- INLINE & BATCH GRADING CONTROLS ---
    function markRowDirty(input) {
        const row = input.closest('tr');
        const gradeInput = row.querySelector('.grade-input');
        const feedbackInput = row.querySelector('.feedback-input');
        
        const currentGrade = gradeInput.value;
        const originalGrade = gradeInput.getAttribute('data-original');
        const currentFeedback = feedbackInput.value;
        const originalFeedback = feedbackInput.getAttribute('data-original');
        
        const isDirty = (currentGrade !== originalGrade) || (currentFeedback !== originalFeedback);
        
        if (isDirty) {
            row.setAttribute('data-dirty', 'true');
            row.classList.add('bg-emerald-50/40');
            row.querySelector('.status-badge').classList.remove('hidden');
            row.querySelector('.save-row-btn').classList.remove('hidden');
            row.querySelector('.save-row-btn').classList.add('inline-flex');
        } else {
            row.removeAttribute('data-dirty');
            row.classList.remove('bg-emerald-50/40');
            row.querySelector('.status-badge').classList.add('hidden');
            row.querySelector('.save-row-btn').classList.add('hidden');
            row.querySelector('.save-row-btn').classList.remove('inline-flex');
        }
        
        updateBulkActionsBar();
    }

    function updateBulkActionsBar() {
        const dirtyRows = document.querySelectorAll('.submission-row[data-dirty="true"]');
        const container = document.getElementById('bulkActionsContainer');
        const countEl = document.getElementById('dirtyRowsCount');
        
        if (dirtyRows.length > 0) {
            countEl.innerText = dirtyRows.length;
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }

    function cancelBatchGrading() {
        const dirtyRows = document.querySelectorAll('.submission-row[data-dirty="true"]');
        dirtyRows.forEach(row => {
            const gradeInput = row.querySelector('.grade-input');
            const feedbackInput = row.querySelector('.feedback-input');
            
            gradeInput.value = gradeInput.getAttribute('data-original');
            feedbackInput.value = feedbackInput.getAttribute('data-original');
            
            row.removeAttribute('data-dirty');
            row.classList.remove('bg-emerald-50/40');
            row.querySelector('.status-badge').classList.add('hidden');
            row.querySelector('.save-row-btn').classList.add('hidden');
            row.querySelector('.save-row-btn').classList.remove('inline-flex');
        });
        
        updateBulkActionsBar();
    }

    async function saveSingleRow(btn) {
        const row = btn.closest('tr');
        const email = row.getAttribute('data-email');
        const idTugas = row.getAttribute('data-idtugas');
        const gradeInput = row.querySelector('.grade-input');
        const feedbackInput = row.querySelector('.feedback-input');
        
        const nilai = gradeInput.value;
        const feedback = feedbackInput.value;
        
        if (nilai === '') {
            Swal.fire({
                title: 'Peringatan',
                text: 'Nilai harus diisi!',
                icon: 'warning',
                confirmButtonColor: '#10b981'
            });
            return;
        }
        if (parseFloat(nilai) < 0 || parseFloat(nilai) > 100) {
            Swal.fire({
                title: 'Peringatan',
                text: 'Nilai harus di antara 0 dan 100!',
                icon: 'warning',
                confirmButtonColor: '#10b981'
            });
            return;
        }

        document.getElementById('loadingOverlay').classList.remove('hidden');
        
        try {
            const response = await fetch("{{ route('guru.submissions.grade') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    email: email,
                    id_tugas: idTugas,
                    nilai: nilai,
                    feedback: feedback
                })
            });
            
            const result = await response.json();
            document.getElementById('loadingOverlay').classList.add('hidden');
            
            if (response.ok && result.status === 'success') {
                // Update original data
                gradeInput.setAttribute('data-original', nilai);
                feedbackInput.setAttribute('data-original', feedback);
                
                // Remove dirty flag
                row.removeAttribute('data-dirty');
                row.classList.remove('bg-emerald-50/40');
                row.querySelector('.status-badge').classList.add('hidden');
                row.querySelector('.save-row-btn').classList.add('hidden');
                row.querySelector('.save-row-btn').classList.remove('inline-flex');
                
                updateBulkActionsBar();
                
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Nilai berhasil disimpan'
                });
            } else {
                Swal.fire({
                    title: 'Gagal',
                    text: result.message || 'Terjadi kesalahan saat menyimpan nilai.',
                    icon: 'error',
                    confirmButtonColor: '#10b981'
                });
            }
        } catch (error) {
            document.getElementById('loadingOverlay').classList.add('hidden');
            Swal.fire({
                title: 'Error',
                text: 'Koneksi ke server terputus.',
                icon: 'error',
                confirmButtonColor: '#10b981'
            });
        }
    }

    async function submitBatchGrading() {
        const dirtyRows = document.querySelectorAll('.submission-row[data-dirty="true"]');
        if (dirtyRows.length === 0) return;
        
        const items = [];
        let hasInvalid = false;
        
        dirtyRows.forEach(row => {
            const email = row.getAttribute('data-email');
            const idTugas = row.getAttribute('data-idtugas');
            const nilai = row.querySelector('.grade-input').value;
            const feedback = row.querySelector('.feedback-input').value;
            
            if (nilai === '') {
                hasInvalid = true;
                return;
            }
            const floatNilai = parseFloat(nilai);
            if (isNaN(floatNilai) || floatNilai < 0 || floatNilai > 100) {
                hasInvalid = true;
                return;
            }
            
            items.push({
                email: email,
                id_tugas: idTugas,
                nilai: floatNilai,
                feedback: feedback
            });
        });
        
        if (hasInvalid) {
            Swal.fire({
                title: 'Peringatan',
                text: 'Mohon pastikan semua nilai terisi dengan angka di antara 0 - 100!',
                icon: 'warning',
                confirmButtonColor: '#10b981'
            });
            return;
        }
        
        document.getElementById('loadingOverlay').classList.remove('hidden');
        
        try {
            const response = await fetch("{{ route('guru.submissions.grade_batch') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ items: items })
            });
            
            const result = await response.json();
            document.getElementById('loadingOverlay').classList.add('hidden');
            
            if (response.ok && result.status === 'success') {
                Swal.fire({
                    title: 'Berhasil!',
                    text: result.message || 'Semua nilai berhasil disimpan.',
                    icon: 'success',
                    confirmButtonColor: '#10b981'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Gagal',
                    text: result.message || 'Terjadi kesalahan saat menyimpan nilai massal.',
                    icon: 'error',
                    confirmButtonColor: '#10b981'
                });
            }
        } catch (error) {
            document.getElementById('loadingOverlay').classList.add('hidden');
            Swal.fire({
                title: 'Error',
                text: 'Koneksi ke server terputus.',
                icon: 'error',
                confirmButtonColor: '#10b981'
            });
        }
    }

    // --- FILTER SUBMISSIONS ---
    function filterSubmissions() {
        const query = document.getElementById('searchSubmissions').value.toLowerCase();
        const tugas = document.getElementById('filterTugas').value;
        const status = document.getElementById('filterStatus').value;
        
        document.querySelectorAll('.submission-row').forEach(row => {
            const nama = row.getAttribute('data-nama') || '';
            const email = row.getAttribute('data-email') || '';
            const idTugas = row.getAttribute('data-idtugas') || '';
            const rowStatus = row.getAttribute('data-status') || '';
            
            const matchesQuery = String(nama).includes(query) || String(email).includes(query);
            const matchesTugas = tugas === '' || String(idTugas) === String(tugas);
            const matchesStatus = status === '' || String(rowStatus) === String(status);
            
            if (matchesQuery && matchesTugas && matchesStatus) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }

    // --- REKAP MATRIKS LOGIC ---
    const allStudents = @json($siswaList ?? []);
    const allTasks = @json($tugas ?? []);
    const allSubmissions = @json($submissions ?? []);

    function renderMatrix() {
        const headerRow = document.getElementById('matrixHeaderRow');
        const body = document.getElementById('matrixBody');
        if (!headerRow || !body) return;

        // Reset
        headerRow.innerHTML = '<th class="px-6 py-4 min-w-[200px]">Nama Siswa</th>';
        body.innerHTML = '';

        if (allStudents.length === 0) {
            body.innerHTML = `
                <tr>
                    <td class="px-6 py-8 text-center text-gray-400" colspan="1">
                        Belum ada siswa terdaftar.
                    </td>
                </tr>
            `;
            return;
        }

        // Sort tasks by id_tugas to display systematically
        const sortedTasks = [...allTasks].sort((a, b) => String(a.id_tugas || '').localeCompare(String(b.id_tugas || '')));

        // Add headers for tasks
        sortedTasks.forEach(task => {
            const th = document.createElement('th');
            th.className = 'px-4 py-4 text-center min-w-[100px] cursor-help';
            th.title = `${task.id_tugas}: ${task.judul} (${task.mapel})`;
            th.innerHTML = `
                <div class="font-bold text-gray-800">${task.id_tugas}</div>
                <div class="text-[8px] text-gray-400 font-semibold normal-case truncate max-w-[90px] mx-auto">${task.judul}</div>
            `;
            headerRow.appendChild(th);
        });

        // Add rows for students
        allStudents.forEach(student => {
            const tr = document.createElement('tr');
            tr.className = 'matrix-row hover:bg-gray-50/50 transition';
            tr.setAttribute('data-nama', String(student.nama || '').toLowerCase());
            tr.setAttribute('data-email', String(student.email || '').toLowerCase());

            // Student profile cell
            const profileCell = document.createElement('td');
            profileCell.className = 'px-6 py-4';
            profileCell.innerHTML = `
                <div class="font-bold text-gray-800">${student.nama}</div>
                <div class="text-[10px] text-gray-400">${student.email}</div>
            `;
            tr.appendChild(profileCell);

            // Grade cells
            sortedTasks.forEach(task => {
                const sub = allSubmissions.find(s => String(s.email || '').toLowerCase() === String(student.email || '').toLowerCase() && String(s.id_tugas) === String(task.id_tugas));
                const td = document.createElement('td');
                td.className = 'px-4 py-4 text-center';

                if (sub) {
                    const hasNilai = sub.nilai !== undefined && sub.nilai !== '' && sub.nilai !== '-';
                    if (hasNilai) {
                        const score = parseInt(sub.nilai);
                        let badgeClass = 'bg-gray-50 text-gray-500 border-gray-150';
                        if (score >= 80) badgeClass = 'bg-green-50 text-green-700 border-green-200';
                        else if (score >= 70) badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                        else badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';

                        td.innerHTML = `
                            <span class="inline-block px-2.5 py-0.5 rounded-lg border text-[10px] font-black ${badgeClass}">
                                ${sub.nilai}
                            </span>
                        `;
                    } else if (sub.link_file) {
                        td.innerHTML = `
                            <span class="inline-block px-2.5 py-0.5 rounded-lg border text-[10px] font-black bg-yellow-50 text-yellow-700 border-yellow-250 animate-pulse" title="Sudah submit, belum dinilai">
                                Proses
                            </span>
                        `;
                    } else {
                        td.innerHTML = `<span class="text-gray-300 font-bold">-</span>`;
                    }
                } else {
                    td.innerHTML = `<span class="text-gray-300 font-bold">-</span>`;
                }
                tr.appendChild(td);
            });

            body.appendChild(tr);
        });
    }

    function filterRekapMatrix() {
        const query = document.getElementById('searchRekap').value.toLowerCase();
        document.querySelectorAll('.matrix-row').forEach(row => {
            const nama = row.getAttribute('data-nama') || '';
            const email = row.getAttribute('data-email') || '';
            if (nama.includes(query) || email.includes(query)) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }

    function exportRekapToCSV() {
        if (allStudents.length === 0) {
            Swal.fire({
                title: 'Info',
                text: 'Tidak ada data untuk diekspor.',
                icon: 'info',
                confirmButtonColor: '#10b981'
            });
            return;
        }

        const sortedTasks = [...allTasks].sort((a, b) => String(a.id_tugas || '').localeCompare(String(b.id_tugas || '')));
        
        // Headers
        const headers = ['Nama Siswa', 'Email', ...sortedTasks.map(t => `${t.id_tugas} (${t.judul})`)];
        const rows = [headers];

        // Student rows
        allStudents.forEach(student => {
            const rowData = [
                String(student.nama || ''),
                String(student.email || '')
            ];

            sortedTasks.forEach(task => {
                const sub = allSubmissions.find(s => String(s.email || '').toLowerCase() === String(student.email || '').toLowerCase() && String(s.id_tugas || '') === String(task.id_tugas || ''));
                if (sub) {
                    const hasNilai = sub.nilai !== undefined && String(sub.nilai || '') !== '' && String(sub.nilai || '') !== '-';
                    if (hasNilai) {
                        rowData.push(String(sub.nilai || ''));
                    } else if (sub.link_file) {
                        rowData.push('Proses (Belum Dinilai)');
                    } else {
                        rowData.push('-');
                    }
                } else {
                    rowData.push('-');
                }
            });

            rows.push(rowData);
        });

        // Generate CSV content
        let csvContent = "data:text/csv;charset=utf-8,";
        rows.forEach(row => {
            // Escape double quotes and wrap values in quotes
            const formattedRow = row.map(val => `"${val.toString().replace(/"/g, '""')}"`).join(",");
            csvContent += formattedRow + "\r\n";
        });

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `Rekap_Nilai_Brevet_Batch_6_${new Date().toISOString().slice(0,10)}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>

<!-- TAB SWITCHER & MODAL BACKDROP CLOSE -->
<script>
    // --- TAB SWITCHING ---
    function switchTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(el => {
            el.classList.add('hidden');
        });

        // Remove active class from all buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
            btn.classList.remove('border-b-2', 'border-emerald-600', 'text-emerald-700');
        });

        // Show selected tab
        const activeTab = document.getElementById(`tab-${tabName}`);
        if (activeTab) {
            activeTab.classList.remove('hidden');
        }

        // Highlight active button
        const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
        if (activeBtn) {
            activeBtn.classList.add('active');
            activeBtn.classList.add('border-b-2', 'border-emerald-600', 'text-emerald-700');
        }

        // Simpan State URL tanpa mereload halaman
        const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=' + tabName;
        window.history.pushState({path: newUrl}, '', newUrl);

        // Sync sidebar active visual if the sync function is exposed
        if (typeof window.syncSidebarActiveState === 'function') {
            window.syncSidebarActiveState(tabName);
        }
    }

    // --- MODAL BACKDROP CLOSE ---
    // Close materi modal when clicking backdrop
    document.getElementById('materiModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeMateriModal();
        }
    });

    // Close tugas modal when clicking backdrop
    document.getElementById('tugasModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeTugasModal();
        }
    });

    // Close grade modal when clicking backdrop
    document.getElementById('gradeModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeGradeModal();
        }
    });

    // Add closeMateriModal function if not exists
    function closeMateriModal() {
        document.getElementById('materiModal').classList.add('hidden');
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

    // --- CHART.JS INITIALIZATION FOR ATTENDANCE ---
    function initAttendanceCharts() {
        const validSiswa = Array.isArray(siswaList) ? siswaList : [];
        const validMapel = Array.isArray(daftarMapel) ? daftarMapel : [];

        // 1. Calculate Doughnut Chart Data (Aman >= 80% vs Butuh Perhatian < 80%)
        let amanCount = 0;
        let perhatianCount = 0;

        validSiswa.forEach(s => {
            if (!s) return;
            const email = s.email ? String(s.email).toLowerCase().trim() : '';
            const studentPresence = currentAbsensiMap[email] || {};

            let totalTarget = 0;
            let totalHadir = 0;

            validMapel.forEach(m => {
                const target = mapelSessionCounts[m] || 0;
                const hadir = studentPresence[m] || 0;
                totalTarget += target;
                totalHadir += hadir;
            });

            const ratio = totalTarget > 0 ? (totalHadir / totalTarget) : 0;
            if (ratio >= 0.8 || totalTarget === 0) {
                amanCount++;
            } else {
                perhatianCount++;
            }
        });

        // If no students/sessions yet, default both to 0
        if (validSiswa.length === 0) {
            amanCount = 0;
            perhatianCount = 0;
        }

        const doughnutCtx = document.getElementById('attendanceDoughnutChart');
        if (doughnutCtx) {
            new Chart(doughnutCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Aman (≥80%)', 'Butuh Perhatian (<80%)'],
                    datasets: [{
                        data: [amanCount, perhatianCount],
                        backgroundColor: ['#10b981', '#f59e0b'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw || 0;
                                    const total = amanCount + perhatianCount;
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return ` ${context.label}: ${value} siswa (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }

        // 2. Calculate Bar Chart Data (Average presence per subject)
        const labels = [];
        const presenceRates = [];

        validMapel.forEach(m => {
            // Find mapel abbreviation for nicer labels
            const abbrev = mapelAbbreviations && mapelAbbreviations[m] ? mapelAbbreviations[m] : m;
            labels.push(abbrev);

            const target = mapelSessionCounts[m] || 0;
            let sumRatio = 0;
            let countSiswa = 0;

            validSiswa.forEach(s => {
                if (!s) return;
                const email = s.email ? String(s.email).toLowerCase().trim() : '';
                const studentPresence = currentAbsensiMap[email] || {};
                const hadir = studentPresence[m] || 0;

                const ratio = target > 0 ? (hadir / target) : 0;
                sumRatio += ratio;
                countSiswa++;
            });

            const avgPercentage = countSiswa > 0 ? Math.round((sumRatio / countSiswa) * 100) : 0;
            presenceRates.push(avgPercentage);
        });

        const barCtx = document.getElementById('attendanceBarChart');
        if (barCtx) {
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Rata-Rata Kehadiran (%)',
                        data: presenceRates,
                        backgroundColor: 'rgba(16, 185, 129, 0.85)', // Emerald-500
                        hoverBackgroundColor: 'rgba(5, 150, 105, 1)', // Emerald-600
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + "%";
                                },
                                font: {
                                    size: 10
                                }
                            },
                            grid: {
                                color: '#f3f4f6'
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 9
                                }
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ` Rata-rata: ${context.raw}%`;
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    function initTugasCharts() {
        const validSiswa = Array.isArray(siswaList) ? siswaList : [];
        const validTugas = Array.isArray(tugasList) ? tugasList : [];
        const validSubmissions = Array.isArray(submissionsList) ? submissionsList : [];

        const totalSiswa = validSiswa.length;

        // 1. Doughnut Chart: Overall Submission Rate
        const totalPossible = totalSiswa * validTugas.length;
        let submittedCount = 0;

        const seenSubmissions = new Set();
        validSubmissions.forEach(sub => {
            if (!sub || !sub.email || !sub.id_tugas) return;
            const key = `${sub.email.toLowerCase().trim()}_${String(sub.id_tugas).trim()}`;
            seenSubmissions.add(key);
        });
        submittedCount = seenSubmissions.size;
        
        if (submittedCount > totalPossible) {
            submittedCount = totalPossible;
        }
        const notSubmittedCount = totalPossible > 0 ? (totalPossible - submittedCount) : 0;

        const doughnutCtx = document.getElementById('tugasDoughnutChart');
        if (doughnutCtx) {
            new Chart(doughnutCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Sudah Kumpul', 'Belum Kumpul'],
                    datasets: [{
                        data: [submittedCount, notSubmittedCount],
                        backgroundColor: ['#6366f1', '#cbd5e1'], // Indigo-500, Slate-300
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw || 0;
                                    const total = totalPossible;
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return ` ${context.label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }

        // 2. Bar Chart: Collection Rate per Tugas ID
        const labels = [];
        const submissionCounts = [];

        validTugas.forEach(t => {
            if (!t || !t.id_tugas) return;
            labels.push(t.id_tugas);

            let count = 0;
            validSubmissions.forEach(sub => {
                if (sub && sub.id_tugas && String(sub.id_tugas).trim() === String(t.id_tugas).trim()) {
                    count++;
                }
            });
            submissionCounts.push(count);
        });

        const barCtx = document.getElementById('tugasBarChart');
        if (barCtx) {
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Sudah Kumpul (Siswa)',
                        data: submissionCounts,
                        backgroundColor: 'rgba(99, 102, 241, 0.85)', // Indigo-500
                        hoverBackgroundColor: 'rgba(79, 70, 229, 1)', // Indigo-600
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                        max: totalSiswa > 0 ? totalSiswa : undefined, // BUG-020: jika kosong, Chart.js otomatis skala
                            ticks: {
                                stepSize: 1,
                                font: {
                                    size: 10
                                }
                            },
                            grid: {
                                color: '#f3f4f6'
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 9
                                }
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ` Mengumpulkan: ${context.raw} dari ${totalSiswa} siswa`;
                                }
                            }
                        }
                    }
                }
            });
        }
    }


    // BUG-001: Fungsi Penilaian Doughnut Chart yang sebelumnya belum ada
    function initPenilaianCharts() {
        const validSubmissions = Array.isArray(submissionsList) ? submissionsList : [];

        if (validSubmissions.length === 0) {
            const ctx = document.getElementById('penilaianDoughnutChart');
            if (ctx) {
                const parentEl = ctx.closest('div[class*="relative"]');
                if (parentEl) {
                    parentEl.innerHTML = '<p class="text-xs text-gray-400 text-center py-8">Belum ada tugas yang dikumpulkan.</p>';
                }
            }
            return;
        }

        let sudahDinilai = 0;
        let belumDinilai = 0;

        validSubmissions.forEach(sub => {
            if (!sub) return;
            const nilai = sub.nilai !== undefined ? String(sub.nilai).trim() : '';
            if (nilai !== '' && nilai !== '-' && nilai !== 'undefined' && nilai !== '0') {
                sudahDinilai++;
            } else {
                belumDinilai++;
            }
        });

        const total = sudahDinilai + belumDinilai;

        const ctx = document.getElementById('penilaianDoughnutChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Sudah Dinilai', 'Belum Dinilai'],
                    datasets: [{
                        data: [sudahDinilai, belumDinilai],
                        backgroundColor: ['#10b981', '#f43f5e'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw || 0;
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return ` ${context.label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });

            // Update legend text di bawah chart
            const legendEl = document.getElementById('penilaianLegendSudah');
            const legendEl2 = document.getElementById('penilaianLegendBelum');
            if (legendEl) legendEl.textContent = sudahDinilai;
            if (legendEl2) legendEl2.textContent = belumDinilai;
        }
    }

    function initDashboard() {
        // Handle Tab Initial dari URL
        const urlParams = new URLSearchParams(window.location.search);
        const initialTab = urlParams.get('tab') || 'dashboard';
        switchTab(initialTab);
        renderMatrix();
        renderAbsenMatrix();
        renderAbsenLog();
        initAttendanceCharts();
        initTugasCharts();
        initPenilaianCharts(); // BUG-001: Sekarang dipanggil

        // Auto-generate ID Tugas ketika Mapel dipilih
        const mapelAbbreviations = {
            "Ketentuan Umum dan Tata Cara Perpajakan (KUP) A & B": "KUP",
            "Pajak Penghasilan (PPh) Orang Pribadi": "PPH-OP",
            "Pajak Pemotongan dan Pemungutan (PPh Pasal 21)": "PPH-21",
            "Pajak Pemotongan dan Pemungutan (PPh Pasal 22, 23, 26, & 4(2))": "PPH-22-23-26",
            "Pajak Penghasilan (PPh) Badan": "PPH-BADAN",
            "Pajak Pertambahan Nilai (PPN) dan PPnBM A & B": "PPN",
            "Pajak Bumi dan Bangunan (PBB), BPHTB, & Bea Meterai": "PBB",
            "Akuntansi Perpajakan": "AKUNTANSI",
            "Pemeriksaan dan Penyidikan Pajak": "PEMERIKSAAN",
            "Pengisian e-SPT / Aplikasi Perpajakan (e-Faktur, dll)": "ESPT",
            "Tax Planning (Perencanaan Pajak)": "TAX-PLAN",
            "Ujian Kelulusan / Komprehensif Brevet": "UJIAN"
        };

        const existingTugas = @json($tugas);
        const mapelSelect = document.querySelector('#tugasForm select[name="mapel"]');
        
        if (mapelSelect) {
            mapelSelect.addEventListener('change', function() {
                // Hanya lakukan auto-generate jika ini form BUAT TUGAS BARU (bukan edit)
                if (document.getElementById('original_id_tugas').value !== '') return;
                
                const mapel = this.value;
                if (!mapel) {
                    document.querySelector('#tugasForm input[name="id_tugas"]').value = '';
                    return;
                }
                
                const abbrev = mapelAbbreviations[mapel] || 'TUGAS';
                
                // Cari counter tertinggi untuk abbrev ini
                let maxNum = 0;
                if (Array.isArray(existingTugas)) {
                    existingTugas.forEach(t => {
                        if (t.mapel === mapel && typeof t.id_tugas === 'string' && t.id_tugas.includes('-')) {
                            const parts = t.id_tugas.split('-');
                            const lastPart = parts[parts.length - 1];
                            const num = parseInt(lastPart);
                            if (!isNaN(num) && num > maxNum) {
                                maxNum = num;
                            }
                        }
                    });
                }
                
                const nextNum = maxNum + 1;
                const formattedNum = String(nextNum).padStart(2, '0');
                
                document.querySelector('#tugasForm input[name="id_tugas"]').value = `TUGAS-${abbrev}-${formattedNum}`;
            });
        }
    }

    if (document.readyState === 'loading') {
        window.addEventListener('DOMContentLoaded', initDashboard);
    } else {
        initDashboard();
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

    // --- REOPEN MODALS WITH OLD INPUTS UPON VALIDATION ERROR ---
    @if($errors->any())
        @if(old('original_mapel'))
            document.addEventListener('DOMContentLoaded', function() {
                const oldMateri = {
                    mapel: @json(old('mapel')),
                    judul: @json(old('judul')),
                    link_modul: @json(old('link_modul')),
                    link_youtube: @json(old('link_youtube')),
                    keterangan: @json(old('keterangan')),
                    status: @json(old('status') ?? 'Rilis'),
                    kelas: @json(old('kelas')),
                    original_mapel: @json(old('original_mapel')),
                    original_judul: @json(old('original_judul'))
                };
                editMateri(oldMateri);
            });
        @elseif(old('judul') && !old('original_mapel') && !old('id_tugas'))
            document.addEventListener('DOMContentLoaded', function() {
                openMateriModal();
                const form = document.getElementById('materiForm');
                form.mapel.value = @json(old('mapel'));
                form.judul.value = @json(old('judul'));
                form.link_modul.value = @json(old('link_modul'));
                if (form.link_youtube) form.link_youtube.value = @json(old('link_youtube'));
                if (form.keterangan) form.keterangan.value = @json(old('keterangan'));
                if (form.status) form.status.value = @json(old('status') ?? 'Rilis');
                if (form.kelas) form.kelas.value = @json(old('kelas'));
            });
        @elseif(old('original_id_tugas'))
            document.addEventListener('DOMContentLoaded', function() {
                const oldTugas = {
                    id_tugas: @json(old('id_tugas')),
                    mapel: @json(old('mapel')),
                    judul: @json(old('judul')),
                    deskripsi: @json(old('deskripsi')),
                    link_soal: @json(old('link_soal')),
                    original_id_tugas: @json(old('original_id_tugas')),
                    deadline: @json(old('deadline_date') ? (old('deadline_date') . ' ' . (old('deadline_hour') ?? '23') . ':' . (old('deadline_minute') ?? '59') . ' WIB') : '')
                };
                editTugas(oldTugas);
                if (document.getElementById('inputTugasBlast')) {
                    document.getElementById('inputTugasBlast').checked = {{ old('blast') ? 'true' : 'false' }};
                }
            });
        @elseif(old('id_tugas') && !old('original_id_tugas'))
            document.addEventListener('DOMContentLoaded', function() {
                openTugasModal();
                const form = document.getElementById('tugasForm');
                form.mapel.value = @json(old('mapel'));
                form.id_tugas.value = @json(old('id_tugas'));
                form.judul.value = @json(old('judul'));
                form.deskripsi.value = @json(old('deskripsi'));
                form.link_soal.value = @json(old('link_soal'));
                form.deadline_date.value = @json(old('deadline_date'));
                form.deadline_hour.value = @json(old('deadline_hour') ?? '23');
                form.deadline_minute.value = @json(old('deadline_minute') ?? '59');
                if (document.getElementById('inputTugasBlast')) {
                    document.getElementById('inputTugasBlast').checked = {{ old('blast') ? 'true' : 'false' }};
                }
            });
        @endif
    @endif



    function openAbsenModal() {
        const modal = document.getElementById('absenModal');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('div').classList.remove('scale-95');
        }, 10);
    }

    function closeAbsenModal() {
        const modal = document.getElementById('absenModal');
        modal.classList.add('opacity-0');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function downloadAbsensi() {
        const filterSearch = document.getElementById('searchAbsen').value.toLowerCase();
        const validSiswaList = Array.isArray(siswaList) ? siswaList : [];
        const filteredSiswa = validSiswaList.filter(s => 
            (s.nama && s.nama.toLowerCase().includes(filterSearch)) || 
            (s.email && s.email.toLowerCase().includes(filterSearch))
        );

        if (filteredSiswa.length === 0) {
            alert('Tidak ada data yang cocok untuk diunduh.');
            return;
        }

        let csvContent = "\uFEFF"; // BOM for Excel encoding support

        // CSV Header
        const headers = ["Nama Siswa", "Email"];
        daftarMapel.forEach(m => {
            const target = mapelSessionCounts[m] || 0;
            if (target > 0) {
                headers.push(`${m} (${target} sesi)`);
            } else {
                headers.push(m);
            }
        });
        csvContent += headers.map(h => `"${h.replace(/"/g, '""')}"`).join(",") + "\n";

        // CSV Rows
        filteredSiswa.forEach(s => {
            const email = (s && s.email) ? String(s.email).toLowerCase().trim() : '';
            const studentPresence = currentAbsensiMap[email] || {};

            const row = [s.nama || '', s.email || ''];
            daftarMapel.forEach(m => {
                const target = mapelSessionCounts[m] || 0;
                const hadir = studentPresence[m] || 0;
                if (target > 0) {
                    row.push(`${hadir}/${target}`);
                } else {
                    row.push(hadir > 0 ? `${hadir} hadir` : '-');
                }
            });
            csvContent += row.map(v => `"${String(v).replace(/"/g, '""')}"`).join(",") + "\n";
        });

        // Trigger file download
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.setAttribute("href", url);

        const dateStr = new Date().toISOString().slice(0,10);
        link.setAttribute("download", `Rekap_Kehadiran_Siswa_${dateStr}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function renderAbsenMatrix() {
        const tbody = document.getElementById('absenMatrixBody');
        if (!tbody) return;
        tbody.innerHTML = '';

        const filterSearch = document.getElementById('searchAbsen').value.toLowerCase();
        const validSiswaList = Array.isArray(siswaList) ? siswaList : [];

        // Filter students based on search term
        const filteredSiswa = validSiswaList.filter(s => 
            (s.nama && s.nama.toLowerCase().includes(filterSearch)) || 
            (s.email && s.email.toLowerCase().includes(filterSearch))
        );

        if (filteredSiswa.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="${daftarMapel.length + 1}" class="text-center py-6 text-gray-400">
                        Tidak ada siswa yang cocok dengan pencarian.
                    </td>
                </tr>
            `;
            return;
        }

        filteredSiswa.forEach(s => {
            const email = (s && s.email) ? String(s.email).toLowerCase().trim() : '';
            const studentPresence = currentAbsensiMap[email] || {};

            let rowHtml = `<tr class="hover:bg-slate-50/50 transition border-b border-gray-100">`;
            rowHtml += `<td class="px-6 py-4 font-semibold text-gray-800 border-r border-gray-150 sticky left-0 bg-white hover:bg-slate-50/50 z-10">${s.nama || ''}<br><span class="text-[10px] text-gray-400 font-normal font-medium">${s.email || ''}</span></td>`;

            daftarMapel.forEach(m => {
                const target = mapelSessionCounts[m] || 0;
                const hadir = studentPresence[m] || 0;

                let cellClass = "text-gray-450";
                let badgeClass = "text-gray-450 border-gray-150";
                
                if (target > 0) {
                    const ratio = hadir / target;
                    if (ratio >= 0.8) {
                        cellClass = "text-emerald-700 font-bold bg-emerald-50/10";
                        badgeClass = "bg-emerald-50 text-emerald-700 border-emerald-150";
                    } else if (ratio > 0) {
                        cellClass = "text-amber-700 font-bold bg-amber-50/10";
                        badgeClass = "bg-amber-50 text-amber-700 border-amber-150";
                    } else {
                        cellClass = "text-red-700 font-bold bg-red-50/10";
                        badgeClass = "bg-red-50 text-red-700 border-red-150";
                    }
                }

                rowHtml += `<td class="px-4 py-4 text-center ${cellClass}">`;
                if (target > 0) {
                    rowHtml += `<span class="inline-block px-2 py-0.5 rounded-full border text-[10px] ${badgeClass}">${hadir}/${target}</span>`;
                } else {
                    if (hadir > 0) {
                        rowHtml += `<span class="inline-block px-2 py-0.5 rounded-full border text-[10px] bg-slate-50 text-slate-600 border-slate-200 font-semibold shadow-sm">${hadir} hadir</span>`;
                    } else {
                        rowHtml += `<span class="text-gray-300 font-medium">-</span>`;
                    }
                }
                rowHtml += `</td>`;
            });

            rowHtml += `</tr>`;
            tbody.insertAdjacentHTML('beforeend', rowHtml);
        });
    }

    function renderAbsenLog() {
        const logBody = document.getElementById('absenLogBody');
        if (!logBody) return;
        logBody.innerHTML = '';

        const filterSearch = document.getElementById('searchAbsen').value.toLowerCase();
        const filterMapel = document.getElementById('filterAbsenMapel').value;

        // Filter log rows
        const filteredLogs = currentAbsensi.filter(log => {
            const logNama = (log && log.nama) ? String(log.nama).toLowerCase() : '';
            const logEmail = (log && log.email) ? String(log.email).toLowerCase() : '';
            
            const matchSearch = !filterSearch || 
                logNama.includes(filterSearch) || 
                logEmail.includes(filterSearch);
            
            const matchMapel = !filterMapel || log.mapel === filterMapel;

            return matchSearch && matchMapel;
        });

        // Sort logs descending by timestamp
        filteredLogs.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));

        if (filteredLogs.length === 0) {
            logBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-6 text-gray-400">
                        Belum ada riwayat kehadiran.
                    </td>
                </tr>
            `;
            return;
        }

        filteredLogs.forEach(log => {
            let formattedDate = log.timestamp;
            try {
                const d = new Date(log.timestamp);
                if (!isNaN(d.getTime())) {
                    formattedDate = d.toLocaleDateString('id-ID', {
                        weekday: 'short',
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    }) + ' - ' + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                }
            } catch (e) {}

            let deleteBtn = '';
            @if(in_array(session('role'), ['ADMIN', 'ADMIN_LMS']))
                deleteBtn = `
                    <td class="py-3.5 pr-4 text-right">
                        <button onclick="confirmDeleteAbsen('${log.email}', '${log.mapel}', '${log.timestamp}')" class="text-red-500 hover:text-red-750 hover:bg-red-50 p-1.5 rounded-lg transition" title="Hapus Kehadiran">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                `;
            @endif

            const row = `
                <tr class="hover:bg-gray-50/50 transition border-b border-gray-50">
                    <td class="py-3.5 pl-4 font-semibold text-gray-500">${formattedDate}</td>
                    <td class="py-3.5 font-bold text-gray-800">
                        ${log.nama}
                        <div class="text-[10px] text-gray-400 font-normal font-medium">${log.email}</div>
                    </td>
                    <td class="py-3.5 font-semibold text-blue-700">${log.mapel}</td>
                    <td class="py-3.5 text-gray-500 font-medium">${log.metode}</td>
                    <td class="py-3.5">
                        <span class="inline-block px-2.5 py-0.5 rounded-full bg-green-50 text-green-700 border border-green-150 text-[9px] font-black">
                            ${log.status || 'HADIR'}
                        </span>
                    </td>
                    ${deleteBtn}
                </tr>
            `;
            logBody.insertAdjacentHTML('beforeend', row);
        });
    }

    function filterAbsen() {
        renderAbsenMatrix();
        renderAbsenLog();
    }

    async function saveAbsenManual(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);

        Swal.fire({
            title: 'Mencatat Kehadiran...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const response = await fetch("{{ route('absensi.store_manual') }}", {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            });
            const result = await response.json();
            if (response.ok && result.status === 'success') {
                Swal.fire({
                    title: 'Berhasil!',
                    text: result.message,
                    icon: 'success',
                    confirmButtonColor: '#059669'
                }).then(() => {
                    closeAbsenModal();
                    window.location.reload();
                });
            } else {
                throw new Error(result.message || 'Gagal menyimpan absensi');
            }
        } catch (e) {
            Swal.fire({
                title: 'Gagal!',
                text: e.message,
                icon: 'error',
                confirmButtonColor: '#dc2626'
            });
        }
    }

    async function confirmDeleteAbsen(email, mapel, timestamp) {
        const result = await Swal.fire({
            title: 'Hapus Kehadiran?',
            text: `Apakah Anda yakin ingin menghapus data absensi siswa ${email} pada mapel ${mapel}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        });

        if (!result.isConfirmed) return;

        Swal.fire({
            title: 'Menghapus Absen...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const response = await fetch("{{ route('absensi.delete') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: JSON.stringify({ email, mapel, timestamp })
            });
            const resData = await response.json();
            if (response.ok && resData.status === 'success') {
                Swal.fire({
                    title: 'Dihapus!',
                    text: resData.message,
                    icon: 'success',
                    confirmButtonColor: '#059669'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error(resData.message || 'Gagal menghapus absensi.');
            }
        } catch (e) {
            Swal.fire({
                title: 'Gagal!',
                text: e.message,
                icon: 'error',
                confirmButtonColor: '#dc2626'
            });
        }
    }
</script>

<style>
    .tab-content {
        animation: fadeIn 0.2s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .tab-btn.active {
        @apply border-b-2 border-emerald-600 text-emerald-700 font-bold;
    }
</style>

@endsection