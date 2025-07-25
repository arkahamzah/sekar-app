<?php

namespace App\Http\Controllers;

use App\Models\Konsultasi;
use App\Models\KonsultasiKomentar;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            $konsultasi = $this->createKonsultasi($user, $validated);
            
            return redirect()->route('konsultasi.index')
                            ->with('success', 'Advokasi/Aspirasi berhasil dibuat dan akan segera ditindaklanjuti.');
        } catch (\Exception $e) {
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
            'komentar' => 'required|string'
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
            });
            
            return redirect()->route('konsultasi.show', $id)
                            ->with('success', 'Komentar berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menambahkan komentar.');
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
            // Add more mappings as needed
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
            // Add more mappings as needed
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
            'Lainnya'
        ];
    }
    
    /**
     * Create new record
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
     * Verify ownership
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
    private function createKomentar($konsultasiId, string $nik, string $komentar): KonsultasiKomentar
    {
        return KonsultasiKomentar::create([
            'ID_KONSULTASI' => $konsultasiId,
            'N_NIK' => $nik,
            'KOMENTAR' => $komentar,
            'PENGIRIM_ROLE' => 'USER',
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
}