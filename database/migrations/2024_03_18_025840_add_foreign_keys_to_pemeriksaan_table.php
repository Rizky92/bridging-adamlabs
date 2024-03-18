<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToPemeriksaanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pemeriksaan', function (Blueprint $table) {
            $table->foreignId('kategori_pemeriksaan_id')->after('status_bridging')->constrained('kategori_pemeriksaan');
            $table->foreignId('hasil_id')->after('kategori_pemeriksaan_id')->constrained('hasil');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pemeriksaan', function (Blueprint $table) {
            $table->dropForeign(['kategori_pemeriksaan_id']);
            $table->dropForeign(['hasil_id']);
            $table->dropColumn(['kategori_pemeriksaan_id']);
            $table->dropColumn(['hasil_id']);
        });
    }
}
