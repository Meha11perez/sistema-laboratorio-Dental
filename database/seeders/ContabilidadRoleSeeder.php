<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class ContabilidadRoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(
            ['nombre' => 'Contabilidad'],
            [
                'descripcion' => 'Gestión de cobros, cuentas y operaciones contables.',
                'estado' => true,
            ]
        );
    }
}