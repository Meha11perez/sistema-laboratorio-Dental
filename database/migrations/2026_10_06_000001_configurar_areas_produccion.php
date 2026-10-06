<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipos_protesis', function (Blueprint $table) {
            $table->string('area_trabajo', 30)->nullable();
        });

        Schema::table('etapas_produccion', function (Blueprint $table) {
            $table->json('areas_trabajo')->nullable();
            $table->boolean('es_final')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('etapas_produccion', function (Blueprint $table) {
            $table->dropColumn(['areas_trabajo', 'es_final']);
        });

        Schema::table('tipos_protesis', function (Blueprint $table) {
            $table->dropColumn('area_trabajo');
        });
    }
};
