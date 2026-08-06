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
        Schema::create('tipo_protesis', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->enum('categoria', [
                'removible',
                'fija',
                'ortodoncia',
            ]);
            $table->text('descripcion')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->unique(['nombre', 'categoria']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_proteses');
    }
};
