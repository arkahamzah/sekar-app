<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_sekar_roles', function (Blueprint $table) {
            $table->id('ID');
            $table->string('NAME', 150)->nullable();
            $table->string('DESC')->nullable();
            $table->char('IS_AKTIF', 2)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_sekar_roles');
    }
};