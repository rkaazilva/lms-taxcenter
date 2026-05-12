@extends('layouts.main')

@section('title', 'Dashboard Siswa')
@section('page_title', 'Selamat Datang, ' . session('nama'))

@section('content')

    {{-- Alert kalau koneksi ke Google gagal --}}
    @if(session('error_koneksi'))
        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-2xl mb-6 text-sm text-red-700">
            <strong>Gagal terhubung ke server jadwal:</strong> {{ session('error_koneksi') }}
        </div>
    @endif

    {{-- ===== ROW 1: KARTU STATISTIK ===== --}}
    <div class="grid md:grid-cols-3 gap-6">

        {{-- Card Progress --}}
        <div class="bg-indigo-600 p-6 rounded-3xl text-white shadow-lg">
            <h3 class="text-sm font-bold opacity-80 uppercase">Progress Belajar</h3>
            <p class="text-4xl font-bold mt-2">85%</p>
            <div class="w-full bg-indigo-400 h-2 rounded-full mt-4">
                <div class="bg-yellow-400 h-2 rounded-full w-[85%]"></div>
            </div>
        </div>

        {{-- Akses Modul --}}
        <a href="{{ session('link') }}" target="_blank"
            class="bg-white p-6 rounded-3xl shadow-sm hover:shadow-xl transition flex flex-col justify-center">
            <div class="bg-indigo-100 w-12 h-12 rounded-2xl flex items-center justify-center mb-4 text-indigo-600">
                <i class="fas fa-book-open text-xl"></i>
            </div>
            <h3 class="font-bold">Buka Modul Pembelajaran</h3>
            <p class="text-xs text-gray-500 mt-1">Klik untuk akses Google Sites eksklusif.</p>
        </a>

        {{-- Jadwal Terdekat (diisi JavaScript) --}}
        <div class="bg-white p-6 rounded-3xl shadow-sm border-l-4 border-yellow-400">
            <h3 class="text-xs font-bold text-gray-400 uppercase mb-2">Jadwal Terdekat</h3>
            <div id="jadwal-loading" class="text-sm text-gray-400 animate-pulse">Mengambil jadwal...</div>
            <div id="jadwal-content" class="hidden">
                <p id="next-matkul" class="font-bold text-indigo-900 text-sm"></p>
                <p id="next-waktu" class="text-xs text-gray-500 mt-1"></p>
                <a id="btn-zoom" href="#" target="_blank"
                    class="inline-block mt-3 px-4 py-2 bg-indigo-100 text-indigo-600 rounded-xl text-[10px] font-bold uppercase">
                    Link Zoom
                </a>
            </div>
        </div>
    </div>

    {{-- ===== ROW 2: UPLOAD TUGAS + MATERI ===== --}}
    <div class="grid md:grid-cols-2 gap-8 mt-8">

        {{-- Form Upload Tugas --}}
        <div class="bg-white p-8 rounded-3xl shadow-sm">
            <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                <i class="fas fa-upload text-indigo-600"></i> Upload Tugas Brevet
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase">Nama & Email (Otomatis)</label>
                    <div class="flex gap-2">
                        <input type="text" id="userName" value="{{ session('nama') }}" readonly
                            class="w-full bg-gray-100 p-3 rounded-xl text-sm">
                        <input type="email" id="userEmail" value="{{ session('email') }}" readonly
                            class="w-full bg-gray-100 p-3 rounded-xl text-sm">
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase">Pilih File Tugas</label>
                    <input type="file" id="fileTugas" class="w-full border-2 border-dashed border-gray-200 p-4 rounded-2xl text-sm
                               file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0
                               file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700
                               hover:file:bg-indigo-100">
                </div>
                <button onclick="uploadKeDrive()" id="btnUpload" class="w-full bg-indigo-600 text-white font-bold py-4 rounded-2xl
                           hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                    Kirim Tugas Sekarang
                </button>
                <p id="uploadStatus" class="text-center text-xs font-medium"></p>
            </div>
        </div>

        {{-- Materi Terbaru --}}
        <div class="bg-white p-8 rounded-3xl shadow-sm">
            <h3 class="font-bold text-lg mb-6">Materi Terbaru</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl">
                    <div class="flex items-center gap-4">
                        <i class="fas fa-file-pdf text-red-500 text-2xl"></i>
                        <div>
                            <p class="font-bold text-sm">Dasar-Dasar PPh 21.pdf</p>
                            <p class="text-[10px] text-gray-400">Diunggah oleh Pak Guru • 2 Jam yang lalu</p>
                        </div>
                    </div>
                    <button class="text-indigo-600 font-bold text-sm hover:underline">Download</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Ganti seluruh bagian tabel lama dengan ini --}}
    <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Jadwal Perkuliahan</h3>
            <div class="flex items-center gap-3">
                <span id="last-update" class="text-xs text-gray-400"></span>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-3 py-1 rounded-full flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse inline-block"></span>
                    Live
                </span>
            </div>
        </div>

        {{-- Loading state --}}
        <div id="tabel-loading" class="p-12 text-center text-gray-400 text-sm animate-pulse">
            Memuat jadwal...
        </div>

        {{-- Tabel (disembunyikan dulu, diisi JS) --}}
        <div id="tabel-wrapper" class="hidden overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold">
                    <tr>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Materi</th>
                        <th class="px-6 py-4">Dosen</th>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tabel-body" class="divide-y divide-gray-100">
                    {{-- Diisi JavaScript --}}
                </tbody>
            </table>
        </div>

        {{-- Empty state --}}
        <div id="tabel-empty" class="hidden p-12 text-center text-gray-400">
            <i class="fas fa-calendar-times text-3xl opacity-30 mb-2 block"></i>
            <p class="text-sm italic">Belum ada jadwal tersedia.</p>
        </div>
    </div>

    {{-- ===== JAVASCRIPT ===== --}}
    <script>
        const scriptURL = "{{ env('API_GOOGLE_SHEET') }}";
        let intervalId = null;

        async function loadSemuaJadwal() {
            try {
                const response = await fetch(`${scriptURL}?action=getJadwal`);
                const data = await response.json();

                // Update widget jadwal terdekat
                if (data && data.length > 0) {
                    const jadwal = data[data.length - 1];
                    document.getElementById('next-matkul').innerText = jadwal.materi || '-';
                    document.getElementById('next-waktu').innerText =
                        `${jadwal.tanggal || ''} | ${jadwal.jam || '-'}`;
                    const btnZoom = document.getElementById('btn-zoom');
                    if (jadwal.link && jadwal.link.startsWith('http')) {
                        btnZoom.href = jadwal.link;
                        btnZoom.classList.remove('hidden');
                    } else {
                        btnZoom.classList.add('hidden');
                    }
                    document.getElementById('jadwal-loading').classList.add('hidden');
                    document.getElementById('jadwal-content').classList.remove('hidden');
                }

                // Update tabel utama
                renderTabel(data);

                // Timestamp update terakhir
                const now = new Date();
                document.getElementById('last-update').innerText =
                    `Update: ${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}:${now.getSeconds().toString().padStart(2, '0')}`;

            } catch (error) {
                console.error("Gagal load jadwal:", error);
                document.getElementById('tabel-loading').innerText = 'Gagal memuat. Mencoba ulang...';
            }
        }

        function renderTabel(data) {
            const loading = document.getElementById('tabel-loading');
            const wrapper = document.getElementById('tabel-wrapper');
            const empty = document.getElementById('tabel-empty');
            const tbody = document.getElementById('tabel-body');

            // Data terbaru di atas
            const dibalik = [...data].reverse();

            if (dibalik.length === 0) {
                loading.classList.add('hidden');
                wrapper.classList.add('hidden');
                empty.classList.remove('hidden');
                return;
            }

            // Render baris tabel
            tbody.innerHTML = dibalik.map(item => `
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 text-gray-500 text-xs">${item.tanggal || '-'}</td>
                <td class="px-6 py-4 font-semibold text-gray-800">${item.materi || '-'}</td>
                <td class="px-6 py-4 text-gray-600">${item.dosen || '-'}</td>
                <td class="px-6 py-4">
                    <span class="bg-indigo-50 text-indigo-600 text-xs font-semibold px-3 py-1 rounded-full">
                        ${item.jam || '-'}
                    </span>
                </td>
                <td class="px-6 py-4">
                    ${item.link && item.link.startsWith('http')
                    ? `<a href="${item.link}" target="_blank"
                              class="inline-flex items-center gap-1 bg-indigo-600 text-white text-xs
                                     font-bold px-4 py-2 rounded-xl hover:bg-indigo-700 transition">
                               <i class="fas fa-video text-[10px]"></i> Join Class
                           </a>`
                    : `<span class="text-xs text-gray-400 italic">Belum ada link</span>`
                }
                </td>
            </tr>
        `).join('');

            loading.classList.add('hidden');
            empty.classList.add('hidden');
            wrapper.classList.remove('hidden');
        }

        // Jalankan pertama kali
        window.addEventListener('load', () => {
            loadSemuaJadwal();

            // Auto refresh setiap 30 detik
            intervalId = setInterval(loadSemuaJadwal, 30000);
        });

        // --- Upload Tugas ke Google Drive ---
        function uploadKeDrive() {
            const fileInput = document.getElementById('fileTugas');
            const btn = document.getElementById('btnUpload');
            const status = document.getElementById('uploadStatus');

            if (fileInput.files.length === 0) {
                alert("Pilih filenya dulu!");
                return;
            }

            const file = fileInput.files[0];
            const reader = new FileReader();

            btn.disabled = true;
            btn.innerText = "Sedang Mengirim...";
            status.className = "text-center text-xs font-medium text-yellow-600";
            status.innerText = "Sabar ya, file lagi meluncur ke Google Drive...";

            reader.onload = function (e) {
                const payload = {
                    base64: e.target.result.split(',')[1],
                    fileName: file.name,
                    mimeType: file.type,
                    nama: document.getElementById('userName').value,
                    email: document.getElementById('userEmail').value
                };

                fetch(scriptURL, {
                    method: "POST",
                    body: JSON.stringify(payload)
                })
                    .then(res => res.json())
                    .then(response => {
                        if (response.status === 'success') {
                            status.className = "text-center text-xs font-medium text-green-600";
                            status.innerText = "✅ Berhasil! Tugasmu sudah tercatat.";
                            fileInput.value = "";
                        } else {
                            throw new Error(response.message);
                        }
                    })
                    .catch(err => {
                        status.className = "text-center text-xs font-medium text-red-600";
                        status.innerText = "❌ Gagal: " + err.message;
                    })
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerText = "Kirim Tugas Sekarang";
                    });
            };

            reader.readAsDataURL(file);
        }

        // Jalankan saat halaman dibuka
        window.addEventListener('load', loadJadwalTerdekat);
        setInterval(loadJadwalTerdekat, 30000);
    </script>

@endsection