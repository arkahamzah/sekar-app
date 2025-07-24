<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konsultasi extends Model
{
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
        'CLOSED_AT' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'N_NIK', 'nik');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'N_NIK', 'N_NIK');
    }

    public function komentar()
    {
        return $this->hasMany(KonsultasiKomentar::class, 'ID_KONSULTASI', 'ID')
                    ->orderBy('CREATED_AT', 'asc');
    }

    public function getStatusColorAttribute()
    {
        return match($this->STATUS) {
            'OPEN' => 'yellow',
            'IN_PROGRESS' => 'blue', 
            'CLOSED' => 'green',
            default => 'gray'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->STATUS) {
            'OPEN' => 'Menunggu',
            'IN_PROGRESS' => 'Diproses',
            'CLOSED' => 'Selesai',
            default => 'Unknown'
        };
    }
}