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
        Schema::create('cuentas_odontologos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('odontologo_id')
                ->unique()
                ->constrained('odontologos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->enum('modalidad_pago', [
                'Contado',
                'Semanal',
                'Crédito',
            ])->default('Contado');

            $table->decimal('limite_credito', 10, 2)->nullable();

            $table->decimal('saldo_pendiente', 10, 2)->default(0);

            $table->boolean('estado')->default(true);

            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas_odontologos');
    }
};
