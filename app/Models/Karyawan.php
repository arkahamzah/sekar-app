<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 't_karyawan';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'N_NIK',
        'NAMA',
        'UNIT_KERJA',
        'JABATAN',
        'KODE_JABATAN',
        'KODE_UNIT',
        'NAMA_DIVISI',
        'BAND_JABATAN',
        'KODE_DIVISI',
        'AREA_PERSONIL',
        'SUB_AREA_PERSONIL',
        'LOKASI_KERJA',
        'NO_HP'
    ];

    // Accessor untuk backward compatibility dengan field yang mungkin berbeda
    public function getNamaAttribute($value)
    {
        return $value ?? $this->attributes['V_NAMA_KARYAWAN'] ?? null;
    }

    public function getUnitKerjaAttribute($value)
    {
        return $value ?? $this->attributes['V_SHORT_UNIT'] ?? null;
    }

    public function getJabatanAttribute($value)
    {
        return $value ?? $this->attributes['V_SHORT_POSISI'] ?? null;
    }

    public function getLokasiKerjaAttribute($value)
    {
        return $value ?? $this->attributes['V_KOTA_GEDUNG'] ?? null;
    }

    public function getNamaDivisiAttribute($value)
    {
        return $value ?? $this->attributes['V_SHORT_DIVISI'] ?? null;
    }

    public function getBandJabatanAttribute($value)
    {
        return $value ?? $this->attributes['V_BAND_POSISI'] ?? null;
    }

    /**
     * Get the pengurus record associated with the karyawan.
     */
    public function pengurus()
    {
        return $this->hasOne(SekarPengurus::class, 'N_NIK', 'N_NIK');
    }

    /**
     * Get the user record associated with the karyawan.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'nik', 'N_NIK');
    }

    /**
     * Get konsultasi submitted by this karyawan
     */
    public function konsultasi()
    {
        return $this->hasMany(Konsultasi::class, 'N_NIK', 'N_NIK');
    }

    /**
     * Get komentar made by this karyawan
     */
    public function komentar()
    {
        return $this->hasMany(KonsultasiKomentar::class, 'N_NIK', 'N_NIK');
    }

    /**
     * Get DPW based on location
     */
    public function getDPW(): string
    {
        $lokasi = $this->LOKASI_KERJA ?? $this->V_KOTA_GEDUNG ?? '';
        
        return match(true) {
            str_contains(strtoupper($lokasi), 'JAKARTA') => 'DPW Jakarta',
            str_contains(strtoupper($lokasi), 'BANDUNG') => 'DPW Jabar', 
            str_contains(strtoupper($lokasi), 'SURABAYA') => 'DPW Jatim',
            str_contains(strtoupper($lokasi), 'MEDAN') => 'DPW Sumut',
            str_contains(strtoupper($lokasi), 'MAKASSAR') => 'DPW Sulsel',
            str_contains(strtoupper($lokasi), 'DENPASAR') => 'DPW Bali',
            default => 'DPW Lainnya'
        };
    }

    /**
     * Get DPD based on location
     */
    public function getDPD(): string
    {
        $lokasi = $this->LOKASI_KERJA ?? $this->V_KOTA_GEDUNG ?? '';
        return "DPD {$lokasi}";
    }

    /**
     * Check if karyawan is in specific region
     */
    public function isInRegion(string $region): bool
    {
        $lokasi = strtoupper($this->LOKASI_KERJA ?? $this->V_KOTA_GEDUNG ?? '');
        $region = strtoupper($region);
        
        return str_contains($lokasi, $region);
    }

    /**
     * Scope to filter by location
     */
    public function scopeByLocation($query, string $location)
    {
        return $query->where(function($q) use ($location) {
            $q->where('LOKASI_KERJA', 'LIKE', "%{$location}%")
              ->orWhere('V_KOTA_GEDUNG', 'LIKE', "%{$location}%");
        });
    }

    /**
     * Scope to filter by division
     */
    public function scopeByDivision($query, string $division)
    {
        return $query->where(function($q) use ($division) {
            $q->where('NAMA_DIVISI', 'LIKE', "%{$division}%")
              ->orWhere('V_SHORT_DIVISI', 'LIKE', "%{$division}%");
        });
    }

    /**
     * Get formatted employee info
     */
    public function getFormattedInfoAttribute(): array
    {
        return [
            'nik' => $this->N_NIK,
            'nama' => $this->NAMA ?? $this->V_NAMA_KARYAWAN,
            'jabatan' => $this->JABATAN ?? $this->V_SHORT_POSISI,
            'unit_kerja' => $this->UNIT_KERJA ?? $this->V_SHORT_UNIT,
            'divisi' => $this->NAMA_DIVISI ?? $this->V_SHORT_DIVISI,
            'lokasi' => $this->LOKASI_KERJA ?? $this->V_KOTA_GEDUNG,
            'band' => $this->BAND_JABATAN ?? $this->V_BAND_POSISI,
            'no_hp' => $this->NO_HP,
            'dpw' => $this->getDPW(),
            'dpd' => $this->getDPD()
        ];
    }
}