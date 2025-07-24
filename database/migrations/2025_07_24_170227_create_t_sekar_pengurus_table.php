<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_sekar_pengurus', function (Blueprint $table) {
            $table->id('ID');
            $table->string('N_NIK', 30)->nullable();
            $table->string('V_SHORT_POSISI', 100)->nullable();
            $table->string('V_SHORT_UNIT', 100)->nullable();
            $table->string('CREATED_BY', 100)->nullable();
            $table->datetime('CREATED_AT')->nullable();
            $table->string('UPDATED_BY', 50)->nullable();
            $table->datetime('UPDATED_AT')->nullable();
            $table->string('DPP', 100)->nullable();
            $table->string('DPW', 100)->nullable();
            $table->string('DPD', 100)->nullable();
            $table->integer('ID_ROLES')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_sekar_pengurus');
    }
};
