<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_karyawan', function (Blueprint $table) {
            $table->id('ID');
            $table->string('N_NIK', 30)->nullable();
            $table->string('V_NAMA_KARYAWAN', 150)->nullable();
            $table->string('V_SHORT_UNIT', 150)->nullable();
            $table->string('V_SHORT_POSISI', 150)->nullable();
            $table->string('C_KODE_POSISI', 60)->nullable();
            $table->string('C_KODE_UNIT', 60)->nullable();
            $table->string('V_SHORT_DIVISI', 150)->nullable();
            $table->string('V_BAND_POSISI', 4)->nullable();
            $table->string('C_KODE_DIVISI', 50)->nullable();
            $table->string('C_PERSONNEL_AREA', 100)->nullable();
            $table->string('C_PERSONNEL_SUB_AREA', 100)->nullable();
            $table->string('V_KOTA_GEDUNG', 100)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_karyawan');
    }
};