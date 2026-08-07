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
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();

            $table->foreignId('material_id')
                ->constrained('materiales')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('tecnico_id')
                ->nullable()
                ->constrained('tecnicos')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('orden_trabajo_id')
                ->nullable()
                ->constrained('ordenes_trabajo')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('registrado_por')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->enum('tipo_movimiento', [
                'Entrada',
                'Salida',
                'Consumo',
                'Ajuste',
                'Devolución',
            ]);

            $table->decimal('cantidad', 10, 2);

            $table->decimal('stock_anterior', 10, 2);
            $table->decimal('stock_nuevo', 10, 2);

            $table->dateTime('fecha_movimiento');

            $table->text('observaciones')->nullable();

            $table->timestamps();

            $table->index('fecha_movimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
