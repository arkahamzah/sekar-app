<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonsultasiKomentar extends Model
{
    protected $table = 't_konsultasi_komentar';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'ID_KONSULTASI',
        'N_NIK',
        'KOMENTAR',
        'PENGIRIM_ROLE',
        'CREATED_AT',
        'CREATED_BY'
    ];

    protected $casts = [
        'CREATED_AT' => 'datetime'
    ];

    public function konsultasi()
    {
        return $this->belongsTo(Konsultasi::class, 'ID_KONSULTASI', 'ID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'N_NIK', 'nik');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'N_NIK', 'N_NIK');
    }
}