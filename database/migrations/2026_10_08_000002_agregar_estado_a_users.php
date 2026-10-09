<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'estado')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('estado')->default(true);
        });
    }

    public function down(): void
    {
        // Se conserva la columna para no perder el estado de las cuentas.
        // También puede existir desde la migración original de users.
    }
};