<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_iuran_history', function (Blueprint $table) {
            $table->id('ID');
            $table->string('N_NIK', 30);
            $table->string('JENIS', 20); // 'WAJIB' atau 'SUKARELA'
            $table->string('NOMINAL_LAMA', 20)->nullable();
            $table->string('NOMINAL_BARU', 20);
            $table->string('STATUS_PROSES', 20)->default('PENDING'); // PENDING, PROCESSED, IMPLEMENTED
            $table->datetime('TGL_PERUBAHAN');
            $table->datetime('TGL_PROSES')->nullable(); // Tanggal diproses HC (n+1 bulan)
            $table->datetime('TGL_IMPLEMENTASI')->nullable(); // Tanggal terimplementasi (n+2 bulan)
            $table->string('KETERANGAN')->nullable();
            $table->string('CREATED_BY', 30);
            $table->datetime('CREATED_AT');
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_iuran_history');
    }
};