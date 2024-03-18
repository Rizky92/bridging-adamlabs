<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHItemPemeriksaanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h_item_pemeriksaan', function (Blueprint $table) {
            $table->id();
            $table->string('h_registrasi_no_lab')->constrained('h_registrasi.no_lab');
            $table->dateTime('waktu_pemeriksaan_di_isi');
            $table->dateTime('waktu_verifikasi')->nullable();
            $table->string('hasil_pemeriksaan');
            $table->string('keterangan', 500)->nullable();
            $table->string('nilai_rujukan_tampilan_nilai_rujukan')->nullable();
            $table->string('item_pemeriksaan_kode');
            $table->string('item_pemeriksaan_nama');
            $table->string('item_pemeriksaan_satuan')->nullable();
            $table->string('item_pemeriksaan_metode');
            $table->integer('item_pemeriksaan_no_urut');
            $table->string('item_pemeriksaan_jenis_input')->nullable();
            $table->tinyInteger('item_pemeriksaan_is_tampilkan_waktu_periksa')->nullable();
            $table->string('kategori_pemeriksaan_nama');
            $table->string('kategori_pemeriksaan_kode')->nullable();
            $table->integer('kategori_pemeriksaan_no_urut');
            $table->string('sub_kategori_pemeriksaan_nama');
            $table->string('sub_kategori_pemeriksaan_kode')->nullable();
            $table->integer('sub_kategori_pemeriksaan_no_urut');
            $table->string('flag_nama')->nullable();
            $table->string('flag_kode');
            $table->string('flag_warna')->nullable();
            $table->string('flag_jenis_pewarnaan')->nullable();
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
        Schema::dropIfExists('h_item_pemeriksaan');
    }
}
