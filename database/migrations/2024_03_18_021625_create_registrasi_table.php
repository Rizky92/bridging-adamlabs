<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registrasi', function (Blueprint $table) {
            $table->id();
            $table->string('no_registrasi');
            $table->string('no_laboratorium');
            $table->dateTime('waktu_registrasi');
            $table->string('diagnosa_awal');
            $table->string('kode_RS');
            $table->string('kode_lab');
            $table->integer('umur_tahun');
            $table->integer('umur_bulan');
            $table->integer('umur_hari');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registrasi');
    }
}
