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
       Schema::create('etapas_produccion', function (Blueprint $table) {
        $table->id();
        $table->string('nombre', 100)->unique();
        $table->string('descripcion', 255)->nullable();
        $table->unsignedSmallInteger('orden')->default(1);
        $table->boolean('estado')->default(true);
        $table->timestamps();
     });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etapas_produccion');
    }
};
