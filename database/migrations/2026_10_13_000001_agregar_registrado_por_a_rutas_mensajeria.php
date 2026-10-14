<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rutas_mensajeria', function (Blueprint $table) {
            $table->foreignId('registrado_por')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rutas_mensajeria', function (Blueprint $table) {
            $table->dropConstrainedForeignId('registrado_por');
        });
    }
};