<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class Konsultasi extends Model
{
    use HasFactory;

    protected $table = 't_konsultasi';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'N_NIK',
        'JENIS',
        'KATEGORI_ADVOKASI',
        'TUJUAN',
        'TUJUAN_SPESIFIK',
        'JUDUL',
        'DESKRIPSI',
        'STATUS',
        'CREATED_BY',
        'CREATED_AT',
        'UPDATED_BY',
        'UPDATED_AT',
        'CLOSED_BY',
        'CLOSED_AT'
    ];

    protected $casts = [
        'CREATED_AT' => 'datetime',
        'UPDATED_AT' => 'datetime',
        'CLOSED_AT' => 'datetime',
    ];

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($konsultasi) {
            if (!$konsultasi->CREATED_AT) {
                $konsultasi->CREATED_AT = now();
            }
            if (!$konsultasi->STATUS) {
                $konsultasi->STATUS = 'OPEN';
            }
        });

        static::updating(function ($konsultasi) {
            $konsultasi->UPDATED_AT = now();
        });

        // Clear cache when konsultasi is modified
        static::saved(function ($konsultasi) {
            Cache::forget('konsultasi_stats');
            Cache::forget('konsultasi_stats_user_' . $konsultasi->N_NIK);
        });

        static::deleted(function ($konsultasi) {
            Cache::forget('konsultasi_stats');
            Cache::forget('konsultasi_stats_user_' . $konsultasi->N_NIK);
        });
    }

    /**
     * Relationship with Karyawan
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'N_NIK', 'N_NIK');
    }

    /**
     * Relationship with comments
     */
    public function komentar(): HasMany
    {
        return $this->hasMany(KonsultasiKomentar::class, 'ID_KONSULTASI', 'ID')
                    ->orderBy('CREATED_AT', 'asc');
    }

    /**
     * Relationship with latest comment
     */
    public function latestKomentar(): BelongsTo
    {
        return $this->belongsTo(KonsultasiKomentar::class, 'ID', 'ID_KONSULTASI')
                    ->latest('CREATED_AT');
    }

    /**
     * Relationship with creator
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'CREATED_BY', 'N_NIK');
    }

    /**
     * Relationship with updater
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'UPDATED_BY', 'N_NIK');
    }

    /**
     * Relationship with closer
     */
    public function closer(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'CLOSED_BY', 'N_NIK');
    }

    /**
     * Get status text attribute
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->STATUS) {
            'OPEN' => 'Menunggu',
            'IN_PROGRESS' => 'Diproses',
            'CLOSED' => 'Selesai',
            'ESCALATED' => 'Dieskalasi',
            default => 'Unknown'
        };
    }

    /**
     * Get status color attribute for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->STATUS) {
            'OPEN' => 'yellow',
            'IN_PROGRESS' => 'blue',
            'CLOSED' => 'green',
            'ESCALATED' => 'orange',
            default => 'gray'
        };
    }

    /**
     * Get jenis badge color
     */
    public function getJenisBadgeColorAttribute(): string
    {
        return match($this->JENIS) {
            'ADVOKASI' => 'red',
            'ASPIRASI' => 'blue',
            default => 'gray'
        };
    }

    /**
     * Get days since creation
     */
    public function getDaysSinceCreatedAttribute(): int
    {
        return $this->CREATED_AT->diffInDays(now());
    }

    /**
     * Get priority based on age and type
     */
    public function getPriorityAttribute(): string
    {
        $daysSince = $this->days_since_created;
        $isAdvokasi = $this->JENIS === 'ADVOKASI';

        if ($daysSince > 7 || $isAdvokasi) {
            return 'high';
        } elseif ($daysSince > 3) {
            return 'medium';
        } else {
            return 'normal';
        }
    }

    /**
     * Get target display name
     */
    public function getTargetDisplayAttribute(): string
    {
        $target = $this->TUJUAN;
        if ($this->TUJUAN_SPESIFIK) {
            $target .= ' - ' . $this->TUJUAN_SPESIFIK;
        }
        return $target;
    }

    /**
     * Check if konsultasi is overdue (more than 7 days without update)
     */
    public function getIsOverdueAttribute(): bool
    {
        if ($this->STATUS === 'CLOSED') {
            return false;
        }

        $lastUpdate = $this->UPDATED_AT ?? $this->CREATED_AT;
        return $lastUpdate->diffInDays(now()) > 7;
    }

    /**
     * Check if konsultasi needs attention (advokasi or overdue)
     */
    public function getNeedsAttentionAttribute(): bool
    {
        return $this->JENIS === 'ADVOKASI' || $this->is_overdue;
    }

    /**
     * Get human readable time difference
     */
    public function getHumanTimeAttribute(): string
    {
        return $this->CREATED_AT->diffForHumans();
    }

    /**
     * Get response time if closed
     */
    public function getResponseTimeAttribute(): ?string
    {
        if (!$this->CLOSED_AT) {
            return null;
        }

        $diff = $this->CREATED_AT->diffInDays($this->CLOSED_AT);
        
        if ($diff === 0) {
            return 'Kurang dari 1 hari';
        } elseif ($diff === 1) {
            return '1 hari';
        } else {
            return "{$diff} hari";
        }
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('STATUS', $status);
    }

    /**
     * Scope for filtering by jenis
     */
    public function scopeByJenis($query, string $jenis)
    {
        return $query->where('JENIS', $jenis);
    }

    /**
     * Scope for filtering by tujuan
     */
    public function scopeByTujuan($query, string $tujuan)
    {
        return $query->where('TUJUAN', $tujuan);
    }

    /**
     * Scope for user's konsultasi
     */
    public function scopeForUser($query, string $nik)
    {
        return $query->where('N_NIK', $nik);
    }

    /**
     * Scope for recent konsultasi
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('CREATED_AT', '>=', now()->subDays($days));
    }

    /**
     * Scope for overdue konsultasi
     */
    public function scopeOverdue($query)
    {
        return $query->where('STATUS', '!=', 'CLOSED')
                    ->where(function($q) {
                        $q->where('UPDATED_AT', '<=', now()->subDays(7))
                          ->orWhere(function($q2) {
                              $q2->whereNull('UPDATED_AT')
                                 ->where('CREATED_AT', '<=', now()->subDays(7));
                          });
                    });
    }

    /**
     * Scope for high priority konsultasi
     */
    public function scopeHighPriority($query)
    {
        return $query->where(function($q) {
            $q->where('JENIS', 'ADVOKASI')
              ->orWhere('CREATED_AT', '<=', now()->subDays(7));
        })->where('STATUS', '!=', 'CLOSED');
    }

    /**
     * Get statistics for dashboard
     */
    public static function getStats(): array
    {
        return Cache::remember('konsultasi_stats', 300, function () {
            return [
                'total' => static::count(),
                'open' => static::byStatus('OPEN')->count(),
                'in_progress' => static::byStatus('IN_PROGRESS')->count(),
                'closed' => static::byStatus('CLOSED')->count(),
                'advokasi' => static::byJenis('ADVOKASI')->count(),
                'aspirasi' => static::byJenis('ASPIRASI')->count(),
                'overdue' => static::overdue()->count(),
                'high_priority' => static::highPriority()->count(),
                'recent' => static::recent(7)->count(),
                'avg_response_time' => static::getAverageResponseTime()
            ];
        });
    }

    /**
     * Get statistics for specific user
     */
    public static function getUserStats(string $nik): array
    {
        return Cache::remember("konsultasi_stats_user_{$nik}", 300, function () use ($nik) {
            return [
                'total' => static::forUser($nik)->count(),
                'open' => static::forUser($nik)->byStatus('OPEN')->count(),
                'in_progress' => static::forUser($nik)->byStatus('IN_PROGRESS')->count(),
                'closed' => static::forUser($nik)->byStatus('CLOSED')->count(),
                'advokasi' => static::forUser($nik)->byJenis('ADVOKASI')->count(),
                'aspirasi' => static::forUser($nik)->byJenis('ASPIRASI')->count(),
                'recent' => static::forUser($nik)->recent(30)->count()
            ];
        });
    }

    /**
     * Get average response time in days
     */
    public static function getAverageResponseTime(): float
    {
        $closed = static::whereNotNull('CLOSED_AT')->get();
        
        if ($closed->isEmpty()) {
            return 0;
        }

        $totalDays = $closed->sum(function ($konsultasi) {
            return $konsultasi->CREATED_AT->diffInDays($konsultasi->CLOSED_AT);
        });

        return round($totalDays / $closed->count(), 1);
    }

    /**
     * Get konsultasi by location for admin targeting
     */
    public static function getByLocation(string $location): \Illuminate\Database\Eloquent\Collection
    {
        return static::join('t_karyawan', 't_konsultasi.N_NIK', '=', 't_karyawan.N_NIK')
                    ->where('t_karyawan.V_KOTA_GEDUNG', $location)
                    ->select('t_konsultasi.*')
                    ->with(['karyawan', 'komentar'])
                    ->orderBy('t_konsultasi.CREATED_AT', 'desc')
                    ->get();
    }

    /**
     * Search konsultasi by keywords
     */
    public static function search(string $keywords, string $nik = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = static::where(function ($q) use ($keywords) {
            $q->where('JUDUL', 'LIKE', "%{$keywords}%")
              ->orWhere('DESKRIPSI', 'LIKE', "%{$keywords}%")
              ->orWhere('KATEGORI_ADVOKASI', 'LIKE', "%{$keywords}%");
        });

        if ($nik) {
            $query->where('N_NIK', $nik);
        }

        return $query->with(['karyawan', 'komentar'])
                    ->orderBy('CREATED_AT', 'desc')
                    ->get();
    }

    /**
     * Close konsultasi
     */
    public function close(string $closedBy): bool
    {
        return $this->update([
            'STATUS' => 'CLOSED',
            'CLOSED_BY' => $closedBy,
            'CLOSED_AT' => now(),
            'UPDATED_BY' => $closedBy,
            'UPDATED_AT' => now()
        ]);
    }

    /**
     * Escalate konsultasi to higher level
     */
    public function escalate(string $newTarget, string $escalatedBy): bool
    {
        return $this->update([
            'TUJUAN' => $newTarget,
            'STATUS' => 'OPEN', // Reset to open for higher level
            'UPDATED_BY' => $escalatedBy,
            'UPDATED_AT' => now()
        ]);
    }

    /**
     * Mark as in progress
     */
    public function markInProgress(string $updatedBy): bool
    {
        if ($this->STATUS === 'OPEN') {
            return $this->update([
                'STATUS' => 'IN_PROGRESS',
                'UPDATED_BY' => $updatedBy,
                'UPDATED_AT' => now()
            ]);
        }
        return false;
    }
}