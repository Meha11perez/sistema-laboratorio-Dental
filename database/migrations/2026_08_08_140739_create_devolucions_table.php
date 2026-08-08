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
        Schema::create('devoluciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('orden_trabajo_id')
                ->constrained('ordenes_trabajo')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('garantia_id')
                ->nullable()
                ->constrained('garantias')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('tecnico_responsable_id')
                ->nullable()
                ->constrained('tecnicos')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('registrado_por')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->enum('tipo', [
                'Devolución',
                'Repetición',
                'Corrección',
            ]);

            $table->text('motivo');

            $table->dateTime('fecha_devolucion');

            $table->boolean('requiere_repeticion')->default(false);

            $table->decimal('perdida_estimada', 10, 2)->default(0);

            $table->enum('estado', [
                'Registrada',
                'En revisión',
                'En corrección',
                'Resuelta',
                'Rechazada',
            ])->default('Registrada');

            $table->text('observaciones')->nullable();

            $table->timestamps();

            $table->index('fecha_devolucion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devoluciones');
    }
};
