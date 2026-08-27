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
                $table->foreignId('devolucion_id')
                    ->nullable()
                    ->after('orden_origen_id')
                    ->constrained('devoluciones')
                    ->nullOnDelete();
            });
        }

    public function down(): void
        {
            Schema::table('ordenes_trabajo', function (Blueprint $table) {
                $table->dropForeign(['devolucion_id']);
                $table->dropColumn('devolucion_id');
            });
        }
};
