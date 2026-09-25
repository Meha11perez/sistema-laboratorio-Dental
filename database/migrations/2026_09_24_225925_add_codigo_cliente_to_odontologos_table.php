<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('odontologos', function (Blueprint $table) {

            $table->string('codigo_cliente', 20)
                ->nullable()
                ->unique()
                ->after('id');
        });
    }


    public function down(): void
    {
        Schema::table('odontologos', function (Blueprint $table) {

            $table->dropUnique([
                'codigo_cliente'
            ]);

            $table->dropColumn(
                'codigo_cliente'
            );
        });
    }
};