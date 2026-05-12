<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AdminArticleController;

// Halaman login — lewat controller biar bisa cek session
Route::get('/', [AuthController::class, 'index'])->name('login');

// Proses login
Route::post('/login-process', [AuthController::class, 'login'])->name('login.post');

// Dashboard — dilindungi middleware custom
Route::middleware('cek.login')->group(function () {
    Route::get('/siswa/dashboard', [JadwalController::class, 'index'])->name('siswa.dashboard');
    Route::get('/guru/dashboard', [GuruController::class, 'index'])->name('guru.dashboard');
});

// Logout
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Halaman Artikel Publik ──
Route::get('/artikel', [ArticleController::class, 'index'])->name('artikel.index');
Route::get('/artikel/{slug}', [ArticleController::class, 'show'])->name('artikel.show');

// ── Admin Artikel (dilindungi session login bawaan Laravel) ──
Route::prefix('admin')->name('admin.')->group(function () {
    // Rute Login Admin
    Route::get('/login', [App\Http\Controllers\AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\AdminAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [App\Http\Controllers\AdminAuthController::class, 'logout'])->name('logout');

    // Rute CRUD Artikel (hanya bisa diakses kalau sudah login admin)
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