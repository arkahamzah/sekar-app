<?php
// routes/web.php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KonsultasiController;
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
    
    // Data Anggota Routes (placeholder - will be developed later)
    Route::get('/data-anggota', function() {
        return redirect()->route('dashboard')->with('info', 'Fitur Data Anggota sedang dalam pengembangan.');
    })->name('data-anggota.index');
    
    // Konsultasi Routes
    Route::get('/konsultasi', [KonsultasiController::class, 'index'])->name('konsultasi.index');
    Route::get('/konsultasi/create', [KonsultasiController::class, 'create'])->name('konsultasi.create');
    Route::post('/konsultasi', [KonsultasiController::class, 'store'])->name('konsultasi.store');
    Route::get('/konsultasi/{id}', [KonsultasiController::class, 'show'])->name('konsultasi.show');
    Route::post('/konsultasi/{id}/comment', [KonsultasiController::class, 'addComment'])->name('konsultasi.comment');
    
    // Banpers Routes (placeholder)
    Route::get('/banpers', function() {
        return redirect()->route('dashboard')->with('info', 'Fitur Banpers sedang dalam pengembangan.');
    })->name('banpers.index');
});