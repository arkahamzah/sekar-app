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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Display user profile with iuran information
     */
    public function index()
    {
        $user = Auth::user();
        $profileData = $this->getProfileData($user);

        return view('profile.index', $profileData);
    }

    /**
     * Update iuran sukarela for authenticated user
     */
    public function updateIuranSukarela(Request $request)
    {
        $validated = $request->validate([
            'iuran_sukarela' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $newAmount = (int)$validated['iuran_sukarela'];
        
        try {
            $result = $this->processIuranSukarelaUpdate($user, $newAmount);
            
            return redirect()->route('profile.index')
                           ->with($result['status'], $result['message']);
        } catch (\Exception $e) {
            return redirect()->route('profile.index')
                           ->with('error', 'Terjadi kesalahan saat memproses perubahan iuran.');
        }
    }

    /**
     * Update user email
     */
    public function updateEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'current_password' => 'required|string',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh user lain.',
            'current_password.required' => 'Password saat ini wajib diisi untuk konfirmasi.',
        ]);

        $user = Auth::user();

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return redirect()->route('profile.index')
                           ->withErrors(['current_password' => 'Password saat ini tidak benar.'])
                           ->withInput();
        }

        try {
            $user->update([
                'email' => $validated['email']
            ]);

            Log::info('Email updated successfully', [
                'user_id' => $user->id,
                'nik' => $user->nik,
                'old_email' => $user->getOriginal('email'),
                'new_email' => $validated['email'],
                'timestamp' => now()
            ]);

            return redirect()->route('profile.index')
                           ->with('success', 'Email berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Email update failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('profile.index')
                           ->with('error', 'Terjadi kesalahan saat memperbarui email.');
        }
    }

    /**
     * TAMBAHAN BARU: Show change password form
     */
    public function showChangePasswordForm()
    {
        $user = Auth::user();
        
        return view('profile.change-password', [
            'user' => $user,
            'pageTitle' => 'Ubah Password',
            'breadcrumb' => [
                ['name' => 'Dashboard', 'url' => route('dashboard')],
                ['name' => 'Profile', 'url' => route('profile.index')],
                ['name' => 'Ubah Password', 'url' => null]
            ]
        ]);
    }

    /**
     * TAMBAHAN BARU: Change user password (untuk user yang sudah login)
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak sesuai.',
        ]);

        $user = Auth::user();

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak benar.']);
        }

        // Check if new password is different from current password
        if (Hash::check($validated['new_password'], $user->password)) {
            return back()->withErrors(['new_password' => 'Password baru harus berbeda dari password saat ini.']);
        }

        try {
            // Update password
            $user->update([
                'password' => Hash::make($validated['new_password'])
            ]);

            // Log successful password change
            Log::info('Password changed successfully', [
                'user_id' => $user->id,
                'nik' => $user->nik,
                'email' => $user->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()
            ]);

            // Optional: Send email notification about password change
            try {
                Mail::send('emails.password-changed', [
                    'user' => $user,
                    'timestamp' => now(),
                    'ip_address' => $request->ip()
                ], function ($message) use ($user) {
                    $message->to($user->email)
                           ->subject('Password Akun SEKAR Berhasil Diubah');
                });
            } catch (\Exception $mailException) {
                Log::warning('Failed to send password change notification email', [
                    'user_id' => $user->id,
                    'error' => $mailException->getMessage()
                ]);
                // Don't fail the password change if email fails
            }

            return redirect()->route('profile.change-password')
                           ->with('success', 'Password berhasil diubah. Akun Anda tetap login di session ini.');
                           
        } catch (\Exception $e) {
            Log::error('Password change failed', [
                'user_id' => $user->id,
                'nik' => $user->nik,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat mengubah password. Silakan coba lagi.');
        }
    }

    /**
     * Get comprehensive profile data for user
     */
    private function getProfileData($user)
    {
        try {
            // Get employee data
            $karyawan = Karyawan::where('NIK', $user->nik)->first();
            
            // Get current iuran data
            $currentIuran = Iuran::where('nik', $user->nik)
                                 ->where('tahun', date('Y'))
                                 ->first();
            
            // Get iuran history
            $iuranHistory = IuranHistory::where('nik', $user->nik)
                                       ->orderBy('created_at', 'desc')
                                       ->take(10)
                                       ->get();
            
            // Get current parameters
            $currentParams = Params::where('tahun', date('Y'))
                                  ->where('is_aktif', '1')
                                  ->first();
            
            // Calculate totals
            $totalIuranWajib = $currentIuran ? $currentIuran->iuran_wajib : 0;
            $totalIuranSukarela = $currentIuran ? $currentIuran->iuran_sukarela : 0;
            $totalIuran = $totalIuranWajib + $totalIuranSukarela;
            
            return [
                'user' => $user,
                'karyawan' => $karyawan,
                'currentIuran' => $currentIuran,
                'iuranHistory' => $iuranHistory,
                'currentParams' => $currentParams,
                'totals' => [
                    'iuran_wajib' => $totalIuranWajib,
                    'iuran_sukarela' => $totalIuranSukarela,
                    'total_iuran' => $totalIuran
                ],
                'pageTitle' => 'Profile Sekar',
                'breadcrumb' => [
                    ['name' => 'Dashboard', 'url' => route('dashboard')],
                    ['name' => 'Profile Sekar', 'url' => null]
                ]
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to get profile data', [
                'user_id' => $user->id,
                'nik' => $user->nik,
                'error' => $e->getMessage()
            ]);
            
            // Return minimal data if error occurs
            return [
                'user' => $user,
                'karyawan' => null,
                'currentIuran' => null,
                'iuranHistory' => collect(),
                'currentParams' => null,
                'totals' => [
                    'iuran_wajib' => 0,
                    'iuran_sukarela' => 0,
                    'total_iuran' => 0
                ],
                'pageTitle' => 'Profile Sekar',
                'breadcrumb' => [
                    ['name' => 'Dashboard', 'url' => route('dashboard')],
                    ['name' => 'Profile Sekar', 'url' => null]
                ],
                'error' => 'Terjadi kesalahan saat memuat data profile.'
            ];
        }
    }

    /**
     * Process iuran sukarela update
     */
    private function processIuranSukarelaUpdate($user, $newAmount)
    {
        return DB::transaction(function () use ($user, $newAmount) {
            // Get or create current year iuran record
            $currentIuran = Iuran::firstOrCreate(
                [
                    'nik' => $user->nik,
                    'tahun' => date('Y')
                ],
                [
                    'iuran_wajib' => 0,
                    'iuran_sukarela' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            $oldAmount = $currentIuran->iuran_sukarela;
            
            // Update iuran sukarela
            $currentIuran->update([
                'iuran_sukarela' => $newAmount,
                'updated_at' => now()
            ]);

            // Create history record
            IuranHistory::create([
                'nik' => $user->nik,
                'tahun' => date('Y'),
                'jenis_perubahan' => 'update_iuran_sukarela',
                'nilai_lama' => $oldAmount,
                'nilai_baru' => $newAmount,
                'keterangan' => 'Perubahan iuran sukarela melalui profile',
                'created_by' => $user->nik,
                'created_at' => now()
            ]);

            // Log the change
            Log::info('Iuran sukarela updated', [
                'user_id' => $user->id,
                'nik' => $user->nik,
                'old_amount' => $oldAmount,
                'new_amount' => $newAmount,
                'timestamp' => now()
            ]);

            return [
                'status' => 'success',
                'message' => 'Iuran sukarela berhasil diperbarui dari Rp ' . number_format($oldAmount, 0, ',', '.') . ' menjadi Rp ' . number_format($newAmount, 0, ',', '.')
            ];
        });
    }

    /**
     * API endpoint for profile data (if needed for mobile app)
     */
    public function apiProfile(Request $request)
    {
        $user = $request->user();
        $profileData = $this->getProfileData($user);
        
        return response()->json([
            'success' => true,
            'data' => $profileData
        ]);
    }
}