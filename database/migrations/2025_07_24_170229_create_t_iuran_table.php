<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_iuran', function (Blueprint $table) {
            $table->id('ID');
            $table->string('N_NIK', 30)->nullable();
            $table->string('IURAN_WAJIB', 20)->nullable();
            $table->string('IURAN_SUKARELA', 20)->nullable();
            $table->string('CREATED_BY', 30)->nullable();
            $table->datetime('CREATED_AT')->nullable();
            $table->string('UPDATE_BY', 30)->nullable();
            $table->datetime('UPDATED_AT')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_iuran');
    }
};