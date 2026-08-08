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
        Schema::create('rutas_mensajeria', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mensajero_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->date('fecha');

            $table->enum('estado', [
                'Pendiente',
                'En ruta',
                'Finalizada',
                'Cancelada',
            ])->default('Pendiente');

            $table->time('hora_salida')->nullable();
            $table->time('hora_regreso')->nullable();

            $table->text('observaciones')->nullable();

            $table->timestamps();

            $table->index('fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rutas_mensajeria');
    }
};
