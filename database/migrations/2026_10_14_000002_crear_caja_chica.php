<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caja_chica_periodos', function (Blueprint $table) {
            $table->id();
            $table->date('mes')->unique();
            $table->unsignedBigInteger('limite_centavos')->default(500000);
            $table->timestamps();
        });

        Schema::create('caja_chica_gastos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_chica_periodo_id')->constrained('caja_chica_periodos')->restrictOnDelete();
            $table->uuid('clave_registro')->unique();
            $table->date('fecha_gasto')->index();
            $table->string('concepto', 255);
            $table->string('beneficiario', 150)->nullable();
            $table->string('responsable', 150);
            $table->unsignedInteger('cantidad_milesimas');
            $table->unsignedBigInteger('precio_unitario_centavos');
            $table->unsignedBigInteger('monto_centavos');
            $table->string('metodo_pago', 30);
            $table->string('tipo_comprobante', 30);
            $table->string('nit', 30)->nullable();
            $table->string('numero_documento', 150)->nullable();
            $table->string('referencia', 150)->nullable();
            $table->string('comprobante_path')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('estado', 30)->index();
            $table->boolean('requiere_autorizacion')->default(false);
            $table->foreignId('registrado_por')->constrained('users')->restrictOnDelete();
            $table->foreignId('autorizado_por')->nullable()->constrained('users')->restrictOnDelete();
            $table->dateTime('fecha_autorizacion')->nullable();
            $table->text('motivo_decision')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
            $table->index(['caja_chica_periodo_id', 'estado']);
        });

        Schema::create('caja_chica_correcciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_chica_gasto_id')->constrained('caja_chica_gastos')->restrictOnDelete();
            $table->uuid('clave_registro')->unique();
            $table->unsignedInteger('version_gasto');
            $table->string('tipo', 20);
            $table->json('datos_anteriores');
            $table->json('datos_propuestos')->nullable();
            $table->json('datos_resultantes')->nullable();
            $table->text('motivo_solicitud');
            $table->string('estado', 20)->default('Pendiente')->index();
            $table->foreignId('solicitado_por')->constrained('users')->restrictOnDelete();
            $table->foreignId('resuelto_por')->nullable()->constrained('users')->restrictOnDelete();
            $table->dateTime('fecha_resolucion')->nullable();
            $table->text('motivo_resolucion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caja_chica_correcciones');
        Schema::dropIfExists('caja_chica_gastos');
        Schema::dropIfExists('caja_chica_periodos');
    }
};
