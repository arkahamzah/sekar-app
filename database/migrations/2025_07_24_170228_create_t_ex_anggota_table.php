<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_ex_anggota', function (Blueprint $table) {
            $table->id('ID');
            $table->string('N_NIK', 60)->nullable();
            $table->string('V_NAMA_KARYAWAN', 100)->nullable();
            $table->string('V_SHORT_POSISI', 150)->nullable();
            $table->string('V_SHORT_DIVISI', 150)->nullable();
            $table->datetime('TGL_KELUAR')->nullable();
            $table->string('DPP', 50)->nullable();
            $table->string('DPW', 50)->nullable();
            $table->string('DPD', 50)->nullable();
            $table->string('V_KOTA_GEDUNG', 100)->nullable();
            $table->string('NO_TELP', 20)->nullable();
            $table->string('CREATED_BY', 20)->nullable();
            $table->datetime('CREATED_AT')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_ex_anggota');
    }
};