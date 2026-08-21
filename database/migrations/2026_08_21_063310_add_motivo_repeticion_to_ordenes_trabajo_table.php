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

            $table->string('motivo_repeticion', 255)
                ->nullable()
                ->after('orden_origen_id');
        });
    }

    public function down(): void
    {
        Schema::table('ordenes_trabajo', function (Blueprint $table) {
            $table->dropColumn('motivo_repeticion');
        });
    }
};
