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
        $totalPengurus = SekarPengurus::count();
        $anggotaKeluar = ExAnggota::count();
        
        // Calculate non-anggota (employees who haven't registered as members)
        $totalKaryawan = Karyawan::count();
        $nonAnggota = max(0, $totalKaryawan - $anggotaAktif);

        // Get DPW mapping data with real joins from database
        $dpwMapping = DB::table('t_sekar_pengurus as sp')
            ->join('t_karyawan as k', 'sp.N_NIK', '=', 'k.N_NIK')
            ->select(
                DB::raw("COALESCE(NULLIF(TRIM(sp.DPW), ''), 'DPW Jabar') as dpw"),
                DB::raw("COALESCE(NULLIF(TRIM(sp.DPD), ''), CONCAT('DPD ', UPPER(k.V_KOTA_GEDUNG))) as dpd"),
                'k.V_KOTA_GEDUNG as kota',
                DB::raw('COUNT(DISTINCT sp.N_NIK) as pengurus')
            )
            ->groupBy('sp.DPW', 'sp.DPD', 'k.V_KOTA_GEDUNG')
            ->get();

        // For each DPW/DPD combination, get member statistics
        $mappingWithStats = $dpwMapping->map(function ($mapping, $index) use ($anggotaAktif, $anggotaKeluar, $nonAnggota) {
            $kota = $mapping->kota;
            
            // Count active members in this area
            $anggotaAktifArea = DB::table('users as u')
                ->join('t_karyawan as k', 'u.nik', '=', 'k.N_NIK')
                ->where('k.V_KOTA_GEDUNG', $kota)
                ->count();
            
            // Count ex-members in this area
            $anggotaKeluarArea = DB::table('t_ex_anggota')
                ->where('V_KOTA_GEDUNG', $kota)
                ->count();
            
            // Count non-members in this area
            $nonAnggotaArea = DB::table('t_karyawan as k')
                ->leftJoin('users as u', 'k.N_NIK', '=', 'u.nik')
                ->where('k.V_KOTA_GEDUNG', $kota)
                ->whereNull('u.nik')
                ->count();
            
            return (object)[
                'dpw' => $mapping->dpw,
                'dpd' => $mapping->dpd,
                'pengurus' => $mapping->pengurus,
                'anggota_aktif' => $anggotaAktifArea,
                'anggota_keluar' => $anggotaKeluarArea,
                'non_anggota' => $nonAnggotaArea
            ];
        });

        // If no pengurus data exists, create default entry
        if ($mappingWithStats->isEmpty()) {
            $defaultCity = Karyawan::select('V_KOTA_GEDUNG')
                ->whereNotNull('V_KOTA_GEDUNG')
                ->groupBy('V_KOTA_GEDUNG')
                ->first();
            
            $cityName = $defaultCity ? strtoupper($defaultCity->V_KOTA_GEDUNG) : 'BANDUNG';
            
            $mappingWithStats = collect([
                (object)[
                    'dpw' => 'DPW Jabar',
                    'dpd' => 'DPD ' . $cityName,
                    'anggota_aktif' => $anggotaAktif,
                    'pengurus' => $totalPengurus,
                    'anggota_keluar' => $anggotaKeluar,
                    'non_anggota' => $nonAnggota
                ]
            ]);
        }

        // Calculate growth indicators based on recent data
        $growthData = [
            'anggota_aktif_growth' => $anggotaAktif > 0 ? '+' . $anggotaAktif : '0',
            'pengurus_growth' => $totalPengurus > 0 ? '+' . $totalPengurus : '0', 
            'anggota_keluar_growth' => $anggotaKeluar > 0 ? '+' . $anggotaKeluar : '0',
            'non_anggota_growth' => $nonAnggota > 0 ? ($nonAnggota > 50 ? '+' . ($nonAnggota - 50) : '0') : '0'
        ];

        return view('dashboard', compact(
            'anggotaAktif',
            'totalPengurus', 
            'anggotaKeluar',
            'nonAnggota',
            'mappingWithStats',
            'growthData'
        ));
    }
}