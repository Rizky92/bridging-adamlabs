<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeySubKategoriPemeriksaanIdToPemeriksaanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pemeriksaan', function (Blueprint $table) {
            $table->foreignId('sub_kategori_pemeriksaan_id')->after('kategori_pemeriksaan_id')->constrained('sub_kategori_pemeriksaan');
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
            $table->dropForeign(['sub_kategori_pemeriksaan_id']);
            $table->dropColumn(['sub_kategori_pemeriksaan_id']);
        });
    }
}
