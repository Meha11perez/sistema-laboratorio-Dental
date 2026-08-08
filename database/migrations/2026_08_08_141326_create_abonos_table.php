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
        Schema::create('abonos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pago_id')
                ->constrained('pagos')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('registrado_por')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('monto', 10, 2);

            $table->enum('metodo_pago', [
                'Efectivo',
                'Transferencia',
                'Depósito',
                'Otro',
            ])->default('Efectivo');

            $table->dateTime('fecha_abono');

            $table->string('referencia', 100)->nullable();

            $table->text('observaciones')->nullable();

            $table->timestamps();

            $table->index('fecha_abono');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abonos');
    }
};
