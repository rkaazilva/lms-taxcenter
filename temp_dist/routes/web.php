<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LmsController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\AdminGuruController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AdminArticleController;

// Halaman login
Route::get('/', [AuthController::class, 'index'])->name('login');

// Proses login
Route::post('/login-process', [AuthController::class, 'login'])->name('login.post');

// Webhook Clear Cache (Untuk sinkronisasi otomatis Google Sheets)
Route::post('/api/webhook/clear-cache', [LmsController::class, 'webhookClearCache'])->name('api.webhook.clear_cache');

// Dashboard & LMS Features
Route::middleware('cek.login')->group(function () {
    // Profil Update (Siswa & Guru)
    Route::post('/profil/update', [LmsController::class, 'updateProfile'])->name('profil.update');

    // API Notifications
    Route::get('/api/notifications', [LmsController::class, 'getNotifications'])->name('api.notifications');
    Route::post('/api/notifications/read', [LmsController::class, 'markNotificationsRead'])->name('api.notifications.read');
    
    // Routing Komentar Kelas (Bisa diakses siapa saja yang login) — BUG-019: Rate limit 10 komentar/menit
    Route::post('/announcements/{announcement}/comments', [LmsController::class, 'storeComment'])->name('announcements.comments.store')->middleware('throttle:10,1');
    Route::delete('/comments/{comment}', [LmsController::class, 'deleteComment'])->name('comments.delete');
    
    // Siswa Dashboard & API (Hanya Siswa dan Admin)
    Route::middleware('cek.role:SISWA,ADMIN,ADMIN_LMS')->group(function () {
        // Siswa Dashboard (View HTML)
        Route::get('/siswa/dashboard', [LmsController::class, 'studentDashboard'])->name('siswa.dashboard');
        
        // Siswa API Endpoints (Data & Action)
        Route::get('/siswa/api/dashboard-data', [LmsController::class, 'getDashboardData'])->name('siswa.api.dashboard');
        Route::post('/siswa/api/absen', [LmsController::class, 'catatAbsen'])->name('siswa.api.absen');
        Route::post('/siswa/api/submit-tugas', [LmsController::class, 'submitTugas'])->name('siswa.api.submit_tugas');
    });
    
    // Guru/Tutor Dashboard & Action (Hanya Tutor/Guru dan Admin)
    Route::middleware('cek.role:TUTOR,GURU,ADMIN,ADMIN_LMS')->group(function () {
        Route::get('/guru/dashboard', [GuruController::class, 'index'])->name('guru.dashboard');
        Route::post('/guru/materi', [LmsController::class, 'materiStore'])->name('guru.materi.store');
        Route::post('/guru/materi/update', [LmsController::class, 'materiUpdate'])->name('guru.materi.update');
        Route::post('/guru/tugas', [LmsController::class, 'tugasStore'])->name('guru.tugas.store');
        Route::post('/guru/tugas/update', [LmsController::class, 'tugasUpdate'])->name('guru.tugas.update');
        Route::post('/guru/submissions/grade', [LmsController::class, 'gradeSubmission'])->name('guru.submissions.grade');
        Route::post('/guru/submissions/grade-batch', [LmsController::class, 'batchGradeSubmissions'])->name('guru.submissions.grade_batch');
        
        // Pengumuman Baru (Hanya Dosen / Admin)
        Route::post('/announcements', [LmsController::class, 'storeAnnouncement'])->name('announcements.store');
        Route::delete('/announcements/{announcement}', [LmsController::class, 'deleteAnnouncement'])->name('announcements.delete');

        // Absensi Management (Shared Admin & Guru)
        Route::post('/absensi/store', [LmsController::class, 'storeAbsensiManual'])->name('absensi.store_manual');
        Route::post('/absensi/delete', [LmsController::class, 'deleteAbsensi'])->name('absensi.delete');
    });

    // Admin LMS Dashboard & Management (Hanya Admin)
    Route::prefix('admin-lms')->name('admin-lms.')->middleware('cek.role:ADMIN,ADMIN_LMS')->group(function () {
        Route::get('/', [App\Http\Controllers\AdminLmsController::class, 'index'])->name('index');
        
        // Absensi Management (Admin UI)
        Route::get('/absensi', [App\Http\Controllers\AdminLmsController::class, 'absensiIndex'])->name('absensi.index');
        
        // Jadwal Management
        Route::get('/jadwal', [App\Http\Controllers\AdminLmsController::class, 'jadwalIndex'])->name('jadwal.index');
        Route::post('/jadwal/store', [App\Http\Controllers\AdminLmsController::class, 'jadwalStore'])->name('jadwal.store');
        Route::post('/jadwal/update', [App\Http\Controllers\AdminLmsController::class, 'jadwalUpdate'])->name('jadwal.update');
        Route::post('/jadwal/delete', [App\Http\Controllers\AdminLmsController::class, 'jadwalDelete'])->name('jadwal.delete');
        
        // Materi Management
        Route::get('/materi', [App\Http\Controllers\AdminLmsController::class, 'materiIndex'])->name('materi.index');
        Route::post('/materi/update-youtube', [App\Http\Controllers\AdminLmsController::class, 'materiUpdateYoutube'])->name('materi.update_youtube');
        
        // Tugas Management
        Route::get('/tugas', [App\Http\Controllers\AdminLmsController::class, 'tugasIndex'])->name('tugas.index');
        
        // Guru Management
        Route::get('/guru', [AdminGuruController::class, 'index'])->name('guru.index');
        Route::get('/guru/tambah', [AdminGuruController::class, 'create'])->name('guru.create');
        Route::post('/guru', [AdminGuruController::class, 'store'])->name('guru.store');
        Route::get('/guru/{guru}/edit', [AdminGuruController::class, 'edit'])->name('guru.edit');
        Route::put('/guru/{guru}', [AdminGuruController::class, 'update'])->name('guru.update');
        Route::delete('/guru/{guru}', [AdminGuruController::class, 'destroy'])->name('guru.destroy');
        Route::post('/guru/{guru}/toggle-status', [AdminGuruController::class, 'toggleStatus'])->name('guru.toggle_status');
        
        // Cache Sync
        Route::post('/sync-cache', [App\Http\Controllers\AdminLmsController::class, 'syncCache'])->name('sync_cache');
        
        // Notifikasi Broadcast & WhatsApp
        Route::get('/notifikasi', [App\Http\Controllers\AdminLmsController::class, 'notificationIndex'])->name('notifikasi.index');
        Route::post('/notifikasi', [App\Http\Controllers\AdminLmsController::class, 'notificationStore'])->name('notifikasi.store');
    });

    // Cache Sync (backward compat - Hanya Admin)
    Route::post('/admin/sync-cache', [LmsController::class, 'syncCache'])->name('admin.sync_cache')->middleware('cek.role:ADMIN,ADMIN_LMS');
});

// Logout
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Halaman Artikel Publik
Route::get('/artikel', [ArticleController::class, 'index'])->name('artikel.index');
Route::get('/artikel/{slug}', [ArticleController::class, 'show'])->name('artikel.show');

// Admin Artikel
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [App\Http\Controllers\AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\AdminAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [App\Http\Controllers\AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('auth')->group(function () {
        Route::get('/artikel', [AdminArticleController::class, 'index'])->name('articles.index');
        Route::get('/artikel/tambah', [AdminArticleController::class, 'create'])->name('articles.create');
        Route::post('/artikel', [AdminArticleController::class, 'store'])->name('articles.store');
        Route::get('/artikel/{article}/edit', [AdminArticleController::class, 'edit'])->name('articles.edit');
        Route::put('/artikel/{article}', [AdminArticleController::class, 'update'])->name('articles.update');
        Route::delete('/artikel/{article}', [AdminArticleController::class, 'destroy'])->name('articles.destroy');
        Route::post('/artikel/{article}/toggle', [AdminArticleController::class, 'togglePublish'])->name('articles.toggle');
    });
});
// ROUTE MIGRASI (BUG-004: Dilindungi middleware cek.role:ADMIN,ADMIN_LMS + hanya environment lokal)
Route::get('/migrate-database', function () {
    if (!app()->environment('local')) {
        abort(403, 'Aksi ini hanya diperbolehkan di lingkungan pengembangan lokal.');
    }
    \Illuminate\Support\Facades\Artisan::call('migrate:fresh', [
        '--seed' => true,
        '--force' => true,
    ]);
    return "Database Berhasil Dimigrasi & Diisi Data Admin! <br><a href='/'>Kembali ke Home</a>";
})->middleware(['cek.login', 'cek.role:ADMIN,ADMIN_LMS']);

// ROUTE MIGRASI AMAN UNTUK PRODUCTION (Bisa diakses langsung saat awal deploy untuk setup tabel)
Route::get('/run-migration-safe', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', [
            '--force' => true,
        ]);
        return "Database Berhasil Dimigrasi Secara Aman! <br><a href='/'>Kembali ke Home</a>";
    } catch (\Exception $e) {
        return "Gagal menjalankan migrasi: " . $e->getMessage();
    }
});

// ROUTE STORAGE LINK UNTUK PRODUCTION (Bisa diakses langsung saat deploy)
Route::get('/run-storage-link', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        return "Storage Link Berhasil Dibuat! <br><a href='/'>Kembali ke Home</a>";
    } catch (\Exception $e) {
        return "Gagal membuat storage link: " . $e->getMessage();
    }
});

// ROUTE MIGRASI 1-CLICK DARI GOOGLE SHEETS KE NATIVE MYSQL
Route::get('/run-import-native', function () {
    try {
        $gs = new \App\Services\GoogleSheetService();
        $res = $gs->syncFromSheetsToNativeDb();
        return "<h3>" . ($res['message'] ?? 'Import Selesai') . "</h3><pre>" . print_r($res['report'] ?? [], true) . "</pre><br><a href='/'>Kembali ke Home</a>";
    } catch (\Exception $e) {
        return "Gagal mengimpor data ke Native DB: " . $e->getMessage();
    }
});