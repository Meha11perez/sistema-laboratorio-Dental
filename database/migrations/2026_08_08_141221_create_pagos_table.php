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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('orden_trabajo_id')
                ->constrained('ordenes_trabajo')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('odontologo_id')
                ->constrained('odontologos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('cuenta_odontologo_id')
                ->nullable()
                ->constrained('cuentas_odontologos')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('registrado_por')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('monto_total', 10, 2);

            $table->decimal('monto_pagado', 10, 2)->default(0);

            $table->decimal('saldo_pendiente', 10, 2)->default(0);

            $table->enum('estado_pago', [
                'Pendiente',
                'Parcial',
                'Pagado',
            ])->default('Pendiente');

            $table->date('fecha_registro');

            $table->date('fecha_vencimiento')->nullable();

            $table->text('observaciones')->nullable();

            $table->timestamps();

            $table->index('fecha_registro');
            $table->index('estado_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
