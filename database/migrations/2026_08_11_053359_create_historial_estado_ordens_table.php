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
        Schema::create('historial_estados_orden', function (Blueprint $table) {
            $table->id();

            $table->foreignId('orden_trabajo_id')
                ->constrained('ordenes_trabajo')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('estado_orden_id')
                ->constrained('estados_orden')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('registrado_por')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete(); 

            $table->string('motivo')->nullable();

            $table->text('observaciones')->nullable();

            $table->dateTime('fecha');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_estados_orden');
    }

    /**
     * Reverse the migrations.
     */
    
};
