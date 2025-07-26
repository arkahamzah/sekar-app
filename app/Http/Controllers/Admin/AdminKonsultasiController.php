<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminKonsultasiController extends Controller
{
    /**
     * Display all konsultasi for admin
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        try {
            // Get admin info untuk filtering
            $adminData = DB::select("
                SELECT 
                    sp.ID_ROLES, sr.NAME as role_name, sr.`DESC` as role_desc,
                    sp.DPW, sp.DPD
                FROM t_sekar_pengurus sp
                LEFT JOIN t_sekar_roles sr ON sp.ID_ROLES = sr.ID
                WHERE sp.N_NIK = ?
            ", [$user->nik]);
            
            $adminInfo = count($adminData) > 0 ? $adminData[0] : null;
            $adminRole = $adminInfo && isset($adminInfo->role_name) ? $adminInfo->role_name : null;
            
            // Build query berdasarkan level admin dan filter
            $query = "
                SELECT 
                    ka.ID,
                    ka.N_NIK_PENGAJU,
                    ka.JENIS,
                    ka.KATEGORI_ADVOKASI,
                    ka.TUJUAN,
                    ka.TUJUAN_SPESIFIK,
                    ka.JUDUL,
                    ka.DESKRIPSI,
                    ka.STATUS,
                    ka.CREATED_AT,
                    ka.UPDATED_AT,
                    ka.CLOSED_AT,
                    ka.CLOSED_BY,
                    k.V_NAMA_KARYAWAN as pengaju_nama,
                    k.N_NIK as pengaju_nik,
                    k.V_KOTA_GEDUNG as pengaju_lokasi,
                    (SELECT COUNT(*) FROM t_konsultasi_komentar kk WHERE kk.ID_KONSULTASI = ka.ID) as total_komentar
                FROM t_konsultasi_advokasi ka
                LEFT JOIN t_karyawan k ON ka.N_NIK_PENGAJU = k.N_NIK
                WHERE 1=1
            ";
            
            $params = [];
            
            // Filter berdasarkan level admin
            if ($adminRole !== 'ADM') {
                if ($adminRole === 'ADMIN_DPW' && isset($adminInfo->DPW) && $adminInfo->DPW) {
                    $query .= " AND SUBSTRING(ka.TUJUAN_SPESIFIK, 1, 5) = ?";
                    $params[] = $adminInfo->DPW;
                } elseif ($adminRole === 'ADMIN_DPD' && isset($adminInfo->DPD) && $adminInfo->DPD) {
                    $query .= " AND ka.TUJUAN_SPESIFIK = ?";
                    $params[] = $adminInfo->DPD;
                }
            }
            
            // Apply filters dari request
            if ($request->filled('status')) {
                $query .= " AND ka.STATUS = ?";
                $params[] = $request->status;
            }
            
            if ($request->filled('jenis')) {
                $query .= " AND ka.JENIS = ?";
                $params[] = $request->jenis;
            }
            
            if ($request->filled('tujuan')) {
                $query .= " AND ka.TUJUAN = ?";
                $params[] = $request->tujuan;
            }
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query .= " AND (ka.JUDUL LIKE ? OR ka.DESKRIPSI LIKE ? OR k.V_NAMA_KARYAWAN LIKE ?)";
                $params[] = "%{$search}%";
                $params[] = "%{$search}%";
                $params[] = "%{$search}%";
            }
            
            if ($request->filled('date_from')) {
                $query .= " AND DATE(ka.CREATED_AT) >= ?";
                $params[] = $request->date_from;
            }
            
            if ($request->filled('date_to')) {
                $query .= " AND DATE(ka.CREATED_AT) <= ?";
                $params[] = $request->date_to;
            }
            
            // Get total count untuk pagination
            $countQuery = str_replace(
                "SELECT ka.ID, ka.N_NIK_PENGAJU, ka.JENIS, ka.KATEGORI_ADVOKASI, ka.TUJUAN, ka.TUJUAN_SPESIFIK, ka.JUDUL, ka.DESKRIPSI, ka.STATUS, ka.CREATED_AT, ka.UPDATED_AT, ka.CLOSED_AT, ka.CLOSED_BY, k.V_NAMA_KARYAWAN as pengaju_nama, k.N_NIK as pengaju_nik, k.V_KOTA_GEDUNG as pengaju_lokasi, (SELECT COUNT(*) FROM t_konsultasi_komentar kk WHERE kk.ID_KONSULTASI = ka.ID) as total_komentar",
                "SELECT COUNT(*) as total",
                $query
            );
            $totalResult = DB::select($countQuery, $params);
            $totalCount = count($totalResult) > 0 ? $totalResult[0]->total : 0;
            
            // Apply pagination
            $perPage = 20;
            $page = $request->get('page', 1);
            $offset = ($page - 1) * $perPage;
            $query .= " ORDER BY ka.CREATED_AT DESC LIMIT {$perPage} OFFSET {$offset}";
            
            $konsultasi = collect(DB::select($query, $params));
            
            // Calculate pagination info
            $pagination = [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $totalCount,
                'last_page' => ceil($totalCount / $perPage),
                'has_more_pages' => $page < ceil($totalCount / $perPage),
                'has_previous_pages' => $page > 1,
            ];
            
            // Get statistics
            $stats = $this->getKonsultasiStats($adminInfo);
            
            return view('admin.konsultasi.index', compact('konsultasi', 'stats', 'adminRole', 'pagination', 'adminInfo'));
            
        } catch (\Exception $e) {
            Log::error('Error loading admin konsultasi: ' . $e->getMessage());
            
            return view('admin.konsultasi.index', [
                'konsultasi' => collect(),
                'stats' => $this->getDefaultStats(),
                'adminRole' => null,
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 20,
                    'total' => 0,
                    'last_page' => 1,
                    'has_more_pages' => false,
                    'has_previous_pages' => false,
                ],
                'adminInfo' => null
            ])->with('error', 'Terjadi kesalahan saat memuat data konsultasi.');
        }
    }
    
    /**
     * Show specific konsultasi for admin
     */
    public function show($id)
    {
        $user = Auth::user();
        
        try {
            // Get admin info untuk filtering
            $adminData = DB::select("
                SELECT 
                    sp.ID_ROLES, sr.NAME as role_name,
                    sp.DPW, sp.DPD
                FROM t_sekar_pengurus sp
                LEFT JOIN t_sekar_roles sr ON sp.ID_ROLES = sr.ID
                WHERE sp.N_NIK = ?
            ", [$user->nik]);
            
            $adminInfo = count($adminData) > 0 ? $adminData[0] : null;
            
            // Get konsultasi dengan access control
            $query = "
                SELECT 
                    ka.*,
                    k.V_NAMA_KARYAWAN as pengaju_nama,
                    k.N_NIK as pengaju_nik,
                    k.EMAIL as pengaju_email,
                    k.V_KOTA_GEDUNG as pengaju_lokasi
                FROM t_konsultasi_advokasi ka
                LEFT JOIN t_karyawan k ON ka.N_NIK_PENGAJU = k.N_NIK
                WHERE ka.ID = ?
            ";
            
            $params = [$id];
            
            // Apply access control berdasarkan level admin
            if ($adminInfo && isset($adminInfo->role_name) && $adminInfo->role_name !== 'ADM') {
                if ($adminInfo->role_name === 'ADMIN_DPW' && isset($adminInfo->DPW) && $adminInfo->DPW) {
                    $query .= " AND SUBSTRING(ka.TUJUAN_SPESIFIK, 1, 5) = ?";
                    $params[] = $adminInfo->DPW;
                } elseif ($adminInfo->role_name === 'ADMIN_DPD' && isset($adminInfo->DPD) && $adminInfo->DPD) {
                    $query .= " AND ka.TUJUAN_SPESIFIK = ?";
                    $params[] = $adminInfo->DPD;
                }
            }
            
            $konsultasiData = DB::select($query, $params);
            
            if (empty($konsultasiData)) {
                return redirect()->route('admin.konsultasi.index')
                               ->with('error', 'Konsultasi tidak ditemukan atau Anda tidak memiliki akses.');
            }
            
            $konsultasi = $konsultasiData[0];
            
            // Get komentar/responses
            $komentar = DB::select("
                SELECT 
                    kk.*,
                    k.V_NAMA_KARYAWAN as responder_nama
                FROM t_konsultasi_komentar kk
                LEFT JOIN t_karyawan k ON kk.N_NIK_RESPONDER = k.N_NIK
                WHERE kk.ID_KONSULTASI = ?
                ORDER BY kk.CREATED_AT ASC
            ", [$id]);
            
            return view('admin.konsultasi.show', compact('konsultasi', 'komentar', 'adminInfo'));
            
        } catch (\Exception $e) {
            Log::error('Error loading konsultasi detail: ' . $e->getMessage());
            
            return redirect()->route('admin.konsultasi.index')
                           ->with('error', 'Terjadi kesalahan saat memuat detail konsultasi.');
        }
    }
    
    /**
     * Update status konsultasi
     */
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'status' => 'required|in:OPEN,IN_PROGRESS,CLOSED',
            'catatan' => 'nullable|string|max:500'
        ]);
        
        try {
            // Check access to this konsultasi
            $hasAccess = $this->checkKonsultasiAccess($id, $user);
            
            if (!$hasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengubah status konsultasi ini.'
                ], 403);
            }
            
            DB::transaction(function () use ($validated, $id, $user) {
                // Update status
                $updateData = [
                    'STATUS' => $validated['status'],
                    'UPDATED_AT' => now(),
                ];
                
                if ($validated['status'] === 'CLOSED') {
                    $updateData['CLOSED_AT'] = now();
                    $updateData['CLOSED_BY'] = $user->nik;
                }
                
                DB::table('t_konsultasi_advokasi')
                  ->where('ID', $id)
                  ->update($updateData);
                
                // Add comment jika ada catatan
                if (!empty($validated['catatan'])) {
                    DB::table('t_konsultasi_komentar')->insert([
                        'ID_KONSULTASI' => $id,
                        'N_NIK_RESPONDER' => $user->nik,
                        'KOMENTAR' => "Status diubah menjadi {$validated['status']}. Catatan: {$validated['catatan']}",
                        'CREATED_AT' => now(),
                    ]);
                }
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Status konsultasi berhasil diperbarui.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating konsultasi status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui status.'
            ], 500);
        }
    }
    
    /**
     * Add response to konsultasi
     */
    public function addResponse(Request $request, $id)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'komentar' => 'required|string|min:10|max:2000'
        ]);
        
        try {
            // Check access to this konsultasi
            $hasAccess = $this->checkKonsultasiAccess($id, $user);
            
            if (!$hasAccess) {
                return redirect()->back()
                               ->with('error', 'Anda tidak memiliki akses untuk merespons konsultasi ini.');
            }
            
            DB::transaction(function () use ($validated, $id, $user) {
                // Add response
                DB::table('t_konsultasi_komentar')->insert([
                    'ID_KONSULTASI' => $id,
                    'N_NIK_RESPONDER' => $user->nik,
                    'KOMENTAR' => $validated['komentar'],
                    'CREATED_AT' => now(),
                ]);
                
                // Update konsultasi status to IN_PROGRESS jika masih OPEN
                DB::table('t_konsultasi_advokasi')
                  ->where('ID', $id)
                  ->where('STATUS', 'OPEN')
                  ->update([
                      'STATUS' => 'IN_PROGRESS',
                      'UPDATED_AT' => now(),
                  ]);
            });
            
            return redirect()->route('admin.konsultasi.show', $id)
                           ->with('success', 'Respons berhasil ditambahkan.');
            
        } catch (\Exception $e) {
            Log::error('Error adding konsultasi response: ' . $e->getMessage());
            
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menambahkan respons.');
        }
    }
    
    /**
     * Delete konsultasi (super admin only)
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        try {
            // Only super admin dapat delete
            $adminData = DB::select("
                SELECT sr.NAME as role_name
                FROM t_sekar_pengurus sp
                LEFT JOIN t_sekar_roles sr ON sp.ID_ROLES = sr.ID
                WHERE sp.N_NIK = ?
            ", [$user->nik]);
            
            $adminRole = count($adminData) > 0 ? $adminData[0]->role_name : null;
            
            if ($adminRole !== 'ADM') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya Super Administrator yang dapat menghapus konsultasi.'
                ], 403);
            }
            
            DB::transaction(function () use ($id) {
                // Delete comments first
                DB::table('t_konsultasi_komentar')->where('ID_KONSULTASI', $id)->delete();
                
                // Delete konsultasi
                $deleted = DB::table('t_konsultasi_advokasi')->where('ID', $id)->delete();
                
                if (!$deleted) {
                    throw new \Exception('Konsultasi tidak ditemukan');
                }
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Konsultasi berhasil dihapus.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting konsultasi: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus konsultasi.'
            ], 500);
        }
    }
    
    /**
     * Check if user has access to specific konsultasi
     */
    private function checkKonsultasiAccess($konsultasiId, $user)
    {
        try {
            // Get admin info
            $adminData = DB::select("
                SELECT 
                    sp.ID_ROLES, sr.NAME as role_name,
                    sp.DPW, sp.DPD
                FROM t_sekar_pengurus sp
                LEFT JOIN t_sekar_roles sr ON sp.ID_ROLES = sr.ID
                WHERE sp.N_NIK = ?
            ", [$user->nik]);
            
            $adminInfo = count($adminData) > 0 ? $adminData[0] : null;
            
            if (!$adminInfo) {
                return false;
            }
            
            // Super admin has access to all
            if ($adminInfo->role_name === 'ADM') {
                return true;
            }
            
            // Get konsultasi
            $konsultasi = DB::select("
                SELECT TUJUAN_SPESIFIK
                FROM t_konsultasi_advokasi
                WHERE ID = ?
            ", [$konsultasiId]);
            
            if (empty($konsultasi)) {
                return false;
            }
            
            $tujuanSpesifik = $konsultasi[0]->TUJUAN_SPESIFIK;
            
            // Check access berdasarkan role
            if ($adminInfo->role_name === 'ADMIN_DPW' && isset($adminInfo->DPW) && $adminInfo->DPW) {
                return substr($tujuanSpesifik, 0, 5) === $adminInfo->DPW;
            } elseif ($adminInfo->role_name === 'ADMIN_DPD' && isset($adminInfo->DPD) && $adminInfo->DPD) {
                return $tujuanSpesifik === $adminInfo->DPD;
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Error checking konsultasi access: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get konsultasi statistics
     */
    private function getKonsultasiStats($adminInfo)
    {
        try {
            $baseWhere = "1=1";
            $params = [];
            
            if ($adminInfo && isset($adminInfo->role_name) && $adminInfo->role_name !== 'ADM') {
                if ($adminInfo->role_name === 'ADMIN_DPW' && isset($adminInfo->DPW) && $adminInfo->DPW) {
                    $baseWhere .= " AND SUBSTRING(TUJUAN_SPESIFIK, 1, 5) = ?";
                    $params[] = $adminInfo->DPW;
                } elseif ($adminInfo->role_name === 'ADMIN_DPD' && isset($adminInfo->DPD) && $adminInfo->DPD) {
                    $baseWhere .= " AND TUJUAN_SPESIFIK = ?";
                    $params[] = $adminInfo->DPD;
                }
            }
            
            $total = DB::select("SELECT COUNT(*) as count FROM t_konsultasi_advokasi WHERE {$baseWhere}", $params)[0]->count;
            
            $openParams = $params;
            $openParams[] = 'OPEN';
            $open = DB::select("SELECT COUNT(*) as count FROM t_konsultasi_advokasi WHERE {$baseWhere} AND STATUS = ?", $openParams)[0]->count;
            
            $progressParams = $params;
            $progressParams[] = 'IN_PROGRESS';
            $inProgress = DB::select("SELECT COUNT(*) as count FROM t_konsultasi_advokasi WHERE {$baseWhere} AND STATUS = ?", $progressParams)[0]->count;
            
            $closedParams = $params;
            $closedParams[] = 'CLOSED';
            $closed = DB::select("SELECT COUNT(*) as count FROM t_konsultasi_advokasi WHERE {$baseWhere} AND STATUS = ?", $closedParams)[0]->count;
            
            return [
                'total' => $total,
                'open' => $open,
                'in_progress' => $inProgress,
                'closed' => $closed,
            ];
            
        } catch (\Exception $e) {
            Log::error('Error getting konsultasi stats: ' . $e->getMessage());
            return $this->getDefaultStats();
        }
    }
    
    /**
     * Get default stats when error occurs
     */
    private function getDefaultStats()
    {
        return [
            'total' => 0,
            'open' => 0,
            'in_progress' => 0,
            'closed' => 0,
        ];
    }
}