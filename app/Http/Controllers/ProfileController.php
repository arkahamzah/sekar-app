<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Karyawan;
use App\Models\Iuran;
use App\Models\Params;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get employee data
        $karyawan = Karyawan::where('N_NIK', $user->nik)->first();
        
        // Get current iuran data
        $iuran = Iuran::where('N_NIK', $user->nik)->first();
        
        // Get active parameters (iuran wajib)
        $params = Params::where('IS_AKTIF', '1')
                       ->where('TAHUN', date('Y'))
                       ->first();
        
        $iuranWajib = $params ? (int)$params->NOMINAL_IURAN_WAJIB : 25000;
        $iuranSukarela = $iuran ? (int)$iuran->IURAN_SUKARELA : 0;
        
        // Calculate total monthly iuran
        $totalIuranPerBulan = $iuranWajib + $iuranSukarela;
        
        // Calculate months since joining (simplified)
        $joinDate = $user->created_at;
        $currentDate = now();
        
        // Get year and month difference
        $yearDiff = $currentDate->year - $joinDate->year;
        $monthDiff = $currentDate->month - $joinDate->month;
        
        // Total months including current month
        $monthsSinceJoin = ($yearDiff * 12) + $monthDiff + 1;
        
        // Minimum 1 month for current members
        $monthsSinceJoin = max(1, $monthsSinceJoin);
        
        // Calculate total iuran paid
        $totalIuran = $totalIuranPerBulan * $monthsSinceJoin;
        
        // Get iuran history (for future implementation)
        $iuranHistory = collect(); // Will be implemented when we have history table
        
        return view('profile.index', compact(
            'user',
            'karyawan', 
            'iuran',
            'iuranWajib',
            'iuranSukarela',
            'totalIuranPerBulan',
            'totalIuran',
            'joinDate',
            'iuranHistory'
        ));
    }
    
    public function updateIuranSukarela(Request $request)
    {
        $request->validate([
            'iuran_sukarela' => 'required|numeric|min:0',
        ]);
        
        $user = Auth::user();
        $iuranSukarela = (int)$request->iuran_sukarela;
        
        // Check if iuran record exists
        $iuran = Iuran::where('N_NIK', $user->nik)->first();
        
        if ($iuran) {
            // Update existing record
            $iuran->update([
                'IURAN_SUKARELA' => $iuranSukarela,
                'UPDATE_BY' => $user->nik,
                'UPDATED_AT' => now()
            ]);
        } else {
            // Create new record
            $params = Params::where('IS_AKTIF', '1')->where('TAHUN', date('Y'))->first();
            $iuranWajib = $params ? $params->NOMINAL_IURAN_WAJIB : '25000';
            
            Iuran::create([
                'N_NIK' => $user->nik,
                'IURAN_WAJIB' => $iuranWajib,
                'IURAN_SUKARELA' => $iuranSukarela,
                'CREATED_BY' => $user->nik,
                'CREATED_AT' => now()
            ]);
        }
        
        return redirect()->route('profile.index')
                        ->with('success', 'Iuran sukarela berhasil diperbarui. Perubahan akan diproses dalam 1-2 bulan.');
    }
}