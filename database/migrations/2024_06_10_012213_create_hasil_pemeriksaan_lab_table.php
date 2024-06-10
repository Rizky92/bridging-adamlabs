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
        Schema::create('hasil_pemeriksaan_lab', function (Blueprint $table) {
            $table->string('no_registrasi');
            $table->string('no_laboratorium')->index();
            $table->dateTime('waktu_registrasi')->index();
            $table->string('diagnosa_awal');
            $table->string('kode_rs');
            $table->string('kode_lab');
            $table->integer('umur_tahun')->unsgined();
            $table->integer('umur_bulan')->unsigned();
            $table->integer('umur_hari')->unsigned();
            $table->string('nama_pasien')->index();
            $table->string('no_rm')->index();
            $table->string('jenis_kelamin');
            $table->string('alamat');
            $table->string('no_telphone')->index();
            $table->string('tanggal_lahir')->index();
            $table->string('nik')->index();
            $table->string('ras');
            $table->string('berat_badan');
            $table->string('jenis_registrasi')->index();
            $table->string('kode_dokter_pengirim')->index();
            $table->string('nama_dokter_pengirim')->index();
            $table->string('kode_unit_asal')->index();
            $table->string('nama_unit_asal')->index();
            $table->string('kode_penjamin')->index();
            $table->string('nama_penjamin')->index();

            $table->primary('no_laboratorium');
        });
    }
};
