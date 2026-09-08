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
        Schema::create('detalles_mensajeria', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ruta_mensajeria_id')
                ->constrained('rutas_mensajeria')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('reprogramada_desde_id')
                ->nullable()
                ->constrained('detalles_mensajeria')
                ->nullOnDelete();

            $table->foreignId('orden_trabajo_id')
                ->nullable()
                ->constrained('ordenes_trabajo')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('odontologo_id')
                ->nullable()
                ->constrained('odontologos')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('clinica_id')
                ->nullable()
                ->constrained('clinicas')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->enum('tipo_movimiento', [
                'Entrega',
                'Recolección',
            ]);

            $table->unsignedSmallInteger('orden_visita')->nullable();

            $table->string('direccion_referencia', 255)->nullable();

            $table->enum('estado', [
                'Pendiente',
                'Realizada',
                'No realizada',
                'Reprogramada',
            ])->default('Pendiente');

            $table->time('hora_realizada')->nullable();
            $table->string('recibido_por', 150)->nullable();
            $table->string('firma_recibido')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_mensajeria');
    }
};
