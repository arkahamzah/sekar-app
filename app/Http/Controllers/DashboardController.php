<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\SekarPengurus;
use App\Models\ExAnggota;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Display dashboard with statistics and DPW mapping
     */
    public function index()
    {
        // Cache key for dashboard statistics
        $cacheKey = 'dashboard_statistics';
        
        $data = Cache::remember($cacheKey, 300, function () { // Cache for 5 minutes
            return $this->getDashboardData();
        });

        return view('dashboard', $data);
    }

    /**
     * Get all dashboard data
     */
    private function getDashboardData(): array
    {
        $statistics = $this->getStatistics();
        $mappingWithStats = $this->getDpwMappingWithStats();
        $growthData = $this->getGrowthData($statistics);

        return array_merge($statistics, [
            'mappingWithStats' => $mappingWithStats,
            'growthData' => $growthData
        ]);
    }

    /**
     * Get basic statistics
     */
    private function getStatistics(): array
    {
        return [
            'anggotaAktif' => User::count(),
            'totalPengurus' => SekarPengurus::count(),
            'anggotaKeluar' => ExAnggota::count(),
            'nonAnggota' => $this->getNonAnggotaCount(),
        ];
    }

    /**
     * Calculate non-anggota count
     */
    private function getNonAnggotaCount(): int
    {
        $totalKaryawan = Karyawan::count();
        $anggotaAktif = User::count();
        
        return max(0, $totalKaryawan - $anggotaAktif);
    }

    /**
     * Get DPW mapping with statistics
     */
    private function getDpwMappingWithStats()
    {
        $dpwMapping = $this->getDpwMappingQuery();

        if ($dpwMapping->isEmpty()) {
            return $this->getDefaultMapping();
        }

        return $dpwMapping->map(function ($mapping) {
            return $this->enrichMappingWithStats($mapping);
        });
    }

    /**
     * Get DPW mapping query
     */
    private function getDpwMappingQuery()
    {
        return DB::table('t_sekar_pengurus as sp')
            ->join('t_karyawan as k', 'sp.N_NIK', '=', 'k.N_NIK')
            ->select(
                DB::raw("COALESCE(NULLIF(TRIM(sp.DPW), ''), 'DPW Jabar') as dpw"),
                DB::raw("COALESCE(NULLIF(TRIM(sp.DPD), ''), CONCAT('DPD ', UPPER(k.V_KOTA_GEDUNG))) as dpd"),
                'k.V_KOTA_GEDUNG as kota',
                DB::raw('COUNT(DISTINCT sp.N_NIK) as pengurus')
            )
            ->groupBy('sp.DPW', 'sp.DPD', 'k.V_KOTA_GEDUNG')
            ->get();
    }

    /**
     * Get default mapping when no pengurus data exists
     */
    private function getDefaultMapping()
    {
        $defaultCity = Karyawan::select('V_KOTA_GEDUNG')
            ->whereNotNull('V_KOTA_GEDUNG')
            ->groupBy('V_KOTA_GEDUNG')
            ->first();
        
        $cityName = $defaultCity ? strtoupper($defaultCity->V_KOTA_GEDUNG) : 'BANDUNG';
        
        return collect([
            (object)[
                'dpw' => 'DPW Jabar',
                'dpd' => 'DPD ' . $cityName,
                'anggota_aktif' => User::count(),
                'pengurus' => SekarPengurus::count(),
                'anggota_keluar' => ExAnggota::count(),
                'non_anggota' => $this->getNonAnggotaCount()
            ]
        ]);
    }

    /**
     * Enrich mapping with area-specific statistics
     */
    private function enrichMappingWithStats($mapping): object
    {
        $kota = $mapping->kota;
        
        return (object)[
            'dpw' => $mapping->dpw,
            'dpd' => $mapping->dpd,
            'pengurus' => $mapping->pengurus,
            'anggota_aktif' => $this->getAnggotaAktifByArea($kota),
            'anggota_keluar' => $this->getAnggotaKeluarByArea($kota),
            'non_anggota' => $this->getNonAnggotaByArea($kota)
        ];
    }

    /**
     * Count active members in specific area
     */
    private function getAnggotaAktifByArea(string $kota): int
    {
        return DB::table('users as u')
            ->join('t_karyawan as k', 'u.nik', '=', 'k.N_NIK')
            ->where('k.V_KOTA_GEDUNG', $kota)
            ->count();
    }

    /**
     * Count ex-members in specific area
     */
    private function getAnggotaKeluarByArea(string $kota): int
    {
        return ExAnggota::where('V_KOTA_GEDUNG', $kota)->count();
    }

    /**
     * Count non-members in specific area
     */
    private function getNonAnggotaByArea(string $kota): int
    {
        return DB::table('t_karyawan as k')
            ->leftJoin('users as u', 'k.N_NIK', '=', 'u.nik')
            ->where('k.V_KOTA_GEDUNG', $kota)
            ->whereNull('u.nik')
            ->count();
    }

    /**
     * Generate growth indicators
     */
    private function getGrowthData(array $statistics): array
    {
        return [
            'anggota_aktif_growth' => $statistics['anggotaAktif'] > 0 ? '+' . $statistics['anggotaAktif'] : '0',
            'pengurus_growth' => $statistics['totalPengurus'] > 0 ? '+' . $statistics['totalPengurus'] : '0',
            'anggota_keluar_growth' => $statistics['anggotaKeluar'] > 0 ? '+' . $statistics['anggotaKeluar'] : '0',
            'non_anggota_growth' => $statistics['nonAnggota'] > 0 
                ? ($statistics['nonAnggota'] > 50 ? '+' . ($statistics['nonAnggota'] - 50) : '0') 
                : '0'
        ];
    }
}