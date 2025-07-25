<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            Log::warning('Unauthenticated user attempted to access admin route', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method()
            ]);
            
            return redirect()->route('login')
                           ->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        }
        
        // Check if user has pengurus relationship
        if (!$user->pengurus) {
            Log::warning('Non-pengurus user attempted to access admin route', [
                'user_id' => $user->id,
                'user_nik' => $user->nik,
                'user_name' => $user->name,
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method()
            ]);
            
            return redirect()->route('dashboard')
                           ->with('error', 'Akses ditolak. Anda tidak terdaftar sebagai pengurus SEKAR.');
        }
        
        // Check if pengurus has role
        if (!$user->pengurus->role) {
            Log::warning('Pengurus without role attempted to access admin route', [
                'user_id' => $user->id,
                'user_nik' => $user->nik,
                'user_name' => $user->name,
                'pengurus_id' => $user->pengurus->ID,
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method()
            ]);
            
            return redirect()->route('dashboard')
                           ->with('error', 'Akses ditolak. Role pengurus Anda belum ditetapkan. Silakan hubungi administrator.');
        }
        
        // Check if user has admin role
        $adminRoles = $this->getAdminRoles();
        $userRole = $user->pengurus->role->NAME;
        
        if (!in_array($userRole, $adminRoles)) {
            Log::warning('Non-admin user attempted to access admin route', [
                'user_id' => $user->id,
                'user_nik' => $user->nik,
                'user_name' => $user->name,
                'user_role' => $userRole,
                'required_roles' => $adminRoles,
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method()
            ]);
            
            // Provide specific error message based on role
            $errorMessage = $this->getErrorMessageForRole($userRole);
            
            return redirect()->route('dashboard')
                           ->with('error', $errorMessage);
        }
        
        // Log successful admin access
        Log::info('Admin user accessed admin route', [
            'user_id' => $user->id,
            'user_nik' => $user->nik,
            'user_name' => $user->name,
            'user_role' => $userRole,
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
            'method' => $request->method()
        ]);
        
        // Add admin context to request for use in controllers
        $request->merge([
            'admin_user' => $user,
            'admin_role' => $userRole,
            'admin_level' => $this->getAdminLevel($userRole)
        ]);
        
        return $next($request);
    }
    
    /**
     * Get list of admin roles
     */
    private function getAdminRoles(): array
    {
        return [
            'ADM',           // Super Administrator
            'ADMIN_DPP',     // Admin Dewan Pengurus Pusat
            'ADMIN_DPW',     // Admin Dewan Pengurus Wilayah
            'ADMIN_DPD'      // Admin Dewan Pengurus Daerah
        ];
    }
    
    /**
     * Get admin level for role hierarchy
     */
    private function getAdminLevel(string $role): int
    {
        return match($role) {
            'ADM' => 4,           // Highest level
            'ADMIN_DPP' => 3,     // National level
            'ADMIN_DPW' => 2,     // Regional level
            'ADMIN_DPD' => 1,     // Local level
            default => 0          // No admin access
        };
    }
    
    /**
     * Get specific error message based on user role
     */
    private function getErrorMessageForRole(?string $role): string
    {
        if (!$role) {
            return 'Akses ditolak. Anda belum memiliki role yang ditetapkan.';
        }
        
        return match($role) {
            'ANGGOTA' => 'Akses ditolak. Fitur ini hanya tersedia untuk pengurus SEKAR.',
            'PENGURUS' => 'Akses ditolak. Anda memerlukan role admin untuk mengakses fitur ini.',
            default => 'Akses ditolak. Role Anda (' . $role . ') tidak memiliki hak akses admin.'
        };
    }
    
    /**
     * Check if user can access specific admin level
     */
    public static function canAccessLevel(int $requiredLevel): bool
    {
        $user = Auth::user();
        
        if (!$user || !$user->pengurus || !$user->pengurus->role) {
            return false;
        }
        
        $middleware = new static();
        $userLevel = $middleware->getAdminLevel($user->pengurus->role->NAME);
        
        return $userLevel >= $requiredLevel;
    }
    
    /**
     * Check if user has specific admin role
     */
    public static function hasRole(string $role): bool
    {
        $user = Auth::user();
        
        if (!$user || !$user->pengurus || !$user->pengurus->role) {
            return false;
        }
        
        return $user->pengurus->role->NAME === $role;
    }
    
    /**
     * Check if user is super admin
     */
    public static function isSuperAdmin(): bool
    {
        return static::hasRole('ADM');
    }
    
    /**
     * Get user's admin role
     */
    public static function getUserRole(): ?string
    {
        $user = Auth::user();
        
        if (!$user || !$user->pengurus || !$user->pengurus->role) {
            return null;
        }
        
        return $user->pengurus->role->NAME;
    }
    
    /**
     * Get user's admin permissions based on role
     */
    public static function getPermissions(): array
    {
        $role = static::getUserRole();
        
        if (!$role) {
            return [];
        }
        
        return match($role) {
            'ADM' => [
                'manage_all_users',
                'manage_all_roles',
                'manage_system_settings',
                'view_all_reports',
                'close_all_konsultasi',
                'escalate_all_konsultasi',
                'manage_banpers',
                'manage_certificates'
            ],
            'ADMIN_DPP' => [
                'manage_dpw_users',
                'manage_dpd_users',
                'view_national_reports',
                'close_dpp_konsultasi',
                'escalate_to_adm',
                'manage_national_policies'
            ],
            'ADMIN_DPW' => [
                'manage_dpd_users',
                'view_regional_reports',
                'close_dpw_konsultasi',
                'escalate_to_dpp',
                'manage_regional_policies'
            ],
            'ADMIN_DPD' => [
                'view_local_reports',
                'close_dpd_konsultasi',
                'escalate_to_dpw',
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
        $permissions = static::getPermissions();
        return in_array($permission, $permissions);
    }
}