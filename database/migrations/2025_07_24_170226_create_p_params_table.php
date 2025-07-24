<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('p_params', function (Blueprint $table) {
            $table->id('ID');
            $table->string('NOMINAL_IURAN_WAJIB', 50)->nullable();
            $table->string('NOMINAL_BANPERS', 50)->nullable();
            $table->string('CREATED_BY')->nullable();
            $table->datetime('CREATED_AT')->nullable();
            $table->string('TAHUN', 4)->nullable();
            $table->char('IS_AKTIF', 2)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('p_params');
    }
};