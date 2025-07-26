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

        // Check if user is admin and redirect accordingly
        $redirectUrl = $this->getRedirectUrl($user);
        
        return redirect()->intended($redirectUrl);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|unique:users,nik',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'iuran_sukarela' => 'nullable|string',
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
        }

        try {
            DB::transaction(function () use ($validated, $karyawan, $iuranSukarela) {
                // Generate dummy email based on NIK
                $email = $validated['nik'] . '@sekar.local';

                $user = User::create([
                    'nik' => $validated['nik'],
                    'name' => $validated['name'],
                    'email' => $email,
                    'password' => Hash::make($validated['password']),
                ]);

                // Create or update iuran record - PREVENT DUPLICATES
                $this->createOrUpdateIuranRecord($validated['nik'], $iuranSukarela);

                Auth::login($user);
            });

            // Check if newly registered user is admin
            $redirectUrl = $this->getRedirectUrl(Auth::user());
            
            return redirect($redirectUrl)->with('success', 'Pendaftaran berhasil! Selamat datang di SEKAR.');

        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return back()->withInput()
                        ->with('error', 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Determine redirect URL based on user role
     */
    private function getRedirectUrl($user)
    {
        try {
            // Check if user is admin using database query
            $adminData = DB::select("
                SELECT sr.NAME as role_name
                FROM t_sekar_pengurus sp
                LEFT JOIN t_sekar_roles sr ON sp.ID_ROLES = sr.ID
                WHERE sp.N_NIK = ?
            ", [$user->nik]);

            if (!empty($adminData)) {
                $roleName = $adminData[0]->role_name;
                $adminRoles = ['ADM', 'ADMIN_DPP', 'ADMIN_DPW', 'ADMIN_DPD'];
                
                if (in_array($roleName, $adminRoles)) {
                    // User is admin, redirect to admin dashboard
                    Log::info('Admin user logged in, redirecting to admin dashboard', [
                        'user_nik' => $user->nik,
                        'user_name' => $user->name,
                        'role' => $roleName
                    ]);
                    
                    return route('admin.dashboard');
                }
            }

            // User is not admin, redirect to regular dashboard
            Log::info('Regular user logged in, redirecting to user dashboard', [
                'user_nik' => $user->nik,
                'user_name' => $user->name
            ]);
            
            return route('dashboard');

        } catch (\Exception $e) {
            Log::error('Error determining redirect URL: ' . $e->getMessage());
            // Fallback to regular dashboard if error occurs
            return route('dashboard');
        }
    }

    /**
     * Create or update iuran record
     */
    private function createOrUpdateIuranRecord($nik, $iuranSukarela)
    {
        try {
            // Check if record already exists
            $existingIuran = Iuran::where('N_NIK', $nik)->first();
            
            if ($existingIuran) {
                // Update existing record
                $existingIuran->update([
                    'N_IURAN_SUKARELA' => $iuranSukarela,
                    'UPDATED_AT' => now(),
                ]);
                Log::info('Updated existing iuran record', ['nik' => $nik, 'iuran' => $iuranSukarela]);
            } else {
                // Create new record
                Iuran::create([
                    'N_NIK' => $nik,
                    'N_IURAN_SUKARELA' => $iuranSukarela,
                    'CREATED_AT' => now(),
                ]);
                Log::info('Created new iuran record', ['nik' => $nik, 'iuran' => $iuranSukarela]);
            }
        } catch (\Exception $e) {
            Log::error('Error in iuran record creation/update: ' . $e->getMessage());
            // Don't throw exception here to avoid breaking registration
        }
    }

    /**
     * Check if user is admin (static method for use in other controllers)
     */
    public static function isUserAdmin($user)
    {
        try {
            $adminData = DB::select("
                SELECT sr.NAME as role_name
                FROM t_sekar_pengurus sp
                LEFT JOIN t_sekar_roles sr ON sp.ID_ROLES = sr.ID
                WHERE sp.N_NIK = ?
            ", [$user->nik]);

            if (!empty($adminData)) {
                $roleName = $adminData[0]->role_name;
                $adminRoles = ['ADM', 'ADMIN_DPP', 'ADMIN_DPW', 'ADMIN_DPD'];
                return in_array($roleName, $adminRoles);
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Error checking admin status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user admin role
     */
    public static function getUserAdminRole($user)
    {
        try {
            $adminData = DB::select("
                SELECT sr.NAME as role_name, sr.DESC as role_desc
                FROM t_sekar_pengurus sp
                LEFT JOIN t_sekar_roles sr ON sp.ID_ROLES = sr.ID
                WHERE sp.N_NIK = ?
            ", [$user->nik]);

            if (!empty($adminData)) {
                return $adminData[0];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error getting admin role: ' . $e->getMessage());
            return null;
        }
    }
}