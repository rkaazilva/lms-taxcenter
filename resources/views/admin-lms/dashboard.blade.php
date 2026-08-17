@extends('layouts.main')

@section('title', 'Admin LMS Dashboard')
@section('page_title', 'Panel Admin LMS')

@section('content')
<!-- Core Styling Tokens & Custom Animations -->
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
        transform: translateY(-4px);
        box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.08);
    }
    .gradient-violet {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
    }
    .gradient-emerald {
        background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);
    }
    .gradient-amber {
        background: linear-gradient(135deg, #78350f 0%, #92400e 100%);
    }
</style>

<div class="container mx-auto px-4 py-6">
    <!-- Header Section with Purple/Fuchsia Gradient Banner -->
    <div class="bg-gradient-to-br from-violet-500 to-fuchsia-600 rounded-3xl p-6 md:p-8 mb-8 shadow-lg text-white relative overflow-hidden border border-violet-400/20 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-violet-400 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-2">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-white/20 backdrop-blur-md text-white border border-white/10">
                    <i class="fas fa-tools"></i>
                </span>
                <span class="text-xs font-bold text-violet-100 uppercase tracking-widest">System Control</span>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold leading-tight">Panel Admin LMS</h2>
            <p class="text-violet-100 text-xs md:text-sm mt-1">Kelola jadwal pembelajaran, rekapitulasi materi, daftar tugas, dan sinkronisasi basis data.</p>
        </div>
        <div class="relative z-10 flex items-center gap-3 w-full md:w-auto">
            <button onclick="syncAllCache()" class="w-full md:w-auto flex items-center justify-center gap-2 bg-white text-violet-700 hover:bg-violet-50 px-5 py-3 rounded-2xl font-bold text-xs shadow-md shadow-violet-950/20 hover:shadow-lg transition duration-200 group border border-white/10">
                <i class="fas fa-sync-alt group-hover:rotate-180 transition-transform duration-500"></i>
                <span>Sinkronisasi Data Google</span>
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if($errors->any())
    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-100 flex items-start gap-3">
        <i class="fas fa-exclamation-circle text-rose-500 mt-0.5"></i>
        <div>
            <p class="text-rose-900 font-semibold text-xs">Terjadi kesalahan input:</p>
            <ul class="text-rose-700 text-[11px] list-disc list-inside mt-1 space-y-0.5">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    @if(session('success'))
    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-500"></i>
        <p class="text-emerald-800 font-semibold text-xs">Berhasil! {{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-100 flex items-center gap-3">
        <i class="fas fa-times-circle text-rose-500"></i>
        <p class="text-rose-800 font-semibold text-xs">Gagal! {{ session('error') }}</p>
    </div>
    @endif

    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Card 1 -->
        <div class="glass-card p-6 rounded-3xl shadow-sm hover-scale flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Jadwal Aktif</p>
                <p class="text-3xl font-extrabold text-violet-950 mt-1">{{ $totalSesi }}</p>
                <p class="text-gray-400 text-[10px] mt-1.5 font-medium">Sesi pembelajaran terjadwal</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-violet-50 text-violet-900 flex items-center justify-center shadow-sm">
                <i class="fas fa-calendar-day text-lg"></i>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="glass-card p-6 rounded-3xl shadow-sm hover-scale flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Modul Pembelajaran</p>
                <p class="text-3xl font-extrabold text-emerald-950 mt-1">{{ $totalMateri }}</p>
                <p class="text-gray-400 text-[10px] mt-1.5 font-medium">Materi & rekaman video aktif</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-900 flex items-center justify-center shadow-sm">
                <i class="fas fa-book text-lg"></i>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="glass-card p-6 rounded-3xl shadow-sm hover-scale flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Tugas Pelatihan</p>
                <p class="text-3xl font-extrabold text-amber-950 mt-1">{{ $totalTugas }}</p>
                <p class="text-gray-400 text-[10px] mt-1.5 font-medium">Tugas didefinisikan tutor</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-900 flex items-center justify-center shadow-sm">
                <i class="fas fa-tasks text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Navigation Menu Cards -->
    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Pilih Layanan Pengelolaan</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Jadwal Card -->
        <a href="{{ route('admin-lms.jadwal.index') }}" class="glass-card p-6 rounded-3xl shadow-sm hover-scale group block">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-violet-50 text-violet-900 flex items-center justify-center transition group-hover:scale-110">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <i class="fas fa-chevron-right text-gray-300 text-xs transition group-hover:text-violet-900 group-hover:translate-x-1"></i>
            </div>
            <h4 class="font-bold text-md text-violet-950">Manajemen Jadwal</h4>
            <p class="text-[11px] text-gray-500 mt-2 leading-relaxed">Tambah baru, sinkronisasi link Zoom, pemateri, jam, atau hapus jadwal kelas brevet.</p>
        </a>

        <!-- Materi Card -->
        <a href="{{ route('admin-lms.materi.index') }}" class="glass-card p-6 rounded-3xl shadow-sm hover-scale group block">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-900 flex items-center justify-center transition group-hover:scale-110">
                    <i class="fas fa-video"></i>
                </div>
                <i class="fas fa-chevron-right text-gray-300 text-xs transition group-hover:text-emerald-900 group-hover:translate-x-1"></i>
            </div>
            <h4 class="font-bold text-md text-emerald-950">Manajemen Rekaman & Materi</h4>
            <p class="text-[11px] text-gray-500 mt-2 leading-relaxed">Pantau berkas modul PDF yang diunggah tutor dan perbarui/edit link rekaman video YouTube kelas.</p>
        </a>

        <!-- Tugas Card -->
        <a href="{{ route('admin-lms.tugas.index') }}" class="glass-card p-6 rounded-3xl shadow-sm hover-scale group block">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-900 flex items-center justify-center transition group-hover:scale-110">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <i class="fas fa-chevron-right text-gray-300 text-xs transition group-hover:text-amber-900 group-hover:translate-x-1"></i>
            </div>
            <h4 class="font-bold text-md text-amber-950">Manajemen Tugas</h4>
            <p class="text-[11px] text-gray-500 mt-2 leading-relaxed">Pantau ringkasan daftar tugas kelas, batas waktu deadline, dan link file soal tugas.</p>
        </a>

        <!-- Guru Card -->
        <a href="{{ route('admin-lms.guru.index') }}" class="glass-card p-6 rounded-3xl shadow-sm hover-scale group block">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-700 flex items-center justify-center transition group-hover:scale-110">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <i class="fas fa-chevron-right text-gray-300 text-xs transition group-hover:text-rose-950 group-hover:translate-x-1"></i>
            </div>
            <h4 class="font-bold text-md text-rose-950">Manajemen Guru</h4>
            <p class="text-[11px] text-gray-500 mt-2 leading-relaxed">Kelola akun guru/tutor, daftarkan email, petakan mata pelajaran, dan aktifkan/nonaktifkan status.</p>
        </a>
    </div>

    <!-- Recent Jadwal Preview -->
    @if(!empty($jadwal) && count($jadwal) > 0)
    <div class="glass-card p-6 md:p-8 rounded-3xl shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-bold text-md text-violet-950">Ikhtisar Sesi Jadwal Kelas</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">5 sesi terbaru yang dijadwalkan</p>
            </div>
            <a href="{{ route('admin-lms.jadwal.index') }}" class="text-[11px] font-bold text-violet-650 hover:text-violet-800 flex items-center gap-1 transition">
                <span>Kelola Jadwal</span>
                <i class="fas fa-arrow-right text-[9px]"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach(array_slice($jadwal, 0, 4) as $item)
            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 hover:bg-gray-100/70 transition flex justify-between items-start">
                <div class="min-w-0">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-violet-50 text-violet-700 text-[9px] font-semibold mb-2">
                        {{ $item['mapel'] ?? 'Brevet' }}
                    </span>
                    <p class="font-bold text-xs text-gray-800 truncate">{{ $item['materi'] ?? '-' }}</p>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2 text-[10px] text-gray-400 font-medium">
                        <span><i class="fas fa-calendar-alt mr-1"></i>{{ $item['tanggal'] ?? '-' }}</span>
                        <span><i class="fas fa-clock mr-1"></i>{{ $item['jam'] ?? '-' }}</span>
                        <span><i class="fas fa-user mr-1"></i>{{ $item['dosen'] ?? '-' }}</span>
                    </div>
                </div>
                @if(!empty($item['link']))
                <a href="{{ $item['link'] }}" target="_blank" class="flex-shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 hover:bg-blue-100 hover:text-blue-700 transition">
                    <i class="fas fa-video text-xs"></i>
                </a>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- DYNAMIC STATISTICS GRID (DOUGHNUT CARDS ROW) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-8">
        <!-- Doughnut Chart Card 1 (Kehadiran) -->
        <div class="glass-card p-6 rounded-3xl shadow-sm flex flex-col justify-between min-h-[320px]">
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
        <div class="glass-card p-6 rounded-3xl shadow-sm flex flex-col justify-between min-h-[320px]">
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
        <div class="glass-card p-6 rounded-3xl shadow-sm flex flex-col justify-between min-h-[320px]">
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
        <div class="glass-card p-6 rounded-3xl shadow-sm">
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
        <div class="glass-card p-6 rounded-3xl shadow-sm">
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



<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // --- ABSENSI CONTROLS GLOBAL VARIABLES ---
    const siswaList = @json($siswaList ?? []);
    const rawAbsensi = @json($absensi ?? []);
    const siswaAbsensiMap = @json($siswaAbsensiMap ?? []);
    const mapelSessionCounts = @json($mapelSessionCounts ?? []);
    const tugasList = @json($tugas ?? []);
    const submissionsList = @json($submissions ?? []);
    const daftarMapel = [
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
    const mapelAbbreviations = {
        "Ketentuan Umum dan Tata Cara Perpajakan (KUP) A & B" : "KUP",
        "Pajak Penghasilan (PPh) Orang Pribadi" : "PPh OP",
        "Pajak Pemotongan dan Pemungutan (PPh Pasal 21)" : "PPh 21",
        "Pajak Pemotongan dan Pemungutan (PPh Pasal 22, 23, 26, & 4(2))" : "PPh 22-26",
        "Pajak Penghasilan (PPh) Badan" : "PPh Badan",
        "Pajak Pertambahan Nilai (PPN) dan PPnBM A & B" : "PPN",
        "Pajak Bumi dan Bangunan (PBB), BPHTB, & Bea Meterai" : "PBB",
        "Akuntansi Perpajakan" : "Akuntansi",
        "Pemeriksaan dan Penyidikan Pajak" : "Pemeriksaan",
        "Pengisian e-SPT / Aplikasi Perpajakan (e-Faktur, dll)" : "e-SPT",
        "Tax Planning (Perencanaan Pajak)" : "Tax Planning",
        "Ujian Kelulusan / Komprehensif Brevet" : "Ujian"
    };

    let currentAbsensiMap = (siswaAbsensiMap && typeof siswaAbsensiMap === 'object') ? JSON.parse(JSON.stringify(siswaAbsensiMap)) : {};

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
                        backgroundColor: 'rgba(124, 58, 237, 0.85)', // Violet-600
                        hoverBackgroundColor: 'rgba(109, 40, 217, 1)', // Violet-700
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
                            max: totalSiswa > 0 ? totalSiswa : undefined, // BUG-020: Chart.js auto-scale jika kosong
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

    // BUG-001: Fungsi Penilaian Doughnut Chart
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

            const legendEl = document.getElementById('penilaianLegendSudah');
            const legendEl2 = document.getElementById('penilaianLegendBelum');
            if (legendEl) legendEl.textContent = sudahDinilai;
            if (legendEl2) legendEl2.textContent = belumDinilai;
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        initAttendanceCharts();
        initTugasCharts();
        initPenilaianCharts(); // BUG-001: Sekarang dipanggil
    });

    async function syncAllCache() {
        const overlay = document.getElementById('loadingOverlay');
        overlay.classList.remove('hidden');

        try {
            const response = await fetch("{{ route('admin-lms.sync_cache') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });
            
            const result = await response.json();
            overlay.classList.add('hidden');
            
            if (result.status === 'success') {
                Swal.fire({
                    title: 'Sinkronisasi Diproses!',
                    text: result.message || 'Proses sinkronisasi data sedang berjalan di background.',
                    icon: 'success',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#7c3aed',
                    customClass: {
                        popup: 'rounded-3xl',
                        confirmButton: 'rounded-2xl px-5 py-2.5 text-xs font-bold'
                    }
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Gagal Sinkronisasi',
                    text: result.message || 'Terjadi kesalahan sistem.',
                    icon: 'error',
                    confirmButtonColor: '#7c3aed',
                    customClass: {
                        popup: 'rounded-3xl'
                    }
                });
            }
        } catch (error) {
            console.error(error);
            overlay.classList.add('hidden');
            Swal.fire({
                title: 'Error Koneksi',
                text: 'Koneksi ke server terputus.',
                icon: 'error',
                confirmButtonColor: '#7c3aed',
                customClass: {
                    popup: 'rounded-3xl'
                }
            });
        }
    }
</script>
@endsection
