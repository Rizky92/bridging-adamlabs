<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasienTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pasien', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pasien');
            $table->string('no_rm');
            $table->string('jenis_kelamin');
            $table->string('alamat');
            $table->string('no_telphone');
            $table->date('tanggal_lahir');
            $table->string('nik');
            $table->string('ras');
            $table->string('berat_badan');
            $table->string('jenis_registrasi');
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
        Schema::dropIfExists('pasien');
    }
}
