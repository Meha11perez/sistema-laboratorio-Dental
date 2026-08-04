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
        Schema::create('odontologos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('clinica_id')
            ->nullable()
            ->constrained('clinicas')
            ->cascadeOnUpdate()
            ->nullOnDelete();

            $table->string('nombre', 150);
            $table->string('telefono', 20)->nullable();
            $table->string('correo')->nullable();
            $table->string('numero_colegiado', 50)->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('odontologos');
    }
};
