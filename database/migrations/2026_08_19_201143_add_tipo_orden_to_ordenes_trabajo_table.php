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
    Schema::table('ordenes_trabajo', function (Blueprint $table) {

        $table->enum('tipo_orden', [
            'Nueva',
            'Repeticion',
        ])
        ->default('Nueva')
        ->after('prioridad');

        $table->foreignId('orden_origen_id')
            ->nullable()
            ->after('tipo_orden')
            ->constrained('ordenes_trabajo')
            ->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('ordenes_trabajo', function (Blueprint $table) {

        $table->dropForeign(['orden_origen_id']);

        $table->dropColumn([
            'tipo_orden',
            'orden_origen_id',
        ]);
    });
}
};
