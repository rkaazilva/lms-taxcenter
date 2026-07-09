# LMS Tax Center - AI Context & Developer Guidelines

Dokumen ini adalah panduan konteks bagi AI Assistant agar langsung memahami arsitektur, batasan, fitur, dan status pengerjaan proyek **LMS Tax Center** saat memulai sesi chat baru.

---

## 1. Rangkuman Proyek & Tech Stack
* **Framework**: Laravel 12 (PHP 8.2+).
* **Frontend**: Blade Template + Tailwind CSS v4 (via `@tailwindcss/vite` & Vite).
* **Database Utama**: **Google Sheets & Google Drive** via Google Apps Script (REST API).
* **Database Lokal**: **SQLite** (`database/database.sqlite`) khusus untuk artikel publik dan autentikasi admin panel artikel.
* **Autentikasi LMS**: Berbasis Sesi (`file` session driver). Login memvalidasi data langsung ke Google Sheets API.

---

## 2. Aturan Caching & Komunikasi API (PENTING!)
* **Caching**: Data statis (Jadwal, Materi, Tugas) dan Absensi (`lms_absensi`) disimpan di cache Laravel mengikuti `LMS_CACHE_TTL` (default 24 jam / 86400 detik) untuk stabilitas server dan mencegah timeout.
* **Real-time**: Rekap Nilai Siswa dan absensi individual siswa diambil secara langsung tanpa cache.
* **Sync Cache**: Admin dan Guru dapat memaksa pembersihan cache melalui tombol sinkronisasi. Tombol ini juga memicu pemanasan cache secara sekuensial.
* **Komunikasi API Sekuensial**: Panggilan ke API Google Apps Script **TIDAK BOLEH** menggunakan `Http::pool` secara paralel karena dapat menyebabkan DNS/SSL timeout di server lokal dan bottleneck di Apps Script. Semua panggilan harus dilakukan secara **sekuensial**.
* **Method API**: Action `getAllAbsensi` wajib dipanggil via request `POST` (`postToApi`) karena didaftarkan di dalam handler `doPost()` Apps Script.
* **Invalidasi Cache**: Cache `lms_absensi` otomatis dibersihkan (`Cache::forget('lms_absensi')`) saat siswa melakukan absen mandiri (`catatAbsen`), manual input, maupun penghapusan log absen.
* **Error Handling**: Semua call HTTP ke API Google Sheets harus dibungkus dalam blok `try-catch` dan direkam di `Log::error` atau `Log::warning`.

---

## 3. Matriks Fitur LMS & Status Detail

Berikut adalah spesifikasi detail 10 fitur utama LMS (mirip konsep Google Classroom) beserta status pengerjaannya saat ini:

### 1. Manajemen Kelas & Materi
*   **Pembuatan kelas/course berdasarkan topik/subjek**: **[Selesai]** Menggunakan pemetaan Mata Pelajaran Brevet di Google Sheets.
*   **Upload materi (PDF, video, link, dll)**: **[Selesai]** Tutor mengunggah judul materi, link modul, dan link YouTube yang tersimpan ke Google Sheets.
*   **Pengaturan akses materi (per kelas / per peserta)**: **[Selesai]** Menggunakan pemfilteran dinamis berdasarkan target kelas/batch yang diatur saat tutor mengunggah materi.

### 2. Tugas & Penilaian
*   **Fitur pengiriman tugas oleh peserta**: **[Selesai]** Siswa mengunggah berkas yang di-convert ke Base64, dikirim ke Google Drive, dan dicatat di sheet `SUBMISSION_TUGAS`.
*   **Deadline tugas dengan pengingat otomatis**: **[Selesai]** Trigger berbasis waktu di Apps Script secara otomatis memeriksa sisa waktu tugas H-1 dan mengirimkan email reminder HTML kepada siswa yang belum mengumpulkan.
*   **Tutor/Dosen bisa melihat submission tugas**: **[Selesai]** Dosen dapat melihat daftar file tugas yang diunggah siswa secara langsung melalui tab **Penilaian** di Panel Guru.
*   **Tutor/Dosen bisa memberi nilai & feedback/komentar**: **[Selesai]** Memberikan nilai & feedback dapat dilakukan secara langsung di tab **Penilaian** (tunggal maupun massal/batch) dan otomatis terupdate ke Google Sheets serta memicu email notifikasi ke siswa.
*   **Rekap nilai per peserta & per kelas**: **[Selesai]** Dashboard siswa menampilkan rekap nilai real-time. Di Panel Guru juga sudah tersedia tab **Rekap Nilai Kelas** (tabel matriks dinamis yang dapat diekspor ke CSV).
*   **Rekap kehadiran per peserta & per kelas**: **[Selesai]** Ditampilkan di dashboard Guru (tab Kehadiran Siswa) dan portal Admin LMS (`admin-lms/absensi`). Dilengkapi tombol **Unduh Rekap (CSV)** dengan dukungan BOM `\uFEFF` (untuk Excel) dan filter pencarian terintegrasi. Jumlah sesi target dihitung secara dinamis dari **jumlah Materi** terunggah (dengan fallback default Brevet jika materi masih 0).

### 3. Notifikasi & Email
*   **Notifikasi sistem (Tugas baru, deadline, pengumuman)**: **[Selesai]** Diimplementasikan sebagai notifikasi internal (in-app) dengan ikon lonceng dinamis di header layout utama (disimpan di SQLite lokal).
*   **Notifikasi email otomatis (Tugas baru, nilai keluar, pengumuman penting)**: **[Selesai]** Notifikasi email otomatis untuk tugas baru, nilai keluar, dan jadwal baru sudah diimplementasikan menggunakan template HTML formal di Google Apps Script.

### 4. Manajemen Tutor / Dosen
*   **Daftar tutor/dosen yang mengajar**: **[Selesai]** Dikelola via database Google Sheets.
*   **Pengaturan tutor berdasarkan subjek/materi**: **[Selesai]** Tutor terikat dengan mata pelajaran yang diajar.
*   **Hak akses tutor (upload materi, kirim tugas, kirim link meeting, beri nilai/feedback)**: **[Selesai]** Hak akses upload materi, tugas, link meeting, serta pengisian nilai dan feedback via UI sudah berjalan sepenuhnya di controller.

### 5. Meeting Online
*   **Tutor mengirim link pertemuan (Zoom/Google Meet/dll)**: **[Selesai]** Bisa dimasukkan di kolom link kelas/jadwal.
*   **Jadwal meeting tersimpan di kelas**: **[Selesai]** Terintegrasi di tabel jadwal Google Sheets yang ditampilkan di dashboard siswa.
*   **Notifikasi ke peserta saat link dibagikan**: **[Selesai]** Email blast otomatis dikirim ke siswa ketika jadwal kelas dirilis/diperbarui dengan opsi blast aktif.

### 6. Pendaftaran Peserta
*   **Fitur pendaftaran online**: **[Selesai]** Melalui form eksternal.
*   **Form pendaftaran peserta (data diri, email, dll)**: **[Selesai]** Menggunakan Google Forms.
*   **Peserta otomatis terdaftar setelah daftar**: **[Selesai]** Ketika Admin menandai status `LUNAS` di sheet `DATABASE_PESERTA`, pemicu `onEdit` (`kirimEmailOtomatis`) akan secara otomatis membuat akun login di `DATA_LOGIN` dan mengirimkan email kredensial otomatis kepada siswa.

### 7. Pembayaran
*   **Sistem pembayaran langsung oleh peserta**: **[Belum]** Belum diimplementasikan di web.
*   **Integrasi payment gateway (transfer, e-wallet, dll)**: **[Belum]** Belum diimplementasikan.
*   **Status pembayaran (pending/berhasil/gagal) & Aktivasi otomatis**: **[Belum]** Akses kelas diaktifkan secara manual oleh Admin setelah pembayaran dikonfirmasi di luar sistem.

### 8. Sertifikat
*   **Sertifikat digital otomatis**: **[Sebagian]** URL sertifikat dikirimkan dari Google Sheets API jika siswa dinyatakan lulus.
*   **Sertifikat dikirim langsung ke peserta & bisa di-download (PDF)**: **[Selesai]** Siswa dapat mengunduh sertifikat berformat PDF langsung dari tombol sertifikat di dashboard jika datanya tersedia di API login.

### 9. Form & Integrasi API (Admin / Sistem Lain)
*   **Form khusus kebutuhan internal & Integrasi API**: **[Sebagian]** API terhubung ke endpoint Apps Script Google Sheets untuk sinkronisasi data.
*   **Akses API terbatas sesuai role (admin saja)**: **[Selesai]** Dilindungi oleh `API_LMS_TOKEN` di file `.env` dan Apps Script.

### 10. Role & Hak Akses
*   **Role pengguna (Admin, Tutor/Dosen, Peserta)**: **[Selesai]** Dikelola melalui session role `ADMIN`, `TUTOR`/`GURU`/`Dosen`, dan `SISWA`/`Peserta`.
*   **Akses fitur berbeda per role**: **[Selesai]** Dilindungi oleh middleware `CekLogin` dan pengecekan role di controller/view masing-masing dashboard.

---

## 4. File Utama yang Sering Diedit
* **Routing**: [routes/web.php](file:///c:/Users/rakaz/lms-taxcenter/routes/web.php)
* **Google Sheet Service**: [app/Services/GoogleSheetService.php](file:///c:/Users/rakaz/lms-taxcenter/app/Services/GoogleSheetService.php)
* **LMS Controller**: [app/Http/Controllers/LmsController.php](file:///c:/Users/rakaz/lms-taxcenter/app/Http/Controllers/LmsController.php)
* **Guru Controller**: [app/Http/Controllers/GuruController.php](file:///c:/Users/rakaz/lms-taxcenter/app/Http/Controllers/GuruController.php)
* **Admin LMS Controller**: [app/Http/Controllers/AdminLmsController.php](file:///c:/Users/rakaz/lms-taxcenter/app/Http/Controllers/AdminLmsController.php)
* **Auth Controller**: [app/Http/Controllers/AuthController.php](file:///c:/Users/rakaz/lms-taxcenter/app/Http/Controllers/AuthController.php)
* **Guru Model**: [app/Models/Guru.php](file:///c:/Users/rakaz/lms-taxcenter/app/Models/Guru.php)
* **Guru Dashboard View**: [resources/views/guru/dashboard.blade.php](file:///c:/Users/rakaz/lms-taxcenter/resources/views/guru/dashboard.blade.php)
* **Layout Utama**: [resources/views/layouts/main.blade.php](file:///c:/Users/rakaz/lms-taxcenter/resources/views/layouts/main.blade.php)

---

## 4.1 Arsitektur Teacher (Guru) - OPSI A (Lokal Database)

Sistem manajemen guru menggunakan **database lokal SQLite** untuk menyimpan akun guru, mata pelajaran yang diajarkan, dan status. Ini memungkinkan:
- **Per-Teacher Filtering**: Setiap guru hanya melihat materi, tugas, dan jadwal untuk mata pelajaran yang mereka ajar
- **Flexible Subject Mapping**: Satu guru dapat mengajar multiple mata pelajaran
- **Scalability**: Berbeda dari Google Sheets yang terbatas, database lokal dapat menangani ratusan guru

### Schema Guru Table

```sql
CREATE TABLE gurus (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    nama VARCHAR(255) NOT NULL,
    mapel JSON NOT NULL DEFAULT '[]',  -- Array of subject names
    status ENUM('active', 'inactive') DEFAULT 'active',
    catatan TEXT NULLABLE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Model Methods (app/Models/Guru.php)

```php
// Lookup guru by email
$guru = Guru::findByEmail('guru@taxcenter.local');

// Check if guru teaches specific subject
$guru->hasMapel('Pajak Penghasilan (PPh) Orang Pribadi');

// Get comma-separated subject list for display
echo $guru->getMapelString();  // "Pajak PPh, Pajak PPN, ..."

// Auto-cast JSON mapel to array
$subjects = $guru->mapel;  // Returns array, not JSON string
```

### GuruController Data Filtering Logic

```php
// In GuruController@index():
$guru = Guru::findByEmail(session('email'));
$guruMapel = $guru ? $guru->mapel : [];

// Filter all data by guru's mapel
$materi = $this->filterByMapel($allMateri, $guruMapel);
$tugas = $this->filterByMapel($allTugas, $guruMapel);
```

### Guru Seeder (database/seeders/GuruSeeder.php)

Sample teacher data is seeded automatically with:
```bash
php artisan db:seed --class=GuruSeeder
```

6 teacher accounts are created with realistic mata pelajaran mappings for testing.

---

## 4.2 Teacher Dashboard UI - 4 Tab System

Guru dashboard (`resources/views/guru/dashboard.blade.php`) is now organized into **4 responsive tabs**:

### Tab Structure

1. **Dashboard Tab** (`#tab-dashboard`)
   - Quick action buttons (Upload Materi, Create Tugas)
   - Summary cards: Jadwal Kelas, Ringkasan Materi/Tugas, Catatan Penilaian
   - Sync cache button to refresh data

2. **Materi Tab** (`#tab-materi`)
   - Full list of uploaded materials (PDF, YouTube links)
   - Edit button for each material
   - Action bar to add new materials

3. **Tugas Tab** (`#tab-tugas`)
   - Full list of created tasks
   - Deadline display for each task
   - Edit/manage existing tasks
   - Action to create new tasks

4. **Penilaian Tab** (`#tab-penilaian`)
   - Penilaian system documentation
   - Links to Google Sheets for grading
   - Sync button to refresh grade changes

### Tab Navigation JavaScript

```javascript
// Switch between tabs
switchTab('dashboard');    // Show dashboard tab
switchTab('materi');       // Show materi tab

// Auto-initialize on page load
window.addEventListener('DOMContentLoaded', () => {
    switchTab('dashboard');
});
```

### Modal Backdrop Close Fix

Both material and task modals now close when clicking the backdrop (semi-transparent background):

```javascript
// Backdrop click closes modal
document.getElementById('materiModal').addEventListener('click', (e) => {
    if (e.target === document.getElementById('materiModal')) {
        closeMateriModal();
    }
});
```

---

## 4.3 Material Edit UI - Current File Display

When editing existing materials, teachers see the current file info with options to:
- Download current file
- Replace with new file (checkbox + file input toggle)
- Warning message when replacing

```blade
<!-- Current File Display (Edit Mode) -->
<div id="currentFileInfo" class="mb-3 hidden p-3 bg-blue-50 border border-blue-200 rounded-lg">
    <p class="text-[9px] text-blue-700 font-bold mb-1">
        <i class="fas fa-file-check mr-1"></i> File Saat Ini:
    </p>
    <p id="currentFileName">filename.pdf</p>
    <a id="currentFileLink" href="#" target="_blank">Download File</a>
    <label>
        <input type="checkbox" id="replaceFileCheckbox" onchange="toggleFileUpload()">
        Upload file baru untuk mengganti file lama
    </label>
</div>
```

JavaScript functions manage visibility:
- `toggleFileUpload()` - Show/hide file input based on checkbox
- `clearFileAndReplace()` - Reset file selection
- File metadata shown in edit mode, hidden in create mode

---

## 5. Instruksi khusus untuk AI Assistant
1. **Jangan membuat tabel migrasi database baru** untuk fitur akademik (jadwal, materi, tugas, nilai) kecuali diminta khusus. Semua harus menggunakan `GoogleSheetService`.
2. Gunakan **bahasa Indonesia** yang santai namun profesional untuk berinteraksi dengan user.
3. Selalu pertahankan styling modern dan responsif yang ada di `welcome.blade.php` (Gunakan glassmorphism, gradients, dan Tailwind CSS v4).
4. Pastikan memanggil `session()->save()` setelah memperbarui session untuk mencegah hilangnya status login siswa.
