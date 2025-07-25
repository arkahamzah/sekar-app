<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Karyawan;
use App\Models\Iuran;
use App\Models\IuranHistory;
use App\Models\Params;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        
        // Calculate months since joining
        $joinDate = $user->created_at;
        $currentDate = now();
        
        $yearDiff = $currentDate->year - $joinDate->year;
        $monthDiff = $currentDate->month - $joinDate->month;
        
        // Total months including current month
        $monthsSinceJoin = ($yearDiff * 12) + $monthDiff + 1;
        $monthsSinceJoin = max(1, $monthsSinceJoin);
        
        // Calculate total iuran paid
        $totalIuran = $totalIuranPerBulan * $monthsSinceJoin;
        
        // Get iuran history
        $iuranHistory = IuranHistory::where('N_NIK', $user->nik)
                                  ->orderBy('CREATED_AT', 'desc')
                                  ->get();
        
        // Check if there's pending iuran sukarela change
        $pendingChange = IuranHistory::where('N_NIK', $user->nik)
                                   ->where('JENIS', 'SUKARELA')
                                   ->where('STATUS_PROSES', 'PENDING')
                                   ->first();
        
        // Calculate effective iuran sukarela (considering pending changes)
        $effectiveIuranSukarela = $iuranSukarela;
        if ($pendingChange) {
            $effectiveIuranSukarela = (int)$pendingChange->NOMINAL_BARU;
        }
        
        return view('profile.index', compact(
            'user',
            'karyawan', 
            'iuran',
            'iuranWajib',
            'iuranSukarela',
            'effectiveIuranSukarela',
            'totalIuranPerBulan',
            'totalIuran',
            'joinDate',
            'iuranHistory',
            'pendingChange'
        ));
    }
    
    public function updateIuranSukarela(Request $request)
    {
        $request->validate([
            'iuran_sukarela' => 'required|numeric|min:0',
        ]);
        
        $user = Auth::user();
        $iuranSukarelaLama = 0;
        $iuranSukarelaBaru = (int)$request->iuran_sukarela;
        
        // Get current iuran sukarela
        $iuran = Iuran::where('N_NIK', $user->nik)->first();
        if ($iuran) {
            $iuranSukarelaLama = (int)$iuran->IURAN_SUKARELA;
        }
        
        // Check if there's any change
        if ($iuranSukarelaLama == $iuranSukarelaBaru) {
            return redirect()->route('profile.index')
                           ->with('info', 'Tidak ada perubahan pada iuran sukarela.');
        }
        
        // Check if there's already pending change
        $pendingChange = IuranHistory::where('N_NIK', $user->nik)
                                   ->where('JENIS', 'SUKARELA')
                                   ->whereIn('STATUS_PROSES', ['PENDING', 'PROCESSED'])
                                   ->first();
        
        if ($pendingChange) {
            return redirect()->route('profile.index')
                           ->with('error', 'Masih ada perubahan iuran sukarela yang sedang diproses. Silakan tunggu hingga selesai.');
        }
        
        DB::transaction(function () use ($user, $iuranSukarelaLama, $iuranSukarelaBaru, $iuran) {
            // Create history record
            IuranHistory::createWithDates([
                'N_NIK' => $user->nik,
                'JENIS' => 'SUKARELA',
                'NOMINAL_LAMA' => $iuranSukarelaLama,
                'NOMINAL_BARU' => $iuranSukarelaBaru,
                'STATUS_PROSES' => 'PENDING',
                'TGL_PERUBAHAN' => now(),
                'KETERANGAN' => 'Perubahan iuran sukarela oleh anggota',
                'CREATED_BY' => $user->nik,
                'CREATED_AT' => now()
            ]);
            
            // Update or create iuran record (for immediate display, will be processed later)
            if ($iuran) {
                $iuran->update([
                    'IURAN_SUKARELA' => $iuranSukarelaBaru,
                    'UPDATE_BY' => $user->nik,
                    'UPDATED_AT' => now()
                ]);
            } else {
                $params = Params::where('IS_AKTIF', '1')->where('TAHUN', date('Y'))->first();
                $iuranWajib = $params ? $params->NOMINAL_IURAN_WAJIB : '25000';
                
                Iuran::create([
                    'N_NIK' => $user->nik,
                    'IURAN_WAJIB' => $iuranWajib,
                    'IURAN_SUKARELA' => $iuranSukarelaBaru,
                    'CREATED_BY' => $user->nik,
                    'CREATED_AT' => now()
                ]);
            }
        });
        
        return redirect()->route('profile.index')
                        ->with('success', 'Iuran sukarela berhasil diperbarui. Perubahan akan diproses dalam 1 bulan dan diterapkan dalam 2 bulan sesuai kebijakan HC.');
    }
}