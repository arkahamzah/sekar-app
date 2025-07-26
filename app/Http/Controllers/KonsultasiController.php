<?php

namespace App\Http\Controllers;

use App\Models\Konsultasi;
use App\Models\KonsultasiKomentar;
use App\Models\Karyawan;
use App\Models\SekarPengurus;
use App\Mail\KonsultasiNotification;
use App\Jobs\SendKonsultasiNotificationJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class KonsultasiController extends Controller
{
    /**
     * Display user's advokasi & aspirasi
     */
    public function index()
    {
        $user = Auth::user();
        
        $konsultasi = Konsultasi::where('N_NIK', $user->nik)
                                ->with(['karyawan'])
                                ->orderBy('CREATED_AT', 'desc')
                                ->get();
        
        return view('konsultasi.index', compact('konsultasi'));
    }
    
    /**
     * Show create advokasi & aspirasi form
     */
    public function create()
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('N_NIK', $user->nik)->first();
        
        $data = [
            'karyawan' => $karyawan,
            'availableTargets' => $this->getAvailableTargets($karyawan),
            'kategoriAdvokasi' => $this->getKategoriAdvokasi(),
        ];
        
        return view('konsultasi.create', $data);
    }
    
    /**
     * Store new advokasi & aspirasi
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required|in:ADVOKASI,ASPIRASI',
            'kategori_advokasi' => 'required_if:jenis,ADVOKASI',
            'tujuan' => 'required|in:DPP,DPW,DPD,GENERAL',
            'tujuan_spesifik' => 'required_unless:tujuan,GENERAL',
            'judul' => 'required|max:200',
            'deskripsi' => 'required'
        ]);
        
        $user = Auth::user();
        
        try {
            DB::transaction(function () use ($user, $validated) {
                $konsultasi = $this->createKonsultasi($user, $validated);
                
                // Send email notification to relevant admins
                $this->sendEmailNotification($konsultasi, 'new');
            });
            
            return redirect()->route('konsultasi.index')
                            ->with('success', 'Advokasi/Aspirasi berhasil dibuat dan akan segera ditindaklanjuti.');
        } catch (\Exception $e) {
            Log::error('Error creating konsultasi: ' . $e->getMessage(), [
                'user_nik' => $user->nik,
                'validated_data' => $validated,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withInput()
                        ->with('error', 'Terjadi kesalahan saat membuat advokasi/aspirasi. Silakan coba lagi.');
        }
    }
    
    /**
     * Show advokasi & aspirasi detail
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $konsultasi = Konsultasi::where('ID', $id)
                                ->where('N_NIK', $user->nik)
                                ->with(['karyawan', 'komentar.karyawan'])
                                ->firstOrFail();
        
        return view('konsultasi.show', compact('konsultasi'));
    }
    
    /**
     * Add comment to advokasi & aspirasi
     */
    public function addComment(Request $request, $id)
    {
        $validated = $request->validate([
            'komentar' => 'required|string|max:1000'
        ]);
        
        $user = Auth::user();
        
        try {
            DB::transaction(function () use ($validated, $id, $user) {
                // Verify ownership
                $konsultasi = $this->verifyKonsultasiOwnership($id, $user->nik);
                
                // Create comment
                $this->createKomentar($id, $user->nik, $validated['komentar']);
                
                // Update status if needed
                $this->updateKonsultasiStatus($konsultasi, $user->nik);
                
                // Send email notification
                $this->sendEmailNotification($konsultasi, 'comment');
            });
            
            return redirect()->route('konsultasi.show', $id)
                            ->with('success', 'Komentar berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error adding comment: ' . $e->getMessage(), [
                'user_nik' => $user->nik,
                'konsultasi_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Terjadi kesalahan saat menambahkan komentar.');
        }
    }
    
    /**
     * Close konsultasi (admin only)
     */
    public function close(Request $request, $id)
    {
        $user = Auth::user();
        
        // Verify admin access
        if (!$this->isAdmin($user)) {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat menutup konsultasi.');
        }
        
        try {
            DB::transaction(function () use ($id, $user) {
                $konsultasi = Konsultasi::findOrFail($id);
                
                $konsultasi->update([
                    'STATUS' => 'CLOSED',
                    'CLOSED_BY' => $user->nik,
                    'CLOSED_AT' => now(),
                    'UPDATED_BY' => $user->nik,
                    'UPDATED_AT' => now()
                ]);
                
                // Send email notification
                $this->sendEmailNotification($konsultasi, 'closed');
            });
            
            return redirect()->back()->with('success', 'Konsultasi berhasil ditutup.');
        } catch (\Exception $e) {
            Log::error('Error closing konsultasi: ' . $e->getMessage(), [
                'user_nik' => $user->nik,
                'konsultasi_id' => $id
            ]);
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menutup konsultasi.');
        }
    }
    
    /**
     * Escalate konsultasi to higher level (admin only)
     */
    public function escalate(Request $request, $id)
    {
        $validated = $request->validate([
            'escalate_to' => 'required|in:DPW,DPP',
            'escalation_note' => 'required|string|max:500'
        ]);
        
        $user = Auth::user();
        
        // Verify admin access
        if (!$this->isAdmin($user)) {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat melakukan eskalasi.');
        }
        
        try {
            DB::transaction(function () use ($validated, $id, $user) {
                $konsultasi = Konsultasi::findOrFail($id);
                
                // Update konsultasi target
                $konsultasi->update([
                    'TUJUAN' => $validated['escalate_to'],
                    'STATUS' => 'OPEN', // Reset to open for higher level
                    'UPDATED_BY' => $user->nik,
                    'UPDATED_AT' => now()
                ]);
                
                // Add escalation comment
                $this->createKomentar(
                    $id, 
                    $user->nik, 
                    "ESKALASI KE {$validated['escalate_to']}: {$validated['escalation_note']}",
                    'ADMIN'
                );
                
                // Send email notification
                $this->sendEmailNotification($konsultasi, 'escalate');
            });
            
            return redirect()->back()->with('success', 'Konsultasi berhasil dieskalasi ke level yang lebih tinggi.');
        } catch (\Exception $e) {
            Log::error('Error escalating konsultasi: ' . $e->getMessage(), [
                'user_nik' => $user->nik,
                'konsultasi_id' => $id,
                'escalate_to' => $validated['escalate_to'] ?? null
            ]);
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat melakukan eskalasi.');
        }
    }
    
    /**
     * Get available targets based on employee location
     */
    private function getAvailableTargets(?Karyawan $karyawan): array
    {
        $targets = [
            'GENERAL' => 'SEKAR Pusat'
        ];
        
        if (!$karyawan) {
            return $targets;
        }
        
        $city = $karyawan->V_KOTA_GEDUNG;
        $dpwMapping = $this->getDpwMapping();
        $dpdMapping = $this->getDpdMapping();
        
        if (isset($dpwMapping[$city])) {
            $targets['DPW'] = $dpwMapping[$city];
        }
        
        if (isset($dpdMapping[$city])) {
            $targets['DPD'] = $dpdMapping[$city];
        }
        
        $targets['DPP'] = 'DPP Pusat';
        
        return $targets;
    }
    
    /**
     * Get DPW mapping configuration
     */
    private function getDpwMapping(): array
    {
        return [
            'BANDUNG' => 'DPW Jabar',
            'JAKARTA' => 'DPW Jakarta',
            'SURABAYA' => 'DPW Jatim',
            'MEDAN' => 'DPW Sumut',
            'MAKASSAR' => 'DPW Sulsel',
            'DENPASAR' => 'DPW Bali',
            'BALIKPAPAN' => 'DPW Kaltim',
        ];
    }
    
    /**
     * Get DPD mapping configuration
     */
    private function getDpdMapping(): array
    {
        return [
            'BANDUNG' => 'DPD Bandung',
            'JAKARTA' => 'DPD Jakarta Pusat',
            'SURABAYA' => 'DPD Surabaya',
            'MEDAN' => 'DPD Medan',
            'MAKASSAR' => 'DPD Makassar',
            'DENPASAR' => 'DPD Denpasar',
            'BALIKPAPAN' => 'DPD Balikpapan',
        ];
    }
    
    /**
     * Get available advocacy categories
     */
    private function getKategoriAdvokasi(): array
    {
        return [
            'Pelanggaran Hak Pekerja',
            'Masalah Kesejahteraan',
            'Diskriminasi',
            'Keselamatan Kerja',
            'Kondisi Kerja',
            'Upah dan Tunjangan',
            'Pelecehan di Tempat Kerja',
            'Pemutusan Hubungan Kerja',
            'Lainnya'
        ];
    }
    
    /**
     * Create new konsultasi record
     */
    private function createKonsultasi($user, array $validated): Konsultasi
    {
        return Konsultasi::create([
            'N_NIK' => $user->nik,
            'JENIS' => $validated['jenis'],
            'KATEGORI_ADVOKASI' => $validated['kategori_advokasi'] ?? null,
            'TUJUAN' => $validated['tujuan'],
            'TUJUAN_SPESIFIK' => $validated['tujuan_spesifik'] ?? null,
            'JUDUL' => $validated['judul'],
            'DESKRIPSI' => $validated['deskripsi'],
            'STATUS' => 'OPEN',
            'CREATED_BY' => $user->nik,
            'CREATED_AT' => now()
        ]);
    }
    
    /**
     * Verify konsultasi ownership
     */
    private function verifyKonsultasiOwnership($id, string $nik): Konsultasi
    {
        return Konsultasi::where('ID', $id)
                         ->where('N_NIK', $nik)
                         ->firstOrFail();
    }
    
    /**
     * Create comment
     */
    private function createKomentar($konsultasiId, string $nik, string $komentar, string $role = 'USER'): KonsultasiKomentar
    {
        return KonsultasiKomentar::create([
            'ID_KONSULTASI' => $konsultasiId,
            'N_NIK' => $nik,
            'KOMENTAR' => $komentar,
            'PENGIRIM_ROLE' => $role,
            'CREATED_AT' => now(),
            'CREATED_BY' => $nik
        ]);
    }
    
    /**
     * Update status based on activity
     */
    private function updateKonsultasiStatus(Konsultasi $konsultasi, string $updatedBy): void
    {
        if ($konsultasi->STATUS === 'OPEN') {
            $konsultasi->update([
                'STATUS' => 'IN_PROGRESS',
                'UPDATED_BY' => $updatedBy,
                'UPDATED_AT' => now()
            ]);
        }
    }
    
    /**
     * Check if user is admin
     */
    private function isAdmin($user): bool
    {
        if (!$user || !$user->pengurus || !$user->pengurus->role) {
            return false;
        }
        
        $adminRoles = ['ADM', 'ADMIN_DPP', 'ADMIN_DPW', 'ADMIN_DPD'];
        return in_array($user->pengurus->role->NAME, $adminRoles);
    }
    
    /**
     * Send email notification to relevant admins (FINAL - SINGLE EMAIL VERSION)
     */
    private function sendEmailNotification(Konsultasi $konsultasi, string $actionType): void
    {
        // Prevent duplicate emails with cache-based deduplication
        $lockKey = "email_lock_{$konsultasi->ID}_{$actionType}";
        
        // Check if we already sent email for this konsultasi+action in last 2 minutes
        if (Cache::has($lockKey)) {
            Log::info("Email notification skipped (duplicate prevention)", [
                'konsultasi_id' => $konsultasi->ID,
                'action_type' => $actionType,
                'reason' => 'Cache lock active'
            ]);
            return;
        }
        
        // Set lock for 2 minutes to prevent rapid duplicates
        Cache::put($lockKey, true, 120);
        
        try {
            // FORCE SINGLE EMAIL TO ELIMINATE DUPLICATES
            $singleEmail = 'arkhamzahs@gmail.com';
            
            // Send only 1 email regardless of admin structure
            Mail::to($singleEmail)->send(new KonsultasiNotification($konsultasi, $actionType));
            
            Log::info("Single email notification sent for konsultasi ID: {$konsultasi->ID}", [
                'action_type' => $actionType,
                'recipient' => $singleEmail,
                'konsultasi_judul' => $konsultasi->JUDUL,
                'konsultasi_jenis' => $konsultasi->JENIS,
                'konsultasi_tujuan' => $konsultasi->TUJUAN,
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'cache_lock_key' => $lockKey
            ]);
            
        } catch (\Exception $e) {
            // Remove lock on error so it can be retried
            Cache::forget($lockKey);
            
            Log::error("Failed to send email notification for konsultasi ID: {$konsultasi->ID}", [
                'error' => $e->getMessage(),
                'action_type' => $actionType,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Get relevant admin emails based on konsultasi target (BACKUP - NOT USED)
     */
    private function getRelevantAdminEmails(Konsultasi $konsultasi): array
    {
        $karyawan = $konsultasi->karyawan;
        if (!$karyawan) {
            return $this->getFallbackAdminEmails();
        }
        
        $emails = [];
        
        // Get admin emails based on target level
        switch ($konsultasi->TUJUAN) {
            case 'DPD':
                $emails = $this->getDpdAdminEmails($karyawan->V_KOTA_GEDUNG);
                break;
                
            case 'DPW':
                $emails = $this->getDpwAdminEmails($karyawan->V_KOTA_GEDUNG);
                break;
                
            case 'DPP':
                $emails = $this->getDppAdminEmails();
                break;
                
            case 'GENERAL':
            default:
                // Get DPD and DPW admins for general inquiries
                $emails = array_merge(
                    $this->getDpdAdminEmails($karyawan->V_KOTA_GEDUNG),
                    $this->getDpwAdminEmails($karyawan->V_KOTA_GEDUNG)
                );
                break;
        }
        
        // If no specific admins found, get fallback admins
        if (empty($emails)) {
            $emails = $this->getFallbackAdminEmails();
        }
        
        return $emails;
    }
    
    /**
     * Get DPD admin emails (BACKUP - NOT USED)
     */
    private function getDpdAdminEmails(string $kota): array
    {
        return DB::table('users')
            ->join('t_sekar_pengurus', 'users.nik', '=', 't_sekar_pengurus.N_NIK')
            ->join('t_sekar_roles', 't_sekar_pengurus.ID_ROLES', '=', 't_sekar_roles.ID')
            ->join('t_karyawan', 't_sekar_pengurus.N_NIK', '=', 't_karyawan.N_NIK')
            ->where('t_sekar_roles.NAME', 'ADMIN_DPD')
            ->where('t_karyawan.V_KOTA_GEDUNG', $kota)
            ->whereNotNull('users.email')
            ->where('users.email', '!=', '')
            ->pluck('users.email')
            ->toArray();
    }
    
    /**
     * Get DPW admin emails (BACKUP - NOT USED)
     */
    private function getDpwAdminEmails(string $kota): array
    {
        // Map city to DPW
        $dpwMapping = $this->getDpwMapping();
        $dpw = $dpwMapping[$kota] ?? 'DPW Jabar';
        
        return DB::table('users')
            ->join('t_sekar_pengurus', 'users.nik', '=', 't_sekar_pengurus.N_NIK')
            ->join('t_sekar_roles', 't_sekar_pengurus.ID_ROLES', '=', 't_sekar_roles.ID')
            ->where('t_sekar_roles.NAME', 'ADMIN_DPW')
            ->where('t_sekar_pengurus.DPW', $dpw)
            ->whereNotNull('users.email')
            ->where('users.email', '!=', '')
            ->pluck('users.email')
            ->toArray();
    }
    
    /**
     * Get DPP admin emails (BACKUP - NOT USED)
     */
    private function getDppAdminEmails(): array
    {
        return DB::table('users')
            ->join('t_sekar_pengurus', 'users.nik', '=', 't_sekar_pengurus.N_NIK')
            ->join('t_sekar_roles', 't_sekar_pengurus.ID_ROLES', '=', 't_sekar_roles.ID')
            ->where('t_sekar_roles.NAME', 'ADMIN_DPP')
            ->whereNotNull('users.email')
            ->where('users.email', '!=', '')
            ->pluck('users.email')
            ->toArray();
    }
    
    /**
     * Get fallback admin emails when no specific admins found (BACKUP - NOT USED)
     */
    private function getFallbackAdminEmails(): array
    {
        return DB::table('users')
            ->join('t_sekar_pengurus', 'users.nik', '=', 't_sekar_pengurus.N_NIK')
            ->join('t_sekar_roles', 't_sekar_pengurus.ID_ROLES', '=', 't_sekar_roles.ID')
            ->where('t_sekar_roles.NAME', 'ADM') // Super admin
            ->whereNotNull('users.email')
            ->where('users.email', '!=', '')
            ->pluck('users.email')
            ->toArray();
    }
}