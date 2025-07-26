<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login')
                           ->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman admin.');
        }

        // Manual check admin status using database
        try {
            $adminData = DB::select("
                SELECT 
                    sp.ID_ROLES, sr.NAME as role_name
                FROM t_sekar_pengurus sp
                LEFT JOIN t_sekar_roles sr ON sp.ID_ROLES = sr.ID
                WHERE sp.N_NIK = ?
            ", [$user->nik]);

            if (empty($adminData)) {
                Log::warning('User without pengurus tried to access admin route', [
                    'user_id' => $user->id,
                    'user_nik' => $user->nik,
                    'user_name' => $user->name,
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl()
                ]);
                
                return redirect()->route('dashboard')
                               ->with('error', 'Anda belum terdaftar sebagai pengurus SEKAR. Silakan hubungi administrator.');
            }

            $userData = $adminData[0];
            $adminRoles = ['ADM', 'ADMIN_DPP', 'ADMIN_DPW', 'ADMIN_DPD'];
            
            if (!in_array($userData->role_name, $adminRoles)) {
                Log::warning('Non-admin user attempted to access admin route', [
                    'user_id' => $user->id,
                    'user_nik' => $user->nik,
                    'user_name' => $user->name,
                    'user_role' => $userData->role_name,
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl()
                ]);
                
                return redirect()->route('dashboard')
                               ->with('error', 'Akses ditolak. Anda tidak memiliki hak akses admin.');
            }

            // Log successful admin access
            Log::info('Admin access granted', [
                'user_id' => $user->id,
                'user_nik' => $user->nik,
                'user_name' => $user->name,
                'user_role' => $userData->role_name,
                'ip' => $request->ip(),
                'url' => $request->fullUrl()
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking admin status', [
                'user_id' => $user->id,
                'user_nik' => $user->nik,
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
                'url' => $request->fullUrl()
            ]);
            
            return redirect()->route('dashboard')
                           ->with('error', 'Terjadi kesalahan saat memverifikasi akses admin. Silakan coba lagi.');
        }

        return $next($request);
    }
}