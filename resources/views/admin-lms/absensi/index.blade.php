@extends('layouts.main')

@section('title', 'Kehadiran Siswa - Admin LMS')
@section('page_title', 'Kehadiran & Absensi Siswa')

@section('content')

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Rekapitulasi Kehadiran Siswa</h3>
            <p class="text-xs text-gray-500 mt-0.5">Pantau dan kelola kehadiran belajar peserta Brevet Pajak</p>
        </div>
        <div class="flex gap-2">
            <button onclick="downloadAbsensi()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-md transition text-xs flex items-center gap-1.5">
                <i class="fas fa-download"></i> Unduh Rekap (CSV)
            </button>
            <button onclick="openAbsenModal()" class="bg-violet-600 hover:bg-violet-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-md transition text-xs flex items-center gap-1.5">
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
                <input type="text" id="searchAbsen" onkeyup="filterAbsen()" placeholder="Ketik nama atau email..." class="w-full bg-slate-50 border border-gray-200 rounded-xl pl-9 pr-4 py-2 text-xs focus:ring-1 focus:ring-violet-500 focus:border-violet-500 outline-none transition font-medium">
            </div>
        </div>
        <div>
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-wider mb-1.5">Filter Mata Pelajaran</label>
            <select id="filterAbsenMapel" onchange="filterAbsen()" class="w-full bg-slate-50 border border-gray-200 rounded-xl px-3 py-2 text-xs focus:ring-1 focus:ring-violet-500 focus:border-violet-500 outline-none transition font-medium">
                <option value="">-- Semua Mata Pelajaran --</option>
                @foreach($daftarMapel ?? [] as $m)
                    <option value="{{ $m }}">{{ $m }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Matriks Kehadiran -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
        <h4 class="font-bold text-gray-800 text-sm mb-4"><i class="fas fa-th-large text-violet-650 mr-2"></i> Matriks Kehadiran Siswa</h4>
        
        <div class="overflow-x-auto rounded-2xl border border-gray-150">
            <table class="min-w-full divide-y divide-gray-150 text-xs text-left">
                <thead class="bg-gray-50 text-gray-500 uppercase font-bold text-[9px] tracking-wider border-b border-gray-150">
                    <tr>
                        <th class="px-6 py-4 min-w-[200px] border-r border-gray-150">Nama Siswa</th>
                        @foreach($daftarMapel ?? [] as $m)
                            @php
                                $mapelAbbreviations = [
                                    "Ketentuan Umum dan Tata Cara Perpajakan (KUP) A & B" => "KUP",
                                    "Pajak Penghasilan (PPh) Orang Pribadi" => "PPh OP",
                                    "Pajak Pemotongan dan Pemungutan (PPh Pasal 21)" => "PPh 21",
                                    "Pajak Pemotongan dan Pemungutan (PPh Pasal 22, 23, 26, & 4(2))" => "PPh 22-26",
                                    "Pajak Penghasilan (PPh) Badan" => "PPh Badan",
                                    "Pajak Pertambahan Nilai (PPN) dan PPnBM A & B" => "PPN",
                                    "Pajak Bumi dan Bangunan (PBB), BPHTB, & Bea Meterai" => "PBB",
                                    "Akuntansi Perpajakan" => "Akuntansi",
                                    "Pemeriksaan dan Penyidikan Pajak" => "Pemeriksaan",
                                    "Pengisian e-SPT / Aplikasi Perpajakan (e-Faktur, dll)" => "e-SPT",
                                    "Tax Planning (Perencanaan Pajak)" => "Tax Planning",
                                    "Ujian Kelulusan / Komprehensif Brevet" => "Ujian"
                                ];
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
        <h4 class="font-bold text-gray-800 text-sm mb-4"><i class="fas fa-history text-violet-650 mr-2"></i> Log Presensi Terakhir</h4>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-400 font-bold uppercase tracking-wider">
                        <th class="pb-3 pl-4">Tanggal & Waktu</th>
                        <th class="pb-3">Siswa</th>
                        <th class="pb-3">Mata Pelajaran</th>
                        <th class="pb-3">Metode</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3 pr-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-700" id="absenLogBody">
                    <!-- Dynamic Log Rows -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL INPUT ABSENSI MANUAL -->
<div id="absenModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl p-6 md:p-8 max-w-md w-full mx-4 shadow-2xl transform scale-95 transition-transform duration-300">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-user-check text-violet-600"></i> Input Kehadiran Manual
            </h3>
            <button onclick="closeAbsenModal()" class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        <form id="absenForm" onsubmit="saveAbsenManual(event)" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Siswa</label>
                <select name="email" required class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border text-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($siswaList ?? [] as $s)
                        <option value="{{ $s['email'] }}">{{ $s['nama'] }} ({{ $s['email'] }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Mata Pelajaran</label>
                <select name="mapel" required class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border text-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach($daftarMapel ?? [] as $m)
                        <option value="{{ $m }}">{{ $m }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border text-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Jam WIB</label>
                    <input type="time" name="jam" required value="09:00" class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border text-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Metode Kehadiran</label>
                <select name="metode" required class="w-full rounded-xl border-gray-300 shadow-sm p-2.5 border text-xs focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                    <option value="Manual (Admin)" selected>Manual (Admin)</option>
                    <option value="Live Zoom">Live Zoom</option>
                    <option value="Nonton Rekaman YouTube">Nonton Rekaman YouTube</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-violet-600 hover:bg-violet-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg transition text-xs flex items-center justify-center gap-2">
                <i class="fas fa-check-circle"></i> Catat Kehadiran
            </button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // --- ABSENSI CONTROLS ---
    const siswaList = @json($siswaList ?? []);
    const rawAbsensi = @json($absensi ?? []);
    const siswaAbsensiMap = @json($siswaAbsensiMap ?? []);
    const mapelSessionCounts = @json($mapelSessionCounts ?? []);
    const daftarMapel = @json($daftarMapel ?? []);
    
    let currentAbsensi = Array.isArray(rawAbsensi) ? [...rawAbsensi] : [];
    let currentAbsensiMap = (siswaAbsensiMap && typeof siswaAbsensiMap === 'object') ? JSON.parse(JSON.stringify(siswaAbsensiMap)) : {};

    document.addEventListener("DOMContentLoaded", () => {
        renderAbsenMatrix();
        renderAbsenLog();
    });

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
        const matrixBody = document.getElementById('absenMatrixBody');
        if (!matrixBody) return;
        matrixBody.innerHTML = '';

        const filterSearch = document.getElementById('searchAbsen').value.toLowerCase();
        
        const validSiswaList = Array.isArray(siswaList) ? siswaList : [];
        const filteredSiswa = validSiswaList.filter(s => 
            s && (
                (s.nama && s.nama.toLowerCase().includes(filterSearch)) || 
                (s.email && s.email.toLowerCase().includes(filterSearch))
            )
        );

        if (filteredSiswa.length === 0) {
            matrixBody.innerHTML = `
                <tr>
                    <td colspan="${daftarMapel.length + 1}" class="text-center py-6 text-gray-400">
                        Tidak ada siswa yang cocok dengan pencarian.
                    </td>
                </tr>
            `;
            return;
        }

        filteredSiswa.forEach(s => {
            const email = s.email ? String(s.email).toLowerCase().trim() : '';
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
                        cellClass = "text-violet-700 font-bold bg-violet-50/10";
                        badgeClass = "bg-violet-50 text-violet-700 border-violet-150";
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
            matrixBody.insertAdjacentHTML('beforeend', rowHtml);
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
            if (!log) return false;
            const logNama = log.nama ? String(log.nama).toLowerCase() : '';
            const logEmail = log.email ? String(log.email).toLowerCase() : '';
            
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

            const deleteBtn = `
                <td class="py-3.5 pr-4 text-right">
                    <button onclick="confirmDeleteAbsen('${log.email}', '${log.mapel}', '${log.timestamp}')" class="text-red-500 hover:text-red-755 hover:bg-red-50 p-1.5 rounded-lg transition" title="Hapus Kehadiran">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            `;

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
                    confirmButtonColor: '#7c3aed'
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
                    confirmButtonColor: '#7c3aed'
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

@endsection
