<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_konsultasi_komentar', function (Blueprint $table) {
            $table->id('ID');
            $table->unsignedBigInteger('ID_KONSULTASI');
            $table->string('N_NIK', 30);
            $table->text('KOMENTAR');
            $table->enum('PENGIRIM_ROLE', ['USER', 'ADMIN'])->default('USER');
            $table->datetime('CREATED_AT');
            $table->string('CREATED_BY', 30);
            
            $table->foreign('ID_KONSULTASI')->references('ID')->on('t_konsultasi')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_konsultasi_komentar');
    }
};