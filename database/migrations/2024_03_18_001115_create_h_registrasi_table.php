<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHRegistrasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h_registrasi', function (Blueprint $table) {
            $table->id();
            $table->string('no_lab');
            $table->string('no_reg_rs');
            $table->text('diagnosa_awal');
            $table->text('keterangan_klinis')->nullable();
            $table->text('expertise')->nullable();
            $table->dateTime('waktu_expertise')->nullable();
            $table->dateTime('waktu_terbit')->nullable();
            $table->dateTime('waktu_registrasi');
            $table->bigInteger('total_bayar')->nullable();
            $table->string('pasien_no_rm');
            $table->string('pasien_nama');
            $table->string('pasien_jenis_kelamin');
            $table->string('pasien_tanggal_lahir');
            $table->string('pasien_alamat');
            $table->string('pasien_no_telphone');
            $table->integer('pasien_umur_hari');
            $table->integer('pasien_umur_bulan');
            $table->integer('pasien_umur_tahun');
            $table->string('dokter_pengirim_nama');
            $table->string('dokter_pengirim_kode');
            $table->string('dokter_pengirim_alamat')->nullable();
            $table->string('dokter_pengirim_no_telphone')->nullable();
            $table->string('dokter_pengirim_spesialis_nama')->nullable();
            $table->string('dokter_pengirim_spesialis_kode')->nullable();
            $table->string('unit_asal_nama');
            $table->string('unit_asal_kode');
            $table->string('unit_asal_kelas');
            $table->string('unit_asal_keterangan')->nullable();
            $table->string('unit_asal_jenis_nama')->nullable();
            $table->string('unit_asal_jenis_kode')->nullable();
            $table->string('penjamin_nama');
            $table->string('penjamin_kode');
            $table->string('penjamin_jenis_nama')->nullable();
            $table->string('penjamin_jenis_kode')->nullable();
            $table->string('pasien_nik');
            $table->string('status_lis_simrs');
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
        Schema::dropIfExists('h_registrasi');
    }
}
