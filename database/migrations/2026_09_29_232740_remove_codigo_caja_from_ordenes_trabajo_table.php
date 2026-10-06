<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordenes_trabajo', function (Blueprint $table) {

            $table->dropColumn('codigo_caja');

        });
    }


    public function down(): void
    {
        Schema::table('ordenes_trabajo', function (Blueprint $table) {

            $table->string('codigo_caja', 20)
                ->nullable()
                ->after('codigo');

        });
    }
};