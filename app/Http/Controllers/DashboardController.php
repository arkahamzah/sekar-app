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
        $anggotaAktif = User::count(); // Users who are registered as members
        $pengurus = SekarPengurus::count(); // Active board members
        $anggotaKeluar = ExAnggota::count(); // Ex members
        
        // Calculate non-anggota (karyawan yang belum jadi anggota)
        $totalKaryawan = Karyawan::count();
        $nonAnggota = $totalKaryawan - $anggotaAktif;

        // Calculate real growth indicators based on recent data
        // For demo purposes, using simple calculations - in real app would compare with previous periods
        $growthData = [
            'anggota_aktif_growth' => $anggotaAktif > 0 ? '+' . $anggotaAktif : '0',
            'pengurus_growth' => $pengurus > 2 ? '+' . ($pengurus - 2) : '-' . (2 - $pengurus),
            'anggota_keluar_growth' => $anggotaKeluar > 0 ? '+' . $anggotaKeluar : '0',
            'non_anggota_growth' => $nonAnggota > 100 ? '+' . ($nonAnggota - 100) : '-' . (100 - $nonAnggota)
        ];

        // Get DPW mapping data with real calculations
        $dpwMapping = DB::table('t_sekar_pengurus as sp')
            ->leftJoin('users as u', 'sp.N_NIK', '=', 'u.nik')
            ->leftJoin('t_karyawan as k', 'sp.N_NIK', '=', 'k.N_NIK')
            ->leftJoin('t_ex_anggota as ex', 'sp.N_NIK', '=', 'ex.N_NIK')
            ->select(
                DB::raw("COALESCE(NULLIF(sp.DPW, ''), 'DPW Jabar') as dpw"),
                DB::raw("COALESCE(NULLIF(sp.DPD, ''), 'DPW Japati') as dpd"),
                DB::raw('COUNT(DISTINCT CASE WHEN u.nik IS NOT NULL THEN u.nik END) as anggota_aktif'),
                DB::raw('COUNT(DISTINCT sp.N_NIK) as pengurus'),
                DB::raw('COUNT(DISTINCT ex.N_NIK) as anggota_keluar'),
                DB::raw('COUNT(DISTINCT CASE WHEN u.nik IS NULL AND k.N_NIK IS NOT NULL THEN k.N_NIK END) as non_anggota')
            )
            ->groupBy('sp.DPW', 'sp.DPD')
            ->get();

        // If no pengurus data, create default entry with real data
        if ($dpwMapping->isEmpty()) {
            $dpwMapping = collect([
                (object)[
                    'dpw' => 'DPW Jabar',
                    'dpd' => 'DPW Japati',
                    'anggota_aktif' => $anggotaAktif,
                    'pengurus' => $pengurus,
                    'anggota_keluar' => $anggotaKeluar,
                    'non_anggota' => $nonAnggota
                ]
            ]);
        }

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