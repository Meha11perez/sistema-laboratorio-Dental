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
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('odontologo_id')
                ->constrained('odontologos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('nombre', 150);
            $table->string('apellido',150)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
