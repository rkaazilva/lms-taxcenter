@extends('layouts.main')

@section('title', 'Manajemen Jadwal')
@section('page_title', 'Manajemen Jadwal Kelas & Link Zoom')

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
                <span class="text-xs font-bold text-violet-650 uppercase tracking-widest">Schedule Management</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Manajemen Jadwal Kelas 🗓️</h2>
            <p class="text-gray-500 text-xs mt-1">Tambahkan sesi pelatihan baru, perbarui tautan Zoom, nama dosen, atau hapus sesi dari Google Sheets.</p>
        </div>
        <div class="flex flex-wrap gap-2 w-full sm:w-auto">
            <button onclick="exportJadwalToExcel()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl transition text-xs font-bold flex items-center justify-center gap-2">
                <i class="fas fa-file-excel"></i> Export Excel (.xlsx)
            </button>
            <button onclick="openJadwalModal()" class="flex-1 sm:flex-initial bg-violet-600 hover:bg-violet-700 text-white px-4 py-2.5 rounded-xl transition text-xs font-bold flex items-center justify-center gap-2 shadow-md shadow-violet-950/10">
                <i class="fas fa-plus"></i> Tambah Jadwal
            </button>
            <a href="{{ route('admin-lms.index') }}" class="flex-1 sm:flex-initial text-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-xl transition text-xs font-bold flex items-center justify-center gap-2">
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

    @if($errors->any())
    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-100 flex flex-col gap-1.5 shadow-sm">
        <div class="flex items-center gap-3 text-rose-800 font-bold text-xs">
            <i class="fas fa-exclamation-triangle text-rose-500"></i>
            <span>Gagal Validasi Form!</span>
        </div>
        <ul class="text-[10px] text-rose-700 list-disc list-inside pl-6 font-medium">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Daftar Jadwal -->
    <div class="glass-card rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        @if(!empty($jadwal) && count($jadwal) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100/80">
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal & Waktu</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Mata Pelajaran / Sesi</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Dosen Pemateri</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Link Zoom</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100/60">
                        @foreach($jadwal as $item)
                        @php
                            $displayJam = $item['jam'] ?? '-';
                            if ($displayJam !== '-') {
                                $cleanedJam = trim(str_ireplace('WIB', '', $displayJam));
                                $cleanedJam = str_replace('.', ':', $cleanedJam);
                                $timeObj = strtotime($cleanedJam);
                                if ($timeObj !== false) {
                                    $displayJam = date('H:i', $timeObj);
                                }
                            }
                        @endphp
                        <tr class="hover:bg-gray-50/40 transition">
                            <td class="px-6 py-4">
                                <p class="text-xs font-bold text-violet-950">{{ $item['tanggal'] ?? '-' }}</p>
                                <p class="text-[10px] text-gray-400 font-medium mt-1"><i class="far fa-clock mr-1"></i>{{ $displayJam }} WIB</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-violet-50 text-violet-700 text-[9px] font-semibold mb-1 border border-violet-100">
                                    {{ $item['mapel'] ?? '-' }}
                                </span>
                                <p class="text-xs font-semibold text-gray-700 leading-tight">{{ $item['materi'] ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-xs font-medium text-gray-700"><i class="far fa-user text-gray-400 mr-1"></i>{{ $item['dosen'] ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if(!empty($item['link']))
                                    <a href="{{ $item['link'] }}" target="_blank" class="inline-flex items-center gap-1.5 text-[10px] bg-blue-50 text-blue-600 border border-blue-100 px-3 py-1 rounded-xl font-bold hover:bg-blue-100 hover:text-blue-700 transition">
                                        <i class="fas fa-video text-[9px]"></i> Link Zoom
                                    </a>
                                @else
                                    <span class="text-[10px] text-gray-400 font-medium"><i class="fas fa-link-slash mr-1"></i>Belum diset</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <button onclick="editJadwal({{ json_encode($item) }})" class="text-[11px] bg-violet-55 bg-violet-50 text-violet-700 border border-violet-100 hover:bg-violet-100 px-3 py-1 rounded-lg font-bold transition">Edit</button>
                                    <form action="{{ route('admin-lms.jadwal.delete') }}" method="POST" class="inline" onsubmit="return confirmHapus(this);">
                                        @csrf
                                        <input type="hidden" name="tanggal" value="{{ $item['tanggal'] ?? '' }}">
                                        <input type="hidden" name="jam" value="{{ $item['jam'] ?? '' }}">
                                        <input type="hidden" name="dosen" value="{{ $item['dosen'] ?? '' }}">
                                        <button type="submit" class="text-[11px] bg-rose-50 text-rose-600 border border-rose-100 hover:bg-rose-100 px-3 py-1 rounded-lg font-bold transition">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-gray-50 text-gray-300 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar-alt text-2xl"></i>
                </div>
                <p class="text-gray-500 text-xs md:text-sm font-medium">Belum ada sesi jadwal pelatihan aktif.</p>
                <button onclick="openJadwalModal()" class="mt-3 inline-flex items-center gap-2 bg-violet-600 hover:bg-violet-700 text-white px-4 py-2 rounded-xl transition text-xs font-bold">
                    Tambah Jadwal Sekarang
                </button>
            </div>
        @endif
    </div>
</div>

<!-- MODAL FORM JADWAL -->
<div id="jadwalModal" onclick="closeJadwalModal()" class="fixed inset-0 bg-violet-950/60 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm transition duration-300">
    <div class="bg-white rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl transform scale-95 opacity-0 transition-all duration-300 flex flex-col max-h-[90vh]" id="modalContainer" onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 flex-shrink-0">
            <div>
                <h3 id="jadwalModalTitle" class="font-bold text-md text-violet-950">Tambah Jadwal Baru</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Simpan jadwal ke Google Sheets</p>
            </div>
            <button onclick="closeJadwalModal()" class="w-8 h-8 rounded-full bg-gray-100 text-gray-400 hover:bg-rose-50 hover:text-rose-500 flex items-center justify-center transition focus:outline-none">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <!-- Modal Form -->
        <form id="jadwalForm" action="{{ route('admin-lms.jadwal.store') }}" method="POST" class="p-6 space-y-4 overflow-y-auto flex-1" onsubmit="showSubmitLoading()">
            @csrf
            <!-- Original fields untuk identifikasi update baris -->
            <input type="hidden" name="original_tanggal" id="original_tanggal" value="">
            <input type="hidden" name="original_jam" id="original_jam" value="">
            <input type="hidden" name="original_dosen" id="original_dosen" value="">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1.5">Tanggal Pelatihan</label>
                    <input type="date" name="tanggal" required class="w-full rounded-xl border-gray-200 shadow-sm p-3 border text-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1.5">Jam Mulai (Format 24 Jam)</label>
                    <input type="text" name="jam" placeholder="Contoh: 08:00 atau 13:30" pattern="^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$" title="Format waktu harus 24-jam (Contoh: 08:00 atau 13:30)" class="w-full rounded-xl border-gray-200 shadow-sm p-3 border text-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1.5">Mata Pelajaran / Pelatihan</label>
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
                    <select name="mapel" required class="w-full rounded-xl border-gray-200 shadow-sm p-3 border text-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition bg-white">
                        <option value="">-- Pilih Mata Pelajaran --</option>
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
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1.5">Materi / Bahasan Sesi</label>
                <input type="text" name="materi" required placeholder="Contoh: Pertemuan 1 - Pengantar KUP" class="w-full rounded-xl border-gray-200 shadow-sm p-3 border text-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1.5">Dosen / Pemateri</label>
                <input type="text" name="dosen" required placeholder="Nama Dosen Lengkap beserta Gelar" class="w-full rounded-xl border-gray-200 shadow-sm p-3 border text-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1.5">Tautan Zoom (Link Full URL)</label>
                <input type="url" name="link" required placeholder="https://us02web.zoom.us/j/..." class="w-full rounded-xl border-gray-200 shadow-sm p-3 border text-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500 outline-none transition">
            </div>

            <div class="flex items-center gap-2 py-2" id="blastCheckboxContainer">
                <input type="checkbox" name="blast" id="inputBlast" value="1" checked class="rounded border-gray-300 text-violet-900 focus:ring-violet-500">
                <label for="inputBlast" class="text-xs font-bold text-gray-750 cursor-pointer">📢 Kirim Notifikasi Email Otomatis ke Siswa</label>
            </div>

            <div class="flex gap-3 pt-4 border-t border-gray-50">
                <button type="submit" id="jadwalSubmitBtn" class="flex-1 bg-violet-600 hover:bg-violet-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition text-xs flex items-center justify-center gap-1.5">
                    <i class="fas fa-save"></i> Simpan Sesi
                </button>
                <button type="button" onclick="closeJadwalModal()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-4 rounded-xl transition text-xs">Batal</button>
            </div>
        </form>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openJadwalModal() {
        const modal = document.getElementById('jadwalModal');
        const container = document.getElementById('modalContainer');
        const form = document.getElementById('jadwalForm');
        
        // Reset form hanya jika sebelumnya berada di mode Edit (agar draft Tambah baru tetap tersimpan)
        if (form.action.includes('update')) {
            form.reset();
            document.getElementById('original_tanggal').value = '';
            document.getElementById('original_jam').value = '';
            document.getElementById('original_dosen').value = '';
        }
        
        form.action = "{{ route('admin-lms.jadwal.store') }}";
        document.getElementById('jadwalModalTitle').innerText = 'Tambah Jadwal Baru';
        document.getElementById('jadwalSubmitBtn').innerHTML = '<i class="fas fa-save mr-1"></i> Simpan Jadwal';
        document.getElementById('blastCheckboxContainer').classList.remove('hidden');
        
        // Reset input tanggal ke tipe date default
        const tanggalInput = document.querySelector('input[name="tanggal"]');
        tanggalInput.type = 'date';
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            container.classList.remove('scale-95', 'opacity-0');
            container.classList.add('scale-100', 'opacity-100');
        }, 50);
    }

    function closeJadwalModal() {
        const modal = document.getElementById('jadwalModal');
        const container = document.getElementById('modalContainer');
        
        container.classList.remove('scale-100', 'opacity-100');
        container.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function editJadwal(jadwal) {
        const modal = document.getElementById('jadwalModal');
        const container = document.getElementById('modalContainer');
        const form = document.getElementById('jadwalForm');
        
        form.action = "{{ route('admin-lms.jadwal.update') }}";
        document.getElementById('jadwalModalTitle').innerText = 'Edit Jadwal Sesi';
        document.getElementById('jadwalSubmitBtn').innerHTML = '<i class="fas fa-save mr-1"></i> Perbarui Jadwal';
        
        document.getElementById('original_tanggal').value = jadwal.tanggal ?? '';
        document.getElementById('original_jam').value = jadwal.jam ?? '';
        document.getElementById('original_dosen').value = jadwal.dosen ?? '';
        document.getElementById('blastCheckboxContainer').classList.remove('hidden');
        document.getElementById('inputBlast').checked = true;
        
        // Tentukan tipe input tanggal secara dinamis untuk mendukung format legacy (seperti rabu/26 juni)
        const tanggalInput = form.querySelector('input[name="tanggal"]');
        const valTanggal = jadwal.tanggal ?? '';
        if (/^\d{4}-\d{2}-\d{2}$/.test(valTanggal)) {
            tanggalInput.type = 'date';
        } else {
            tanggalInput.type = 'text';
        }
        tanggalInput.value = valTanggal;
        
        let rawJam = jadwal.jam ?? '';
        let formattedJam = '';
        if (rawJam) {
            rawJam = rawJam.replace(/WIB/gi, '').trim().replace(/\./g, ':');
            const match = rawJam.match(/(\d{1,2}):(\d{2})\s*(AM|PM)?/i);
            if (match) {
                let hours = parseInt(match[1]);
                const minutes = match[2];
                const ampm = match[3];
                if (ampm) {
                    if (ampm.toUpperCase() === 'PM' && hours < 12) {
                        hours += 12;
                    } else if (ampm.toUpperCase() === 'AM' && hours === 12) {
                        hours = 0;
                    }
                }
                formattedJam = String(hours).padStart(2, '0') + ':' + minutes;
            }
        }
        form.querySelector('input[name="jam"]').value = formattedJam || rawJam;
        // Cari kecocokan mapel secara cerdas agar tidak kosong saat edit
        let mapelValue = "";
        const selectMapel = form.querySelector('select[name="mapel"]');
        if (jadwal.mapel) {
            mapelValue = jadwal.mapel;
        } else if (jadwal.materi) {
            const cleanMateri = jadwal.materi.toLowerCase();
            for (let option of selectMapel.options) {
                if (option.value && (cleanMateri.includes(option.value.toLowerCase()) || option.value.toLowerCase().includes(cleanMateri))) {
                    mapelValue = option.value;
                    break;
                }
            }
            if (!mapelValue) {
                if (cleanMateri.includes("kup")) {
                    mapelValue = "Ketentuan Umum dan Tata Cara Perpajakan (KUP) A & B";
                } else if (cleanMateri.includes("pph orang pribadi") || cleanMateri.includes("pph op")) {
                    mapelValue = "Pajak Penghasilan (PPh) Orang Pribadi";
                } else if (cleanMateri.includes("pph pasal 21") || cleanMateri.includes("pph 21")) {
                    mapelValue = "Pajak Pemotongan dan Pemungutan (PPh Pasal 21)";
                } else if (cleanMateri.includes("22") || cleanMateri.includes("23") || cleanMateri.includes("26")) {
                    mapelValue = "Pajak Pemotongan dan Pemungutan (PPh Pasal 22, 23, 26, & 4(2))";
                } else if (cleanMateri.includes("pph badan")) {
                    mapelValue = "Pajak Penghasilan (PPh) Badan";
                } else if (cleanMateri.includes("ppn")) {
                    mapelValue = "Pajak Pertambahan Nilai (PPN) dan PPnBM A & B";
                } else if (cleanMateri.includes("pbb") || cleanMateri.includes("bphtb") || cleanMateri.includes("bea meterai")) {
                    mapelValue = "Pajak Bumi dan Bangunan (PBB), BPHTB, & Bea Meterai";
                } else if (cleanMateri.includes("akuntansi")) {
                    mapelValue = "Akuntansi Perpajakan";
                } else if (cleanMateri.includes("pemeriksaan") || cleanMateri.includes("penyidikan")) {
                    mapelValue = "Pemeriksaan dan Penyidikan Pajak";
                } else if (cleanMateri.includes("e-spt") || cleanMateri.includes("aplikasi") || cleanMateri.includes("e-faktur")) {
                    mapelValue = "Pengisian e-SPT / Aplikasi Perpajakan (e-Faktur, dll)";
                } else if (cleanMateri.includes("planning") || cleanMateri.includes("perencanaan")) {
                    mapelValue = "Tax Planning (Perencanaan Pajak)";
                } else if (cleanMateri.includes("ujian") || cleanMateri.includes("komprehensif")) {
                    mapelValue = "Ujian Kelulusan / Komprehensif Brevet";
                }
            }
        }
        selectMapel.value = mapelValue;
        form.querySelector('input[name="materi"]').value = jadwal.materi ?? '';
        form.querySelector('input[name="dosen"]').value = jadwal.dosen ?? '';
        form.querySelector('input[name="link"]').value = jadwal.link ?? '';
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            container.classList.remove('scale-95', 'opacity-0');
            container.classList.add('scale-100', 'opacity-100');
        }, 50);
    }

    function showSubmitLoading() {
        document.getElementById('loadingText').innerText = 'Menyimpan jadwal ke Google Sheets...';
        document.getElementById('loadingOverlay').classList.remove('hidden');
        closeJadwalModal();
    }

    function confirmHapus(form) {
        Swal.fire({
            title: 'Hapus Sesi Jadwal?',
            text: "Data jadwal akan dihapus selamanya dari Google Sheets.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#475569',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-3xl',
                confirmButton: 'rounded-2xl px-4 py-2 text-xs font-bold',
                cancelButton: 'rounded-2xl px-4 py-2 text-xs font-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('loadingText').innerText = 'Menghapus jadwal dari Google Sheets...';
                document.getElementById('loadingOverlay').classList.remove('hidden');
                form.submit();
            }
        });
        return false;
    }

    // --- REOPEN MODAL WITH OLD INPUTS UPON VALIDATION ERROR ---
    @if($errors->any())
        @if(old('original_tanggal'))
            document.addEventListener('DOMContentLoaded', function() {
                const oldJadwal = {
                    tanggal: @json(old('tanggal')),
                    jam: @json(old('jam')),
                    mapel: @json(old('mapel')),
                    materi: @json(old('materi')),
                    dosen: @json(old('dosen')),
                    link: @json(old('link')),
                    original_tanggal: @json(old('original_tanggal')),
                    original_jam: @json(old('original_jam')),
                    original_dosen: @json(old('original_dosen'))
                };
                editJadwal(oldJadwal);
                
                // Keep the checkbox value
                const blastInput = document.getElementById('inputBlast');
                if (blastInput) {
                    blastInput.checked = {{ old('blast') ? 'true' : 'false' }};
                }
            });
        @else
            document.addEventListener('DOMContentLoaded', function() {
                openJadwalModal();
                
                const form = document.getElementById('jadwalForm');
                form.tanggal.value = @json(old('tanggal'));
                form.jam.value = @json(old('jam'));
                form.mapel.value = @json(old('mapel'));
                form.materi.value = @json(old('materi'));
                form.dosen.value = @json(old('dosen'));
                form.link.value = @json(old('link'));
                
                // Keep the checkbox value
                const blastInput = document.getElementById('inputBlast');
                if (blastInput) {
                    blastInput.checked = {{ old('blast') ? 'true' : 'false' }};
                }
            });
        @endif
    function exportJadwalToExcel() {
        const table = document.querySelector('table');
        if (!table) return;

        const dataRows = [
            ["Tanggal & Jam", "Mata Pelajaran", "Judul Topik / Materi", "Dosen / Tutor", "Link Zoom", "Link Rekaman"]
        ];

        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(tr => {
            const tds = tr.querySelectorAll('td');
            if (tds.length >= 5) {
                const zoomLink = tds[4].querySelector('a') ? tds[4].querySelector('a').href : '-';
                dataRows.push([
                    tds[0].innerText.replace(/\n/g, ' ').trim(),
                    tds[1].innerText.trim(),
                    tds[2].innerText.trim(),
                    tds[3].innerText.trim(),
                    zoomLink,
                    '-'
                ]);
            }
        });

        const dateStr = new Date().toISOString().slice(0,10);

        if (typeof XLSX !== 'undefined') {
            const ws = XLSX.utils.aoa_to_sheet(dataRows);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Jadwal Kelas");
            XLSX.writeFile(wb, `Jadwal_Kelas_LMS_${dateStr}.xlsx`);
        } else {
            alert('Library Excel belum siap.');
        }
    }
</script>
@endsection
