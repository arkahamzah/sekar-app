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
    public function index()
    {
        $user = Auth::user();
        
        // Get user's consultations
        $konsultasi = Konsultasi::where('N_NIK', $user->nik)
                                ->with(['karyawan'])
                                ->orderBy('CREATED_AT', 'desc')
                                ->get();
        
        return view('konsultasi.index', compact('konsultasi'));
    }
    
    public function create()
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('N_NIK', $user->nik)->first();
        
        // Get available DPW/DPD options based on location
        $availableTargets = $this->getAvailableTargets($karyawan);
        
        // Kategori advokasi
        $kategoriAdvokasi = [
            'Pelanggaran Hak Pekerja',
            'Masalah Kesejahteraan',
            'Diskriminasi',
            'Keselamatan Kerja',
            'Lainnya'
        ];
        
        return view('konsultasi.create', compact('karyawan', 'availableTargets', 'kategoriAdvokasi'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:ADVOKASI,ASPIRASI',
            'kategori_advokasi' => 'required_if:jenis,ADVOKASI',
            'tujuan' => 'required|in:DPP,DPW,DPD,GENERAL',
            'tujuan_spesifik' => 'required_unless:tujuan,GENERAL',
            'judul' => 'required|max:200',
            'deskripsi' => 'required'
        ]);
        
        $user = Auth::user();
        
        Konsultasi::create([
            'N_NIK' => $user->nik,
            'JENIS' => $request->jenis,
            'KATEGORI_ADVOKASI' => $request->kategori_advokasi,
            'TUJUAN' => $request->tujuan,
            'TUJUAN_SPESIFIK' => $request->tujuan_spesifik,
            'JUDUL' => $request->judul,
            'DESKRIPSI' => $request->deskripsi,
            'STATUS' => 'OPEN',
            'CREATED_BY' => $user->nik,
            'CREATED_AT' => now()
        ]);
        
        return redirect()->route('konsultasi.index')
                        ->with('success', 'Konsultasi/Aspirasi berhasil dibuat dan akan segera ditindaklanjuti.');
    }
    
    public function show($id)
    {
        $user = Auth::user();
        
        $konsultasi = Konsultasi::where('ID', $id)
                                ->where('N_NIK', $user->nik)
                                ->with(['karyawan', 'komentar.karyawan'])
                                ->firstOrFail();
        
        return view('konsultasi.show', compact('konsultasi'));
    }
    
    public function addComment(Request $request, $id)
    {
        $request->validate([
            'komentar' => 'required'
        ]);
        
        $user = Auth::user();
        
        // Verify ownership
        $konsultasi = Konsultasi::where('ID', $id)
                                ->where('N_NIK', $user->nik)
                                ->firstOrFail();
        
        KonsultasiKomentar::create([
            'ID_KONSULTASI' => $id,
            'N_NIK' => $user->nik,
            'KOMENTAR' => $request->komentar,
            'PENGIRIM_ROLE' => 'USER',
            'CREATED_AT' => now(),
            'CREATED_BY' => $user->nik
        ]);
        
        // Update status to IN_PROGRESS if still OPEN
        if ($konsultasi->STATUS === 'OPEN') {
            $konsultasi->update([
                'STATUS' => 'IN_PROGRESS',
                'UPDATED_BY' => $user->nik,
                'UPDATED_AT' => now()
            ]);
        }
        
        return redirect()->route('konsultasi.show', $id)
                        ->with('success', 'Komentar berhasil ditambahkan.');
    }
    
    private function getAvailableTargets($karyawan)
    {
        $targets = [
            'GENERAL' => 'SEKAR Pusat'
        ];
        
        if ($karyawan) {
            // Based on location, add DPW/DPD options
            $city = $karyawan->V_KOTA_GEDUNG;
            
            // Simple mapping - in real app this would come from database
            $dpwMapping = [
                'BANDUNG' => 'DPW Jabar',
                'JAKARTA' => 'DPW Jakarta',
                'SURABAYA' => 'DPW Jatim',
            ];
            
            $dpdMapping = [
                'BANDUNG' => 'DPD Bandung',
                'JAKARTA' => 'DPD Jakarta Pusat', 
                'SURABAYA' => 'DPD Surabaya',
            ];
            
            if (isset($dpwMapping[$city])) {
                $targets['DPW'] = $dpwMapping[$city];
            }
            
            if (isset($dpdMapping[$city])) {
                $targets['DPD'] = $dpdMapping[$city];
            }
        }
        
        return $targets;
    }
}