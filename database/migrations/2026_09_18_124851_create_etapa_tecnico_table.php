<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etapa_tecnico', function (Blueprint $table) {

            $table->id();

            $table->foreignId('tecnico_id')
                ->constrained('tecnicos')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('etapa_produccion_id')
                ->constrained('etapas_produccion')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique([
                'tecnico_id',
                'etapa_produccion_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etapa_tecnico');
    }
};