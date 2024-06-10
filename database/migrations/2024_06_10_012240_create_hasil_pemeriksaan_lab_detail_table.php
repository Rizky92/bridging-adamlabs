<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hasil_pemeriksaan_lab_detail', function (Blueprint $table) {
            $table->id();
            $table->string('no_laboratorium');
            $table->string('nama_kategori_pemeriksaan');
            $table->integer('urut_kategori_pemeriksaan');
            $table->string('nama_subkategori_pemeriksaan');
            $table->integer('urut_subkategori_pemeriksaan');
            $table->integer('urut');
            $table->string('kode_tindakan_simrs');
            $table->string('kode_pemeriksaan_lis');
            $table->string('nama_pemeriksaan_lis');
            $table->string('metode');
            $table->dateTime('waktu_pemeriksaan');
            $table->boolean('status_bridging');
            $table->string('hasil_satuan');
            $table->string('hasil_nilai_hasil');
            $table->string('hasil_nilai_rujukan');
            $table->string('flag_kode');

            $table->foreign('no_laboratorium')
                ->references('no_laboratorium')
                ->on('hasil_pemeriksaan_lab');
        });
    }
};
