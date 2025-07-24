<?php
// database/migrations/2024_01_01_000001_create_m_jajaran_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('m_jajaran', function (Blueprint $table) {
            $table->id('ID');
            $table->string('NAMA_JAJARAN')->nullable();
            $table->string('IS_AKTIF')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_jajaran');
    }
};