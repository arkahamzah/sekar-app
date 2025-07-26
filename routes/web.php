<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KonsultasiController;
use App\Http\Controllers\DataAnggotaController;
use App\Http\Controllers\BanpersController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SertifikatController;
use App\Http\Middleware\CheckAdmin;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update-iuran', [ProfileController::class, 'updateIuranSukarela'])->name('profile.update-iuran');
    
    Route::get('/data-anggota', [DataAnggotaController::class, 'index'])->name('data-anggota.index');
    Route::get('/data-anggota/export', [DataAnggotaController::class, 'export'])->name('data-anggota.export');
    
    Route::prefix('advokasi-aspirasi')->name('konsultasi.')->group(function () {
        Route::get('/', [KonsultasiController::class, 'index'])->name('index');
        Route::get('/create', [KonsultasiController::class, 'create'])->name('create');
        Route::post('/', [KonsultasiController::class, 'store'])->name('store');
        Route::get('/{id}', [KonsultasiController::class, 'show'])->name('show');
        Route::post('/{id}/comment', [KonsultasiController::class, 'addComment'])->name('comment');
        
        Route::middleware(CheckAdmin::class)->group(function () {
            Route::post('/{id}/close', [KonsultasiController::class, 'close'])->name('close');
            Route::post('/{id}/escalate', [KonsultasiController::class, 'escalate'])->name('escalate');
        });
    });
    
    Route::get('/banpers', [BanpersController::class, 'index'])->name('banpers.index');
    Route::get('/banpers/export', [BanpersController::class, 'export'])->name('banpers.export');
    
    Route::get('/sertifikat', [SertifikatController::class, 'show'])->name('sertifikat.show');
    Route::get('/sertifikat/download', [SertifikatController::class, 'download'])->name('sertifikat.download');
    
    Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
    Route::post('/setting', [SettingController::class, 'update'])->name('setting.update');
});