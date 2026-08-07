<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('historial_produccion', function (Blueprint $table) {
        $table->id();

        $table->foreignId('orden_trabajo_id')
            ->constrained('ordenes_trabajo')
            ->cascadeOnUpdate()
            ->cascadeOnDelete();

        $table->foreignId('etapa_produccion_id')
            ->constrained('etapas_produccion')
            ->cascadeOnUpdate()
            ->restrictOnDelete();

        $table->foreignId('tecnico_id')
            ->nullable()
            ->constrained('tecnicos')
            ->cascadeOnUpdate()
            ->nullOnDelete();

        $table->foreignId('registrado_por')
            ->constrained('users')
            ->cascadeOnUpdate()
            ->restrictOnDelete();

        $table->dateTime('fecha_inicio');

        $table->dateTime('fecha_fin')->nullable();

        $table->enum('estado', [
            'Pendiente',
            'En proceso',
            'Completado',
            'Pausado',
        ])->default('En proceso');

        $table->text('observaciones')->nullable();

        $table->timestamps();

        $table->index('fecha_inicio');
        $table->index('fecha_fin');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_produccion');
    }
};
