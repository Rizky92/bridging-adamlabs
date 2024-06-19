<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrasi', function (Blueprint $table) {
            $table->dropColumn('pegawai');
        });

        Schema::table('registrasi', function (Blueprint $table) {
            $table->string('username', 25)->nullable();
            $table->string('nama_pegawai', 80)->nullable();
            $table->string('dokter_penanggung_jawab', 80)->nullable();
        });
    }
};
