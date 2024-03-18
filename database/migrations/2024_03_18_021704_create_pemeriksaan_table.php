<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePemeriksaanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pemeriksaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registrasi_id')->constrained('registrasi');
            $table->integer('nomor_urut');
            $table->string('kode_tindakan_simrs');
            $table->string('kode_pemeriksaan_lis');
            $table->string('nama_pemeriksaan_lis');
            $table->string('metode');
            $table->dateTime('waktu_pemeriksaan');
            $table->boolean('status_bridging');
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
        Schema::dropIfExists('pemeriksaan');
    }
}
