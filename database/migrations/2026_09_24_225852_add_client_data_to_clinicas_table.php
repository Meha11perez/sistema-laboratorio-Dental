<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinicas', function (Blueprint $table) {

            $table->string('nit', 30)
                ->nullable()
                ->after('direccion');

            $table->string('asistente_secretaria', 150)
                ->nullable()
                ->after('nit');

            $table->string('departamento', 100)
                ->nullable()
                ->after('asistente_secretaria');

            $table->string('municipio', 100)
                ->nullable()
                ->after('departamento');
        });
    }


    public function down(): void
    {
        Schema::table('clinicas', function (Blueprint $table) {

            $table->dropColumn([
                'nit',
                'asistente_secretaria',
                'departamento',
                'municipio',
            ]);
        });
    }
};