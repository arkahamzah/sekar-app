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
            // Get admin info
            $adminData = DB::select("
                SELECT 
                    sp.ID_ROLES, sr.NAME as role_name, sr.`DESC` as role_desc,
                    sp.DPW, sp.DPD
                FROM t_sekar_pengurus sp
                LEFT JOIN t_sekar_roles sr ON sp.ID_ROLES = sr.ID
                WHERE sp.N_NIK = ?
            ", [$user->nik]);
            
            $adminInfo = $adminData[0] ?? null;
            $adminRole = $adminInfo->role_name ?? null;
            
            // Build query based on admin role and access level
            $query = "
                SELECT 
                    ka.*,
                    k.NAMA as pengaju_nama,
                    k.N_NIK as pengaju_nik
                FROM t_konsultasi_advokasi ka
                LEFT JOIN t_karyawan k ON ka.N_NIK_PENGAJU = k.N_NIK
                WHERE 1=1
            ";
            
            $params = [];
            
            // Filter by admin access level
            if ($adminRole !== 'ADM') {
                if ($adminRole === 'ADMIN_DPW' && $adminInfo->DPW) {
                    $query .= " AND SUBSTRING(ka.TUJUAN_SPESIFIK, 1, 5) = ?";
                    $params[] = $adminInfo->DPW;
                } elseif ($adminRole === 'ADMIN_DPD' && $adminInfo->DPD) {
                    $query .= " AND ka.TUJUAN_SPESIFIK = ?";
                    $params[] = $adminInfo->DPD;
                }
            }
            
            // Apply filters
            if ($request->filled('status')) {
                $query .= " AND ka.STATUS = ?";
                $params[] = $request->status;
            }
            
            if ($request->filled('jenis')) {
                $query .= " AND ka.JENIS = ?";
                $params[] = $request->jenis;
            }
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query .= " AND (ka.JUDUL LIKE ? OR ka.DESKRIPSI LIKE ? OR k.NAMA LIKE ?)";
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
            
            $query .= " ORDER BY ka.CREATED_AT DESC";
            
            // Get total count for pagination
            $countQuery = str_replace("SELECT ka.*, k.NAMA as pengaju_nama, k.N_NIK as pengaju_nik", "SELECT COUNT(*) as total", $query);
            $totalCount = DB::select($countQuery, $params)[0]->total ?? 0;
            
            // Apply pagination
            $perPage = 20;
            $page = $request->get('page', 1);
            $offset = ($page - 1) * $perPage;
            $query .= " LIMIT {$perPage} OFFSET {$offset}";
            
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
            
            return view('admin.konsultasi.index', compact('konsultasi', 'stats', 'adminRole', 'pagination'));
            
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
                ]
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
            // Get admin info
            $adminData = DB::select("
                SELECT 
                    sp.ID_ROLES, sr.NAME as role_name,
                    sp.DPW, sp.DPD
                FROM t_sekar_pengurus sp
                LEFT JOIN t_sekar_roles sr ON sp.ID_ROLES = sr.ID
                WHERE sp.N_NIK = ?
            ", [$user->nik]);
            
            $adminInfo = $adminData[0] ?? null;
            
            // Get konsultasi with access control
            $query = "
                SELECT 
                    ka.*,
                    k.NAMA as pengaju_nama,
                    k.N_NIK as pengaju_nik,
                    k.EMAIL as pengaju_email
                FROM t_konsultasi_advokasi ka
                LEFT JOIN t_karyawan k ON ka.N_NIK_PENGAJU = k.N_NIK
                WHERE ka.ID = ?
            ";
            
            $params = [$id];
            
            // Apply access control
            if ($adminInfo && $adminInfo->role_name !== 'ADM') {
                if ($adminInfo->role_name === 'ADMIN_DPW' && $adminInfo->DPW) {
                    $query .= " AND SUBSTRING(ka.TUJUAN_SPESIFIK, 1, 5) = ?";
                    $params[] = $adminInfo->DPW;
                } elseif ($adminInfo->role_name === 'ADMIN_DPD' && $adminInfo->DPD) {
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
                    k.NAMA as responder_nama
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
            
            // Add comment if provided
            if (!empty($validated['catatan'])) {
                DB::table('t_konsultasi_komentar')->insert([
                    'ID_KONSULTASI' => $id,
                    'N_NIK_RESPONDER' => $user->nik,
                    'KOMENTAR' => "Status diubah menjadi {$validated['status']}. Catatan: {$validated['catatan']}",
                    'CREATED_AT' => now(),
                ]);
            }
            
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
            
            // Add response
            DB::table('t_konsultasi_komentar')->insert([
                'ID_KONSULTASI' => $id,
                'N_NIK_RESPONDER' => $user->nik,
                'KOMENTAR' => $validated['komentar'],
                'CREATED_AT' => now(),
            ]);
            
            // Update konsultasi status to IN_PROGRESS if still OPEN
            DB::table('t_konsultasi_advokasi')
              ->where('ID', $id)
              ->where('STATUS', 'OPEN')
              ->update([
                  'STATUS' => 'IN_PROGRESS',
                  'UPDATED_AT' => now(),
              ]);
            
            return redirect()->route('admin.konsultasi.show', $id)
                           ->with('success', 'Respons berhasil ditambahkan.');
            
        } catch (\Exception $e) {
            Log::error('Error adding konsultasi response: ' . $e->getMessage());
            
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menambahkan respons.');
        }
    }
    
    /**
     * Delete konsultasi (admin only)
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        try {
            // Only super admin can delete
            $adminData = DB::select("
                SELECT sr.NAME as role_name
                FROM t_sekar_pengurus sp
                LEFT JOIN t_sekar_roles sr ON sp.ID_ROLES = sr.ID
                WHERE sp.N_NIK = ?
            ", [$user->nik]);
            
            $adminRole = $adminData[0]->role_name ?? null;
            
            if ($adminRole !== 'ADM') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya Super Administrator yang dapat menghapus konsultasi.'
                ], 403);
            }
            
            // Delete comments first
            DB::table('t_konsultasi_komentar')->where('ID_KONSULTASI', $id)->delete();
            
            // Delete konsultasi
            $deleted = DB::table('t_konsultasi_advokasi')->where('ID', $id)->delete();
            
            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Konsultasi berhasil dihapus.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Konsultasi tidak ditemukan.'
                ], 404);
            }
            
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
            
            $adminInfo = $adminData[0] ?? null;
            
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
            
            // Check access based on role
            if ($adminInfo->role_name === 'ADMIN_DPW' && $adminInfo->DPW) {
                return substr($tujuanSpesifik, 0, 5) === $adminInfo->DPW;
            } elseif ($adminInfo->role_name === 'ADMIN_DPD' && $adminInfo->DPD) {
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
            $baseWhere = "";
            $params = [];
            
            if ($adminInfo && $adminInfo->role_name !== 'ADM') {
                if ($adminInfo->role_name === 'ADMIN_DPW' && $adminInfo->DPW) {
                    $baseWhere = " WHERE SUBSTRING(TUJUAN_SPESIFIK, 1, 5) = ?";
                    $params[] = $adminInfo->DPW;
                } elseif ($adminInfo->role_name === 'ADMIN_DPD' && $adminInfo->DPD) {
                    $baseWhere = " WHERE TUJUAN_SPESIFIK = ?";
                    $params[] = $adminInfo->DPD;
                }
            }
            
            $total = DB::select("SELECT COUNT(*) as count FROM t_konsultasi_advokasi" . $baseWhere, $params)[0]->count ?? 0;
            
            $openParams = $params;
            $openParams[] = 'OPEN';
            $open = DB::select("SELECT COUNT(*) as count FROM t_konsultasi_advokasi" . ($baseWhere ? $baseWhere . " AND" : " WHERE") . " STATUS = ?", $openParams)[0]->count ?? 0;
            
            $progressParams = $params;
            $progressParams[] = 'IN_PROGRESS';
            $inProgress = DB::select("SELECT COUNT(*) as count FROM t_konsultasi_advokasi" . ($baseWhere ? $baseWhere . " AND" : " WHERE") . " STATUS = ?", $progressParams)[0]->count ?? 0;
            
            $closedParams = $params;
            $closedParams[] = 'CLOSED';
            $closed = DB::select("SELECT COUNT(*) as count FROM t_konsultasi_advokasi" . ($baseWhere ? $baseWhere . " AND" : " WHERE") . " STATUS = ?", $closedParams)[0]->count ?? 0;
            
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