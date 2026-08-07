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
        Schema::create('inventarios_tecnicos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tecnico_id')
                ->constrained('tecnicos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('material_id')
                ->constrained('materiales')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('cantidad', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['tecnico_id', 'material_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventarios_tecnicos');
    }
};
