<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\ExAnggota;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AdminDashboardController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        try {
            // Get admin info
            $adminData = DB::select("
                SELECT 
                    sp.ID_ROLES, sr.NAME as role_name, sr.`DESC` as role_desc,
                    sp.DPW, sp.DPD
                FROM t_sekar_pengurus sp
                LEFT JOIN t_sekar_roles sr ON sp.ID_ROLES = sr.ID
                WHERE sp.N_NIK = ?
            ", [$user->nik]);
            
            $adminInfo = count($adminData) > 0 ? $adminData[0] : null;
            
            // Get statistics using same logic as user dashboard but with admin perspective
            $stats = $this->getDashboardStats($adminInfo);
            
            // Get recent konsultasi based on admin level
            $recentKonsultasi = $this->getRecentKonsultasi($adminInfo);
            
            return view('admin.dashboard', compact('adminInfo', 'stats', 'recentKonsultasi'));
            
        } catch (\Exception $e) {
            Log::error('Error loading admin dashboard: ' . $e->getMessage());
            
            return view('admin.dashboard', [
                'adminInfo' => null,
                'stats' => $this->getDefaultStats(),
                'recentKonsultasi' => collect()
            ])->with('error', 'Terjadi kesalahan saat memuat dashboard admin.');
        }
    }
    
    /**
     * Get dashboard statistics using real database queries (same as user dashboard)
     */
    private function getDashboardStats($adminInfo)
    {
        // Cache key for admin statistics
        $cacheKey = "admin_dashboard_statistics_" . ($adminInfo && isset($adminInfo->role_name) ? $adminInfo->role_name : 'default');
        
        return Cache::remember($cacheKey, 300, function () use ($adminInfo) { // Cache for 5 minutes
            try {
                // Get konsultasi statistics first
                $konsultasiStats = $this->getKonsultasiStats($adminInfo);
                
                // Get user statistics (same as user dashboard)
                $userStats = $this->getUserStats();
                
                // Get today's activities
                $todayStats = $this->getTodayStats($adminInfo);
                
                return array_merge($konsultasiStats, $userStats, $todayStats);
                
            } catch (\Exception $e) {
                Log::error('Error getting admin dashboard stats: ' . $e->getMessage());
                return $this->getDefaultStats();
            }
        });
    }
    
    /**
     * Get konsultasi statistics based on admin level access
     */
    private function getKonsultasiStats($adminInfo)
    {
        try {
            $baseWhere = "1=1";
            $params = [];
            
            // Filter based on admin level (same logic as AdminKonsultasiController)
            if ($adminInfo && $adminInfo->role_name !== 'ADM') {
                if ($adminInfo->role_name === 'ADMIN_DPW' && $adminInfo->DPW) {
                    $baseWhere .= " AND SUBSTRING(TUJUAN_SPESIFIK, 1, 5) = ?";
                    $params[] = $adminInfo->DPW;
                } elseif ($adminInfo->role_name === 'ADMIN_DPD' && $adminInfo->DPD) {
                    $baseWhere .= " AND TUJUAN_SPESIFIK = ?";
                    $params[] = $adminInfo->DPD;
                }
            }
            
            // Get total konsultasi
            $totalQuery = "SELECT COUNT(*) as count FROM t_konsultasi_advokasi WHERE {$baseWhere}";
            $total = DB::select($totalQuery, $params)[0]->count;
            
            // Get status-specific counts
            $openParams = $params;
            $openParams[] = 'OPEN';
            $openQuery = "SELECT COUNT(*) as count FROM t_konsultasi_advokasi WHERE {$baseWhere} AND STATUS = ?";
            $open = DB::select($openQuery, $openParams)[0]->count;
            
            $progressParams = $params;
            $progressParams[] = 'IN_PROGRESS';
            $progressQuery = "SELECT COUNT(*) as count FROM t_konsultasi_advokasi WHERE {$baseWhere} AND STATUS = ?";
            $inProgress = DB::select($progressQuery, $progressParams)[0]->count;
            
            $closedParams = $params;
            $closedParams[] = 'CLOSED';
            $closedQuery = "SELECT COUNT(*) as count FROM t_konsultasi_advokasi WHERE {$baseWhere} AND STATUS = ?";
            $closed = DB::select($closedQuery, $closedParams)[0]->count;
            
            // Get jenis-specific counts
            $advokasiParams = $params;
            $advokasiParams[] = 'ADVOKASI';
            $advokasiQuery = "SELECT COUNT(*) as count FROM t_konsultasi_advokasi WHERE {$baseWhere} AND JENIS = ?";
            $advokasi = DB::select($advokasiQuery, $advokasiParams)[0]->count;
            
            $aspirasiParams = $params;
            $aspirasiParams[] = 'ASPIRASI';
            $aspirasiQuery = "SELECT COUNT(*) as count FROM t_konsultasi_advokasi WHERE {$baseWhere} AND JENIS = ?";
            $aspirasi = DB::select($aspirasiQuery, $aspirasiParams)[0]->count;
            
            return [
                'total_konsultasi' => $total,
                'konsultasi_open' => $open,
                'konsultasi_in_progress' => $inProgress,
                'konsultasi_closed' => $closed,
                'konsultasi_advokasi' => $advokasi,
                'konsultasi_aspirasi' => $aspirasi,
            ];
            
        } catch (\Exception $e) {
            Log::error('Error getting konsultasi stats: ' . $e->getMessage());
            return [
                'total_konsultasi' => 0,
                'konsultasi_open' => 0,
                'konsultasi_in_progress' => 0,
                'konsultasi_closed' => 0,
                'konsultasi_advokasi' => 0,
                'konsultasi_aspirasi' => 0,
            ];
        }
    }
    
    /**
     * Get user statistics (same logic as user dashboard)
     */
    private function getUserStats()
    {
        try {
            // Anggota Aktif = users yang sudah terdaftar (tidak termasuk GPTP)
            $anggotaAktif = DB::table('users as u')
                ->join('t_karyawan as k', 'u.nik', '=', 'k.N_NIK')
                ->where('k.V_SHORT_POSISI', 'NOT LIKE', '%GPTP%')
                ->count();

            // Total Pengurus = pengurus yang masih aktif sebagai karyawan
            $totalPengurus = DB::table('t_sekar_pengurus as sp')
                ->join('t_karyawan as k', 'sp.N_NIK', '=', 'k.N_NIK')
                ->count();

            // Anggota Keluar = ex anggota
            $anggotaKeluar = ExAnggota::count();

            // Total Karyawan (tidak termasuk GPTP)
            $totalKaryawan = Karyawan::where('V_SHORT_POSISI', 'NOT LIKE', '%GPTP%')->count();
            
            // Non Anggota = total karyawan - anggota aktif
            $nonAnggota = max(0, $totalKaryawan - $anggotaAktif);
            
            return [
                'anggota_aktif' => $anggotaAktif,
                'total_pengurus' => $totalPengurus,
                'anggota_keluar' => $anggotaKeluar,
                'total_karyawan' => $totalKaryawan,
                'non_anggota' => $nonAnggota,
            ];
            
        } catch (\Exception $e) {
            Log::error('Error getting user stats: ' . $e->getMessage());
            return [
                'anggota_aktif' => 0,
                'total_pengurus' => 0,
                'anggota_keluar' => 0,
                'total_karyawan' => 0,
                'non_anggota' => 0,
            ];
        }
    }
    
    /**
     * Get today's activity statistics
     */
    private function getTodayStats($adminInfo)
    {
        try {
            $baseWhere = "DATE(CREATED_AT) = CURDATE()";
            $params = [];
            
            // Filter based on admin level
            if ($adminInfo && $adminInfo->role_name !== 'ADM') {
                if ($adminInfo->role_name === 'ADMIN_DPW' && $adminInfo->DPW) {
                    $baseWhere .= " AND SUBSTRING(TUJUAN_SPESIFIK, 1, 5) = ?";
                    $params[] = $adminInfo->DPW;
                } elseif ($adminInfo->role_name === 'ADMIN_DPD' && $adminInfo->DPD) {
                    $baseWhere .= " AND TUJUAN_SPESIFIK = ?";
                    $params[] = $adminInfo->DPD;
                }
            }
            
            $todayKonsultasi = DB::select("SELECT COUNT(*) as count FROM t_konsultasi_advokasi WHERE {$baseWhere}", $params)[0]->count;
            
            // Today's new users (system-wide, not filtered by admin level)
            $todayUsers = DB::table('users')
                ->whereDate('created_at', today())
                ->count();
            
            return [
                'today_konsultasi' => $todayKonsultasi,
                'today_users' => $todayUsers,
            ];
            
        } catch (\Exception $e) {
            Log::error('Error getting today stats: ' . $e->getMessage());
            return [
                'today_konsultasi' => 0,
                'today_users' => 0,
            ];
        }
    }
    
    /**
     * Get recent konsultasi based on admin access level
     */
    private function getRecentKonsultasi($adminInfo)
    {
        try {
            $query = "
                SELECT 
                    ka.ID,
                    ka.JUDUL,
                    ka.STATUS,
                    ka.JENIS,
                    ka.TUJUAN,
                    ka.CREATED_AT,
                    k.V_NAMA_KARYAWAN as pengaju_nama,
                    k.N_NIK as pengaju_nik
                FROM t_konsultasi_advokasi ka
                LEFT JOIN t_karyawan k ON ka.N_NIK_PENGAJU = k.N_NIK
                WHERE 1=1
            ";
            
            $params = [];
            
            // Filter based on admin level
            if ($adminInfo && $adminInfo->role_name !== 'ADM') {
                if ($adminInfo->role_name === 'ADMIN_DPW' && $adminInfo->DPW) {
                    $query .= " AND SUBSTRING(ka.TUJUAN_SPESIFIK, 1, 5) = ?";
                    $params[] = $adminInfo->DPW;
                } elseif ($adminInfo->role_name === 'ADMIN_DPD' && $adminInfo->DPD) {
                    $query .= " AND ka.TUJUAN_SPESIFIK = ?";
                    $params[] = $adminInfo->DPD;
                }
            }
            
            $query .= " ORDER BY ka.CREATED_AT DESC LIMIT 10";
            
            $results = DB::select($query, $params);
            
            return collect($results);
            
        } catch (\Exception $e) {
            Log::error('Error getting recent konsultasi: ' . $e->getMessage());
            return collect();
        }
    }
    
    /**
     * Get default stats when error occurs
     */
    private function getDefaultStats()
    {
        return [
            'total_konsultasi' => 0,
            'konsultasi_open' => 0,
            'konsultasi_in_progress' => 0,
            'konsultasi_closed' => 0,
            'konsultasi_advokasi' => 0,
            'konsultasi_aspirasi' => 0,
            'anggota_aktif' => 0,
            'total_pengurus' => 0,
            'anggota_keluar' => 0,
            'total_karyawan' => 0,
            'non_anggota' => 0,
            'today_konsultasi' => 0,
            'today_users' => 0,
        ];
    }
}