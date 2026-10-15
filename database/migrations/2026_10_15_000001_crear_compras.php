<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->uuid('clave_registro')->unique();
            $table->date('fecha')->index();
            $table->string('proveedor', 150);
            $table->string('nit', 30);
            $table->string('serie', 50)->default('');
            $table->string('numero_factura', 100);
            $table->unique(['nit', 'serie', 'numero_factura'], 'compras_factura_unica');
            $table->string('metodo_pago', 30);
            $table->string('referencia', 150)->nullable();
            $table->string('factura_path');
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('total_centavos');
            $table->string('estado', 20)->default('Registrada')->index();
            $table->unsignedInteger('version')->default(1);
            $table->foreignId('registrado_por')->constrained('users')->restrictOnDelete();
            $table->foreignId('recibido_por')->nullable()->constrained('users')->restrictOnDelete();
            $table->dateTime('fecha_recepcion')->nullable();
            $table->timestamps();
        });
        Schema::create('compra_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->constrained('compras')->restrictOnDelete();
            $table->string('tipo', 20);
            $table->foreignId('material_id')->nullable()->constrained('materiales')->restrictOnDelete();
            $table->string('descripcion', 255);
            $table->string('unidad', 50);
            $table->unsignedBigInteger('cantidad_centesimas');
            $table->unsignedBigInteger('precio_centavos');
            $table->unsignedBigInteger('total_centavos');
            $table->foreignId('movimiento_entrada_id')->nullable()->unique()->constrained('movimientos_inventario')->restrictOnDelete();
            $table->timestamps();
        });
        Schema::create('compra_correcciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->constrained('compras')->restrictOnDelete();
            $table->uuid('clave_registro')->unique();
            $table->unsignedInteger('version_compra');
            $table->string('tipo', 20);
            $table->json('anteriores');
            $table->json('propuestos')->nullable();
            $table->json('aplicados')->nullable();
            $table->text('motivo');
            $table->string('estado', 20)->default('Pendiente')->index();
            $table->foreignId('solicitado_por')->constrained('users')->restrictOnDelete();
            $table->foreignId('resuelto_por')->nullable()->constrained('users')->restrictOnDelete();
            $table->dateTime('fecha_resolucion')->nullable();
            $table->text('motivo_resolucion')->nullable();
            $table->timestamps();
        });
        Schema::create('compra_historiales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->constrained('compras')->restrictOnDelete();
            $table->foreignId('usuario_id')->constrained('users')->restrictOnDelete();
            $table->string('accion', 80);
            $table->text('motivo');
            $table->json('datos');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compra_historiales');
        Schema::dropIfExists('compra_correcciones');
        Schema::dropIfExists('compra_detalles');
        Schema::dropIfExists('compras');
    }
};
