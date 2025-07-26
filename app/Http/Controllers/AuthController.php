<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Karyawan;
use App\Models\Iuran;
use App\Models\Params;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nik' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('nik', $credentials['nik'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'nik' => ['NIK atau password salah.'],
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|unique:users,nik',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'iuran_sukarela' => 'nullable|string',
            'agreement' => 'required|accepted',
            'email_agreement' => 'required|accepted',
        ], [
            'email.required' => 'Email pribadi wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'agreement.required' => 'Anda harus menyetujui syarat dan ketentuan.',
            'email_agreement.required' => 'Anda harus menyetujui penggunaan email untuk notifikasi.',
        ]);

        // Check if NIK exists in karyawan table
        $karyawan = Karyawan::where('N_NIK', $validated['nik'])->first();
        
        if (!$karyawan) {
            throw ValidationException::withMessages([
                'nik' => ['NIK tidak ditemukan dalam data karyawan.'],
            ]);
        }

        // Process iuran sukarela
        $iuranSukarela = 0;
        if (!empty($validated['iuran_sukarela'])) {
            // Remove all non-numeric characters and convert to integer
            $cleanedIuran = preg_replace('/[^0-9]/', '', $validated['iuran_sukarela']);
            $iuranSukarela = (int) $cleanedIuran;
            
            // Validate kelipatan 5000
            if ($iuranSukarela > 0 && $iuranSukarela % 5000 !== 0) {
                throw ValidationException::withMessages([
                    'iuran_sukarela' => ['Iuran sukarela harus dalam kelipatan Rp 5.000.'],
                ]);
            }
        }

        try {
            DB::transaction(function () use ($validated, $karyawan, $iuranSukarela) {
                $user = User::create([
                    'nik' => $validated['nik'],
                    'name' => $validated['name'],
                    'email' => $validated['email'], // Use real email
                    'password' => Hash::make($validated['password']),
                ]);

                // Create or update iuran record - PREVENT DUPLICATES
                $this->createOrUpdateIuranRecord($validated['nik'], $iuranSukarela);

                // Log successful registration with real email
                Log::info('User registered with real email', [
                    'nik' => $user->nik,
                    'name' => $user->name,
                    'email' => $user->email,
                    'iuran_sukarela' => $iuranSukarela
                ]);

                Auth::login($user);
            });

            return redirect()->route('dashboard')->with('success', 'Pendaftaran berhasil! Selamat datang di SEKAR. Email konfirmasi akan dikirim ke ' . $validated['email']);
            
        } catch (\Exception $e) {
            Log::error('Registration Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $validated
            ]);
            
            return back()->withInput()->with('error', 'Terjadi kesalahan saat registrasi: ' . $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Create or update iuran record - PREVENT DUPLICATES
     */
    private function createOrUpdateIuranRecord(string $nik, int $iuranSukarela): void
    {
        $params = Params::where('IS_AKTIF', '1')
                        ->where('TAHUN', date('Y'))
                        ->first();
        
        $iuranWajib = $params ? $params->NOMINAL_IURAN_WAJIB : '25000';

        // CHECK FOR EXISTING RECORD FIRST
        $existingIuran = Iuran::where('N_NIK', $nik)->first();
        
        if ($existingIuran) {
            // UPDATE EXISTING RECORD - DON'T CREATE NEW ONE
            $existingIuran->update([
                'IURAN_WAJIB' => $iuranWajib,
                'IURAN_SUKARELA' => (string) $iuranSukarela,
                'UPDATE_BY' => $nik,
                'UPDATED_AT' => now(),
            ]);
            
            Log::info('Iuran Record Updated:', [
                'nik' => $nik,
                'iuran_sukarela' => (string) $iuranSukarela
            ]);
        } else {
            // CREATE NEW RECORD ONLY IF NOT EXISTS
            $iuranRecord = Iuran::create([
                'N_NIK' => $nik,
                'IURAN_WAJIB' => $iuranWajib,
                'IURAN_SUKARELA' => (string) $iuranSukarela,
                'CREATED_BY' => $nik,
                'CREATED_AT' => now(),
            ]);

            Log::info('Iuran Record Created:', [
                'record_id' => $iuranRecord->ID,
                'nik' => $nik,
                'iuran_sukarela' => (string) $iuranSukarela
            ]);
        }
    }
}