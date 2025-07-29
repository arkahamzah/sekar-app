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
use App\Http\Controllers\PasswordResetController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    
    // Password Reset Routes
    Route::prefix('password')->name('password.')->group(function () {
        Route::get('/reset', [PasswordResetController::class, 'showRequestForm'])->name('request');
        Route::post('/email', [PasswordResetController::class, 'sendResetLink'])->name('email');
        Route::get('/reset/{token}', [PasswordResetController::class, 'showResetForm'])->name('reset');
        Route::post('/reset', [PasswordResetController::class, 'resetPassword'])->name('update');
        Route::get('/success', [PasswordResetController::class, 'showSuccessPage'])->name('success');
    });
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::post('/profile/update-email', [ProfileController::class, 'updateEmail'])->name('profile.update-email');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Routes (accessible from user dropdown)
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update-iuran', [ProfileController::class, 'updateIuranSukarela'])->name('profile.update-iuran');
    
    // Data Anggota Routes
    Route::get('/data-anggota', [DataAnggotaController::class, 'index'])->name('data-anggota.index');
    Route::get('/data-anggota/export', [DataAnggotaController::class, 'export'])->name('data-anggota.export');
    
    // Advokasi & Aspirasi Routes (Enhanced)
    Route::prefix('advokasi-aspirasi')->name('konsultasi.')->group(function () {
        Route::get('/', [KonsultasiController::class, 'index'])->name('index');
        Route::get('/create', [KonsultasiController::class, 'create'])->name('create');
        Route::post('/', [KonsultasiController::class, 'store'])->name('store');
        Route::get('/{id}', [KonsultasiController::class, 'show'])->name('show');
        Route::post('/{id}/comment', [KonsultasiController::class, 'addComment'])->name('comment');
        
        // Admin only routes
        Route::middleware('check.admin')->group(function () {
            Route::post('/{id}/close', [KonsultasiController::class, 'close'])->name('close');
            Route::post('/{id}/escalate', [KonsultasiController::class, 'escalate'])->name('escalate');
        });
    });
    
    // Banpers Routes
    Route::get('/banpers', [BanpersController::class, 'index'])->name('banpers.index');
    Route::get('/banpers/export', [BanpersController::class, 'export'])->name('banpers.export');
    
    // Sertifikat Routes (accessible by all authenticated users)
    Route::get('/sertifikat', [SertifikatController::class, 'show'])->name('sertifikat.show');
    Route::get('/sertifikat/download', [SertifikatController::class, 'download'])->name('sertifikat.download');
    
    // Setting Routes (Admin check will be done in controller)
    Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
    Route::post('/setting', [SettingController::class, 'update'])->name('setting.update');
});