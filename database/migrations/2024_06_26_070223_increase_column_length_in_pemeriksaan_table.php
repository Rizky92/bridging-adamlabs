<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemeriksaan', function (Blueprint $table) {
            $table->string('hasil_nilai_hasil', 500)->change();
            $table->string('hasil_nilai_rujukan', 500)->change();
        });
    }
};
