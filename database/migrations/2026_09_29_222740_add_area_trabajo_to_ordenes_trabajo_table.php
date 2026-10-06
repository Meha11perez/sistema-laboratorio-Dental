<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordenes_trabajo', function (Blueprint $table) {

            $table->string('area_trabajo', 30)
                ->nullable()
                ->after('codigo_caja');

            $table->string('codigo_area', 30)
                ->nullable()
                ->unique()
                ->after('area_trabajo');
        });
    }


    public function down(): void
    {
        Schema::table('ordenes_trabajo', function (Blueprint $table) {

            $table->dropUnique([
                'codigo_area'
            ]);

            $table->dropColumn([
                'area_trabajo',
                'codigo_area',
            ]);
        });
    }
};