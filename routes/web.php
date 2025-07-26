<?php
// routes/web.php - UPDATED WITH ADMIN REDIRECT

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KonsultasiController;
use App\Http\Controllers\DataAnggotaController;
use App\Http\Controllers\BanpersController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SertifikatController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminKonsultasiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

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
    
    // Dashboard route dengan pengecekan admin
    Route::get('/dashboard', function() {
        $user = Auth::user();
        
        // Cek apakah user adalah admin menggunakan CheckAdmin helper
        if (\App\Http\Middleware\CheckAdmin::isCurrentUserAdmin()) {
            return redirect()->route('admin.dashboard')
                           ->with('info', 'Anda telah dialihkan ke dashboard admin.');
        }
        
        // Jika bukan admin, tampilkan dashboard user biasa
        return app(DashboardController::class)->index();
    })->name('dashboard');
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update-email', [ProfileController::class, 'updateEmail'])->name('profile.update-email');
    Route::post('/profile/update-iuran', [ProfileController::class, 'updateIuranSukarela'])->name('profile.update-iuran');
    
    // Data Anggota Routes
    Route::get('/data-anggota', [DataAnggotaController::class, 'index'])->name('data-anggota.index');
    Route::get('/data-anggota/export', [DataAnggotaController::class, 'export'])->name('data-anggota.export');
    
    // Advokasi & Aspirasi Routes
    Route::prefix('advokasi-aspirasi')->name('konsultasi.')->group(function () {
        Route::get('/', [KonsultasiController::class, 'index'])->name('index');
        Route::get('/create', [KonsultasiController::class, 'create'])->name('create');
        Route::post('/', [KonsultasiController::class, 'store'])->name('store');
        Route::get('/{konsultasi}', [KonsultasiController::class, 'show'])->name('show');
        Route::get('/{konsultasi}/edit', [KonsultasiController::class, 'edit'])->name('edit');
        Route::put('/{konsultasi}', [KonsultasiController::class, 'update'])->name('update');
        Route::delete('/{konsultasi}', [KonsultasiController::class, 'destroy'])->name('destroy');
    });
    
    // Bantuan Perusahaan Routes
    Route::prefix('bantuan-perusahaan')->name('banpers.')->group(function () {
        Route::get('/', [BanpersController::class, 'index'])->name('index');
        Route::get('/create', [BanpersController::class, 'create'])->name('create');
        Route::post('/', [BanpersController::class, 'store'])->name('store');
        Route::get('/{banpers}', [BanpersController::class, 'show'])->name('show');
        Route::get('/{banpers}/edit', [BanpersController::class, 'edit'])->name('edit');
        Route::put('/{banpers}', [BanpersController::class, 'update'])->name('update');
        Route::delete('/{banpers}', [BanpersController::class, 'destroy'])->name('destroy');
    });
    
    // Sertifikat Routes
    Route::prefix('sertifikat')->name('sertifikat.')->group(function () {
        Route::get('/', [SertifikatController::class, 'index'])->name('index');
        Route::get('/generate', [SertifikatController::class, 'generate'])->name('generate');
        Route::post('/generate', [SertifikatController::class, 'store'])->name('store');
        Route::get('/{sertifikat}/download', [SertifikatController::class, 'download'])->name('download');
    });
});

// Admin Routes - Dengan middleware CheckAdmin
Route::middleware(['auth', 'check.admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Admin Dashboard - Menggunakan Controller
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);
    
    // Admin Konsultasi Management
    Route::prefix('konsultasi')->name('konsultasi.')->group(function () {
        Route::get('/', [AdminKonsultasiController::class, 'index'])->name('index');
        Route::get('/{konsultasi}', [AdminKonsultasiController::class, 'show'])->name('show');
        Route::put('/{konsultasi}/status', [AdminKonsultasiController::class, 'updateStatus'])->name('update-status');
        Route::post('/{konsultasi}/response', [AdminKonsultasiController::class, 'addResponse'])->name('add-response');
        Route::delete('/{konsultasi}', [AdminKonsultasiController::class, 'destroy'])->name('destroy');
    });
    
    // Admin Data Anggota (jika ada)
    Route::prefix('data-anggota')->name('data-anggota.')->group(function () {
        Route::get('/', [AdminDataAnggotaController::class, 'index'])->name('index');
        Route::get('/export', [AdminDataAnggotaController::class, 'export'])->name('export');
        Route::get('/{user}', [AdminDataAnggotaController::class, 'show'])->name('show');
        Route::put('/{user}', [AdminDataAnggotaController::class, 'update'])->name('update');
    });
    
    // Admin Bantuan Perusahaan (jika ada)
    Route::prefix('banpers')->name('banpers.')->group(function () {
        Route::get('/', [AdminBanpersController::class, 'index'])->name('index');
        Route::get('/{banpers}', [AdminBanpersController::class, 'show'])->name('show');
        Route::put('/{banpers}/status', [AdminBanpersController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{banpers}', [AdminBanpersController::class, 'destroy'])->name('destroy');
    });
    
    // Admin User Management (jika ada)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('index');
        Route::get('/{user}', [AdminUserController::class, 'show'])->name('show');
        Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
        Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
    });
    
    // Admin Reports (jika ada)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [AdminReportController::class, 'index'])->name('index');
        Route::get('/konsultasi', [AdminReportController::class, 'konsultasi'])->name('konsultasi');
        Route::get('/anggota', [AdminReportController::class, 'anggota'])->name('anggota');
        Route::get('/banpers', [AdminReportController::class, 'banpers'])->name('banpers');
    });
});

// Setting Routes - Hanya untuk admin
Route::middleware(['auth', 'check.admin'])->prefix('setting')->name('setting.')->group(function () {
    Route::get('/', [SettingController::class, 'index'])->name('index');
    Route::post('/update', [SettingController::class, 'update'])->name('update');
    Route::post('/backup', [SettingController::class, 'backup'])->name('backup');
    Route::post('/restore', [SettingController::class, 'restore'])->name('restore');
});

// API Routes (jika diperlukan)
Route::prefix('api')->name('api.')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/user-info', function () {
            $user = Auth::user();
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'nik' => $user->nik,
                'email' => $user->email
            ]);
        });
        
        Route::get('/check-admin', function () {
            $user = Auth::user();
            $isAdmin = false;
            
            try {
                $adminData = DB::select("
                    SELECT 
                        sp.ID_ROLES, sr.NAME as role_name
                    FROM t_sekar_pengurus sp
                    LEFT JOIN t_sekar_roles sr ON sp.ID_ROLES = sr.ID
                    WHERE sp.N_NIK = ?
                ", [$user->nik]);

                if (!empty($adminData)) {
                    $userData = $adminData[0];
                    $adminRoles = ['ADM', 'ADMIN_DPP', 'ADMIN_DPW', 'ADMIN_DPD'];
                    $isAdmin = in_array($userData->role_name, $adminRoles);
                }
            } catch (\Exception $e) {
                Log::error('Error checking admin status: ' . $e->getMessage());
            }
            
            return response()->json(['is_admin' => $isAdmin]);
        });
    });
});