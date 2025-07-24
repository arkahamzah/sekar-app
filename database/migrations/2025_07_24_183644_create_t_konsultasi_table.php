<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_konsultasi', function (Blueprint $table) {
            $table->id('ID');
            $table->string('N_NIK', 30);
            $table->enum('JENIS', ['ADVOKASI', 'ASPIRASI']);
            $table->string('KATEGORI_ADVOKASI', 100)->nullable(); // For ADVOKASI type
            $table->enum('TUJUAN', ['DPP', 'DPW', 'DPD', 'GENERAL']);
            $table->string('TUJUAN_SPESIFIK', 100)->nullable(); // Specific DPW/DPD name
            $table->string('JUDUL', 200);
            $table->text('DESKRIPSI');
            $table->enum('STATUS', ['OPEN', 'IN_PROGRESS', 'CLOSED'])->default('OPEN');
            $table->string('CREATED_BY', 30);
            $table->datetime('CREATED_AT');
            $table->string('UPDATED_BY', 30)->nullable();
            $table->datetime('UPDATED_AT')->nullable();
            $table->string('CLOSED_BY', 30)->nullable();
            $table->datetime('CLOSED_AT')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_konsultasi');
    }
};