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
    
    // Password Reset Routes (untuk user yang belum login)
    Route::prefix('password')->name('password.')->group(function () {
        Route::get('/reset', [PasswordResetController::class, 'showRequestForm'])->name('request');
        Route::post('/email', [PasswordResetController::class, 'sendResetLink'])->name('email');
        Route::get('/reset/{token}', [PasswordResetController::class, 'showResetForm'])->name('reset');
        Route::post('/reset', [PasswordResetController::class, 'resetPassword'])->name('update');
        Route::get('/success', [PasswordResetController::class, 'showSuccessPage'])->name('success');
    });
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (untuk user yang sudah login)
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::post('/update-iuran', [ProfileController::class, 'updateIuranSukarela'])->name('update-iuran');
        Route::post('/update-email', [ProfileController::class, 'updateEmail'])->name('update-email');
        
        // TAMBAHAN BARU: Change Password Routes (untuk user yang sudah login)
        Route::get('/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('change-password');
        Route::post('/change-password', [ProfileController::class, 'changePassword'])->name('change-password.update');
    });
    
    // Data Anggota Routes
    Route::get('/data-anggota', [DataAnggotaController::class, 'index'])->name('data-anggota.index');
    Route::get('/data-anggota/export', [DataAnggotaController::class, 'export'])->name('data-anggota.export');
    
    // Advokasi & Aspirasi Routes (Enhanced)
    Route::prefix('advokasi-aspirasi')->name('konsultasi.')->group(function () {
        Route::get('/', [KonsultasiController::class, 'index'])->name('index');
        Route::get('/create', [KonsultasiController::class, 'create'])->name('create');
        Route::post('/store', [KonsultasiController::class, 'store'])->name('store');
        Route::get('/{id}', [KonsultasiController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [KonsultasiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [KonsultasiController::class, 'update'])->name('update');
        Route::delete('/{id}', [KonsultasiController::class, 'destroy'])->name('destroy');
        
        // Admin responses
        Route::post('/{id}/respond', [KonsultasiController::class, 'respond'])->name('respond');
        Route::put('/{id}/status', [KonsultasiController::class, 'updateStatus'])->name('update-status');
        
        // Export functionality
        Route::get('/export/excel', [KonsultasiController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [KonsultasiController::class, 'exportPdf'])->name('export.pdf');
    });
    
    // Banpers Routes
    Route::prefix('banpers')->name('banpers.')->group(function () {
        Route::get('/', [BanpersController::class, 'index'])->name('index');
        Route::get('/create', [BanpersController::class, 'create'])->name('create');
        Route::post('/store', [BanpersController::class, 'store'])->name('store');
        Route::get('/{id}', [BanpersController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [BanpersController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BanpersController::class, 'update'])->name('update');
        Route::delete('/{id}', [BanpersController::class, 'destroy'])->name('destroy');
        
        // Admin functionality
        Route::post('/{id}/approve', [BanpersController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [BanpersController::class, 'reject'])->name('reject');
        Route::put('/{id}/status', [BanpersController::class, 'updateStatus'])->name('update-status');
        
        // Export functionality
        Route::get('/export/excel', [BanpersController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [BanpersController::class, 'exportPdf'])->name('export.pdf');
    });
    
    // Sertifikat Routes
    Route::prefix('sertifikat')->name('sertifikat.')->group(function () {
        Route::get('/', [SertifikatController::class, 'show'])->name('show');
        Route::get('/download', [SertifikatController::class, 'download'])->name('download');
        Route::get('/preview', [SertifikatController::class, 'preview'])->name('preview');
    });
    
    // Admin Only Routes
    Route::middleware(['auth', 'admin'])->group(function () {
        // Settings Management
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
            Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
            
            // User Management
            Route::get('/users', [SettingController::class, 'users'])->name('users.index');
            Route::get('/users/{id}/edit', [SettingController::class, 'editUser'])->name('users.edit');
            Route::put('/users/{id}', [SettingController::class, 'updateUser'])->name('users.update');
            Route::delete('/users/{id}', [SettingController::class, 'deleteUser'])->name('users.delete');
            
            // Password Reset Token Management (Super Admin only)
            Route::get('/password-reset-tokens', [PasswordResetController::class, 'adminTokenIndex'])->name('password-reset.tokens');
            Route::post('/password-reset-tokens/generate', [PasswordResetController::class, 'adminGenerateToken'])->name('password-reset.generate');
            Route::delete('/password-reset-tokens/{token}', [PasswordResetController::class, 'adminDeleteToken'])->name('password-reset.delete');
            
            // Reports
            Route::get('/reports', [SettingController::class, 'reports'])->name('reports.index');
            Route::get('/reports/export', [SettingController::class, 'exportReports'])->name('reports.export');
        });
    });
});

// API Routes (jika diperlukan)
Route::prefix('api')->name('api.')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        
        // API endpoints untuk mobile app atau AJAX calls
        Route::get('/profile', [ProfileController::class, 'apiProfile']);
        Route::get('/data-anggota/search', [DataAnggotaController::class, 'apiSearch']);
        Route::get('/konsultasi/stats', [KonsultasiController::class, 'apiStats']);
    });
});

// Fallback route
Route::fallback(function () {
    return redirect()->route('login');
});