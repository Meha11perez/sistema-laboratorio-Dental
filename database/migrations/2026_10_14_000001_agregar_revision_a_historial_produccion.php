<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('historial_produccion', function (Blueprint $table) {
            $table->boolean('pendiente_revision')->default(false)->index();
            $table->foreignId('revisado_por')->nullable()->constrained('users')->restrictOnDelete();
            $table->dateTime('fecha_revision')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('historial_produccion', function (Blueprint $table) {
            $table->dropForeign(['revisado_por']);
            $table->dropIndex(['pendiente_revision']);
            $table->dropColumn(['pendiente_revision', 'revisado_por', 'fecha_revision']);
        });
    }
};