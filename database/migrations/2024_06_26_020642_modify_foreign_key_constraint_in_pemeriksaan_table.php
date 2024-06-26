<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('pemeriksaan', function (Blueprint $table) {
            $table->dropForeign(['no_laboratorium']);
        });

        Schema::table('pemeriksaan', function (Blueprint $table) {
            $table->foreign('no_laboratorium')
                ->references('no_laboratorium')
                ->on('registrasi')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        Schema::enableForeignKeyConstraints();
    }
};