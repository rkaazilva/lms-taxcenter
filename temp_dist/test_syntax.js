
    // --- ABSENSI CONTROLS GLOBAL VARIABLES ---
    const siswaList = [];
    const rawAbsensi = [];
    const siswaAbsensiMap = [];
    const mapelSessionCounts = [];
    const daftarMapel = [];
    
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
    const allStudents = [];
    const allTasks = [];
    const allSubmissions = [];

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

