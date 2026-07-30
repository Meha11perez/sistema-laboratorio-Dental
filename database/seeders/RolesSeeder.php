<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run (): void
    {
        Role::insert([
            [
                'nombre' => 'Administrador',
                'descripcion'=> 'Acceso completo al sistema',
                'estado'=> true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Recepcion',
                'description' => 'Gestiona órdenes, clientes y agenda.',
                'estado'=> true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Técnico',
                'descripcion' => 'Gestiona las etapas de producción asignadas',
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Mensajero',
                'descripcion' => 'Gestiona recolecciones y entregas',
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}