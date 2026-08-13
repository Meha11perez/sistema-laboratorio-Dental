<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $rolAdministrador = Role::where('nombre', 'Administrador')->first();

        User::updateOrCreate(
            [
                'email' => 'admin@laboratorio.com',
            ],
            [
                'role_id' => $rolAdministrador->id,
                'name' => 'Administrador',
                'password' => Hash::make('Admin12345'),
            ]
        );
    }
}