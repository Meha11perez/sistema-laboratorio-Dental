<?php

namespace Database\Seeders;

use App\Models\EstadoOrden;
use Illuminate\Database\Seeder;

class EstadoOrdenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EstadoOrden::insert([
            [
                'nombre' => 'Recibido',
                'descripcion' => 'La orden fue recibida en el laboratorio',
                'color' => '#6B7280',
                'orden' => 1,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Pendiente',
                'descripcion' => 'La orden está pendiente de iniciar producción',
                'color' => '#F59E0B',
                'orden' => 2,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'En proceso',
                'descripcion' => 'La orden se encuentra en producción',
                'color' => '#3B82F6',
                'orden' => 3,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'En prueba',
                'descripcion' => 'La pieza está en etapa de prueba',
                'color' => '#8B5CF6',
                'orden' => 4,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Terminado',
                'descripcion' => 'La producción de la pieza ha finalizado',
                'color' => '#10B981',
                'orden' => 5,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Entregado',
                'descripcion' => 'La orden fue entregada al odontólogo',
                'color' => '#059669',
                'orden' => 6,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Cancelado',
                'descripcion' => 'La orden fue cancelada',
                'color' => '#EF4444',
                'orden' => 7,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
