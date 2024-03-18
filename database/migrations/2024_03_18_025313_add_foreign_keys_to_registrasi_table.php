<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToRegistrasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registrasi', function (Blueprint $table) {
            $table->foreignId('pasien_id')->after('umur_hari')->constrained('pasien');
            $table->foreignId('dokter_pengirim_id')->after('pasien_id')->constrained('dokter_pengirim');
            $table->foreignId('unit_asal_id')->after('dokter_pengirim_id')->constrained('unit_asal');
            $table->foreignId('penjamin_id')->after('unit_asal_id')->constrained('penjamin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registrasi', function (Blueprint $table) {
            $table->dropForeign(['pasien_id']);
            $table->dropForeign(['dokter_pengirim_id']);
            $table->dropForeign(['unit_asal_id']);
            $table->dropForeign(['penjamin_id']);
            $table->dropColumn(['pasien_id', 'dokter_pengirim_id', 'unit_asal_id', 'penjamin_id']);
        });
    }
}
