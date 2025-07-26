<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nik',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the pengurus record associated with the user.
     */
    public function pengurus()
    {
        return $this->hasOne(SekarPengurus::class, 'N_NIK', 'nik');
    }

    /**
     * Get the karyawan record associated with the user.
     */
    public function karyawan()
    {
        return $this->hasOne(Karyawan::class, 'N_NIK', 'nik');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->pengurus && 
               $this->pengurus->role && 
               in_array($this->pengurus->role->NAME, ['ADM', 'ADMIN_DPP', 'ADMIN_DPW', 'ADMIN_DPD']);
    }

    /**
     * Check if user has specific admin role
     */
    public function hasRole(string $role): bool
    {
        return $this->pengurus && 
               $this->pengurus->role && 
               $this->pengurus->role->NAME === $role;
    }

    /**
     * Get user admin level
     */
    public function getAdminLevel(): int
    {
        if (!$this->pengurus || !$this->pengurus->role) {
            return 0;
        }

        return match($this->pengurus->role->NAME) {
            'ADM' => 4,           // Super Administrator
            'ADMIN_DPP' => 3,     // National level
            'ADMIN_DPW' => 2,     // Regional level
            'ADMIN_DPD' => 1,     // Local level
            default => 0          // No admin access
        };
    }

    /**
     * Check if user can access admin level
     */
    public function canAccessAdminLevel(int $requiredLevel = 1): bool
    {
        return $this->getAdminLevel() >= $requiredLevel;
    }

    /**
     * Get user's admin role name
     */
    public function getAdminRole(): ?string
    {
        return $this->pengurus?->role?->NAME;
    }

    /**
     * Get user's DPW
     */
    public function getDPW(): ?string
    {
        if ($this->pengurus && $this->pengurus->DPW) {
            return $this->pengurus->DPW;
        }
        
        // Fallback to karyawan location mapping
        if ($this->karyawan) {
            $location = $this->karyawan->V_KOTA_GEDUNG ?? '';
            return match(true) {
                str_contains($location, 'JAKARTA') => 'DPW Jakarta',
                str_contains($location, 'BANDUNG') => 'DPW Jabar',
                str_contains($location, 'SURABAYA') => 'DPW Jatim',
                default => null
            };
        }
        
        return null;
    }

    /**
     * Get user's DPD
     */
    public function getDPD(): ?string
    {
        if ($this->pengurus && $this->pengurus->DPD) {
            return $this->pengurus->DPD;
        }
        
        // Fallback to karyawan location mapping
        if ($this->karyawan) {
            $location = $this->karyawan->V_KOTA_GEDUNG ?? '';
            return "DPD {$location}";
        }
        
        return null;
    }

    /**
     * Get user's admin permissions
     */
    public function getAdminPermissions(): array
    {
        $role = $this->getAdminRole();
        
        if (!$role) {
            return [];
        }

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
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->getAdminPermissions());
    }
}