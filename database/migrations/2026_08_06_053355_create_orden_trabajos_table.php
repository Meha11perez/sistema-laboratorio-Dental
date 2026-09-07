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
            Schema::create('ordenes_trabajo', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 30)->unique();
            $table->string('codigo_caja', 20)->nullable();

            $table->foreignId('odontologo_id')
                ->constrained('odontologos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('paciente_id')
                ->constrained('pacientes')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('tipo_protesis_id')
                ->constrained('tipos_protesis')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('estado_orden_id')
                ->constrained('estados_orden')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('etapa_actual_id')
                ->nullable()
                ->constrained('etapas_produccion')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('registrado_por')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('tecnico_actual_id')
                ->nullable()
                ->constrained('tecnicos')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->date('fecha_ingreso');
            $table->date('fecha_entrega_estimada')->nullable();
            $table->date('fecha_entrega_real')->nullable();
            $table->date('fecha_entrega_programada')->nullable();

            $table->unsignedSmallInteger('cantidad')->default(1);

            $table->text('especificaciones');
            $table->text('observaciones')->nullable();
            
            $table->string('color', 100)->nullable();

            $table->enum('prioridad', [
                'Normal',
                'Urgente',
            ])->default('Normal');
            
            $table->enum('tipo_orden', [
                'Nueva',
                'Repeticion',
            ])->default('Nueva');

            $table->foreignId('orden_origen_id')
                ->nullable()
                ->constrained('ordenes_trabajo')
                ->nullOnDelete();

            $table->string('motivo_repeticion', 255)
                ->nullable();

            $table->decimal('total', 10, 2)->default(0);

            $table->timestamps();

            $table->index('codigo_caja');
            $table->index('fecha_ingreso');
            $table->index('fecha_entrega_estimada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_trabajo');
    }
};
