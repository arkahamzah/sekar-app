<?php
// routes/web.php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KonsultasiController;
use App\Http\Controllers\DataAnggotaController;
use App\Http\Controllers\BanpersController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SertifikatController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Routes (accessible from user dropdown)
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update-iuran', [ProfileController::class, 'updateIuranSukarela'])->name('profile.update-iuran');
    
    // Data Anggota Routes
    Route::get('/data-anggota', [DataAnggotaController::class, 'index'])->name('data-anggota.index');
    Route::get('/data-anggota/export', [DataAnggotaController::class, 'export'])->name('data-anggota.export');
    
    // Advokasi & Aspirasi Routes (renamed from Konsultasi)
    Route::get('/advokasi-aspirasi', [KonsultasiController::class, 'index'])->name('konsultasi.index');
    Route::get('/advokasi-aspirasi/create', [KonsultasiController::class, 'create'])->name('konsultasi.create');
    Route::post('/advokasi-aspirasi', [KonsultasiController::class, 'store'])->name('konsultasi.store');
    Route::get('/advokasi-aspirasi/{id}', [KonsultasiController::class, 'show'])->name('konsultasi.show');
    Route::post('/advokasi-aspirasi/{id}/comment', [KonsultasiController::class, 'addComment'])->name('konsultasi.comment');
    
    // Banpers Routes
    Route::get('/banpers', [BanpersController::class, 'index'])->name('banpers.index');
    Route::get('/banpers/export', [BanpersController::class, 'export'])->name('banpers.export');
    
    // Sertifikat Routes (accessible by all authenticated users)
    Route::get('/sertifikat', [SertifikatController::class, 'show'])->name('sertifikat.show');
    Route::get('/sertifikat/download', [SertifikatController::class, 'download'])->name('sertifikat.download');
    
    // Setting Routes (Admin only - dengan middleware dapat ditambahkan nanti)
    Route::middleware(['check.admin'])->group(function () {
        Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
        Route::post('/setting', [SettingController::class, 'update'])->name('setting.update');
    });
});