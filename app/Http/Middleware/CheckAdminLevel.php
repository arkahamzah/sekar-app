<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckAdminLevel
{
    /**
     * Handle an incoming request.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $requiredLevel
     * @return mixed
     */
    public function handle(Request $request, Closure $next, int $requiredLevel = 1)
    {
        $user = Auth::user();

        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login')
                           ->with('error', 'Silakan login terlebih dahulu.');
        }

        // Check if user has pengurus relationship
        if (!$user->pengurus) {
            Log::warning('User without pengurus tried to access admin level route', [
                'user_id' => $user->id,
                'user_nik' => $user->nik,
                'required_level' => $requiredLevel
            ]);
            
            return redirect()->route('dashboard')
                           ->with('error', 'Anda belum terdaftar sebagai pengurus SEKAR.');
        }

        // Check if user has role
        if (!$user->pengurus->role) {
            Log::warning('User pengurus without role tried to access admin level route', [
                'user_id' => $user->id,
                'user_nik' => $user->nik,
                'required_level' => $requiredLevel
            ]);
            
            return redirect()->route('dashboard')
                           ->with('error', 'Role pengurus Anda belum ditetapkan.');
        }

        $userRole = $user->pengurus->role->NAME;
        $userLevel = $this->getAdminLevel($userRole);

        // Check if user has sufficient admin level
        if ($userLevel < $requiredLevel) {
            Log::warning('Insufficient admin level for route access', [
                'user_id' => $user->id,
                'user_nik' => $user->nik,
                'user_role' => $userRole,
                'user_level' => $userLevel,
                'required_level' => $requiredLevel,
                'ip' => $request->ip(),
                'url' => $request->fullUrl()
            ]);
            
            return redirect()->route('admin.konsultasi.index')
                           ->with('error', 'Anda tidak memiliki hak akses untuk fitur ini.');
        }

        // Log successful access
        Log::info('Admin level access granted', [
            'user_id' => $user->id,
            'user_nik' => $user->nik,
            'user_role' => $userRole,
            'user_level' => $userLevel,
            'required_level' => $requiredLevel,
            'url' => $request->fullUrl()
        ]);

        // Add admin level info to request
        $request->merge([
            'admin_level' => $userLevel,
            'admin_role' => $userRole,
            'admin_permissions' => $this->getPermissions($userRole)
        ]);

        return $next($request);
    }

    /**
     * Get admin level for role hierarchy
     */
    private function getAdminLevel(string $role): int
    {
        return match($role) {
            'ADM' => 4,           // Super Administrator - highest level
            'ADMIN_DPP' => 3,     // National level admin
            'ADMIN_DPW' => 2,     // Regional level admin  
            'ADMIN_DPD' => 1,     // Local level admin
            default => 0          // No admin access
        };
    }

    /**
     * Get permissions based on admin role
     */
    private function getPermissions(string $role): array
    {
        return match($role) {
            'ADM' => [
                'view_all_konsultasi',
                'manage_all_konsultasi',
                'close_all_konsultasi',
                'escalate_all_konsultasi',
                'manage_users',
                'manage_roles',
                'manage_system_settings',
                'view_all_reports',
                'export_all_data',
                'manage_banpers',
                'manage_certificates'
            ],
            'ADMIN_DPP' => [
                'view_dpp_konsultasi',
                'view_general_konsultasi',
                'manage_dpp_konsultasi',
                'close_dpp_konsultasi',
                'view_national_reports',
                'export_national_data',
                'manage_dpw_admins',
                'escalate_to_adm'
            ],
            'ADMIN_DPW' => [
                'view_dpw_konsultasi',
                'manage_dpw_konsultasi',
                'close_dpw_konsultasi',
                'escalate_to_dpp',
                'view_regional_reports',
                'export_regional_data',
                'manage_dpd_admins'
            ],
            'ADMIN_DPD' => [
                'view_dpd_konsultasi',
                'manage_dpd_konsultasi',
                'close_dpd_konsultasi',
                'escalate_to_dpw',
                'view_local_reports',
                'export_local_data',
                'manage_local_members'
            ],
            default => []
        };
    }

    /**
     * Check if user has specific permission
     */
    public static function hasPermission(string $permission): bool
    {
        $user = Auth::user();
        
        if (!$user || !$user->pengurus || !$user->pengurus->role) {
            return false;
        }
        
        $middleware = new static();
        $permissions = $middleware->getPermissions($user->pengurus->role->NAME);
        
        return in_array($permission, $permissions);
    }

    /**
     * Get user's current admin level
     */
    public static function getCurrentLevel(): int
    {
        $user = Auth::user();
        
        if (!$user || !$user->pengurus || !$user->pengurus->role) {
            return 0;
        }
        
        $middleware = new static();
        return $middleware->getAdminLevel($user->pengurus->role->NAME);
    }

    /**
     * Check if user can access specific level
     */
    public static function canAccessLevel(int $requiredLevel): bool
    {
        return static::getCurrentLevel() >= $requiredLevel;
    }
}