<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\SekarPengurus;
use App\Models\ExAnggota;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get real statistics from database
        $anggotaAktif = User::count();
        $pengurus = SekarPengurus::count();
        $anggotaKeluar = ExAnggota::count();
        
        // Calculate non-anggota (employees who haven't registered as members)
        $totalKaryawan = Karyawan::count();
        $nonAnggota = max(0, $totalKaryawan - $anggotaAktif);

        // Get DPW mapping data with accurate joins
        $dpwMapping = DB::table('t_sekar_pengurus as sp')
            ->join('t_karyawan as k', 'sp.N_NIK', '=', 'k.N_NIK')
            ->select(
                DB::raw("COALESCE(NULLIF(TRIM(sp.DPW), ''), 'DPW Jabar') as dpw"),
                DB::raw("COALESCE(NULLIF(TRIM(sp.DPD), ''), 'DPD Bandung') as dpd"),
                DB::raw('COUNT(DISTINCT sp.N_NIK) as pengurus'),
                // Count active members in this DPW/DPD
                DB::raw('(SELECT COUNT(DISTINCT u.nik) 
                         FROM users u 
                         JOIN t_karyawan tk ON u.nik = tk.N_NIK 
                         WHERE tk.V_KOTA_GEDUNG = k.V_KOTA_GEDUNG) as anggota_aktif'),
                // Count ex-members in this DPW/DPD  
                DB::raw('(SELECT COUNT(DISTINCT ex.N_NIK) 
                         FROM t_ex_anggota ex 
                         WHERE ex.V_KOTA_GEDUNG = k.V_KOTA_GEDUNG) as anggota_keluar'),
                // Count non-members in this area
                DB::raw('(SELECT COUNT(DISTINCT tk2.N_NIK) 
                         FROM t_karyawan tk2 
                         LEFT JOIN users u2 ON tk2.N_NIK = u2.nik 
                         WHERE tk2.V_KOTA_GEDUNG = k.V_KOTA_GEDUNG 
                         AND u2.nik IS NULL) as non_anggota')
            )
            ->groupBy('sp.DPW', 'sp.DPD', 'k.V_KOTA_GEDUNG')
            ->orderBy('dpw')
            ->orderBy('dpd')
            ->get();

        // If no pengurus data exists, create a default entry based on existing data
        if ($dpwMapping->isEmpty()) {
            $defaultCity = Karyawan::select('V_KOTA_GEDUNG')
                ->whereNotNull('V_KOTA_GEDUNG')
                ->first();
            
            $cityName = $defaultCity ? $defaultCity->V_KOTA_GEDUNG : 'BANDUNG';
            
            $dpwMapping = collect([
                (object)[
                    'dpw' => 'DPW Jabar',
                    'dpd' => 'DPD ' . ucfirst(strtolower($cityName)),
                    'anggota_aktif' => $anggotaAktif,
                    'pengurus' => $pengurus,
                    'anggota_keluar' => $anggotaKeluar,
                    'non_anggota' => $nonAnggota
                ]
            ]);
        }

        // Calculate growth indicators (simplified for now)
        $growthData = [
            'anggota_aktif_growth' => $anggotaAktif > 0 ? '+' . $anggotaAktif : '0',
            'pengurus_growth' => $pengurus > 0 ? '+' . $pengurus : '0', 
            'anggota_keluar_growth' => $anggotaKeluar > 0 ? '+' . $anggotaKeluar : '0',
            'non_anggota_growth' => $nonAnggota > 0 ? ($nonAnggota > 50 ? '+' . ($nonAnggota - 50) : '0') : '0'
        ];

        return view('dashboard', compact(
            'anggotaAktif',
            'pengurus', 
            'anggotaKeluar',
            'nonAnggota',
            'dpwMapping',
            'growthData'
        ));
    }
}