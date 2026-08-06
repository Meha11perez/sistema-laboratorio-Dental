<?php

namespace Database\Seeders;

use App\Models\EtapaProduccion;
use Illuminate\Database\Seeder;

class EtapaProduccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EtapaProduccion::insert([
            [
                'nombre' => 'Cromos',
                'descripcion' => 'Trabajos relacionados con estructuras de cromo cobalto',
                'orden' => 1,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Rodetes y cubetas individuales',
                'descripcion' => 'Elaboración de rodetes y cubetas individuales',
                'orden' => 2,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Prueba de dientes',
                'descripcion' => 'Prueba estética y funcional de dientes',
                'orden' => 3,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Prueba de metal',
                'descripcion' => 'Prueba de estructura metálica',
                'orden' => 4,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Biscochos',
                'descripcion' => 'Etapa de prueba de porcelana sin acabado final',
                'orden' => 5,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Terminados',
                'descripcion' => 'Trabajos finalizados y listos para entrega',
                'orden' => 6,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Ortodoncia',
                'descripcion' => 'Trabajos correspondientes al área de ortodoncia',
                'orden' => 7,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    
    }
}
