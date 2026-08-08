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
            Schema::create('garantias', function (Blueprint $table) {
                $table->id();

                $table->foreignId('orden_trabajo_id')
                    ->constrained('ordenes_trabajo')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();

                $table->date('fecha_inicio');
                $table->date('fecha_vencimiento');

                $table->enum('estado', [
                    'Vigente',
                    'Por vencer',
                    'Vencida',
                    'Aplicada',
                    'Anulada',
                ])->default('Vigente');

                $table->text('observaciones')->nullable();

                $table->timestamps();

                $table->index('fecha_vencimiento');
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('garantias');
    }
};
