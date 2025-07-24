
<?php
// database/migrations/2024_01_01_000007_create_t_sekar_jajaran_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_sekar_jajaran', function (Blueprint $table) {
            $table->id('ID');
            $table->string('N_NIK', 50)->nullable();
            $table->string('V_NAMA_KARYAWAN', 150)->nullable();
            $table->string('ID_JAJARAN')->nullable();
            $table->datetime('START_DATE')->nullable();
            $table->datetime('END_DATE')->nullable();
            $table->char('CREATED_BY', 30)->nullable();
            $table->datetime('CREATED_AT')->nullable();
            $table->string('IS_AKTIF', 2)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_sekar_jajaran');
    }
};