<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use RuntimeException;
 
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $nombre = trim((string) config('administrador.nombre', 'Administrador'));
        $username = Str::upper(trim((string) config('administrador.usuario')));

        Validator::make(
            ['name' => $nombre, 'username' => $username],
            [
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[A-Z0-9][A-Z0-9._-]*$/'],
            ]
        )->validate();

        $existente = User::with('role')->where('username', $username)->first();

        if ($existente) {
            if ($existente->role?->nombre !== 'Administrador') {
                throw new RuntimeException('Ese nombre de usuario pertenece a otra cuenta. No se cambió su rol.');
            }

            if (!$existente->estado || !$existente->role->estado) {
                throw new RuntimeException('La cuenta o el rol están inactivos. Revise su estado desde la administración de usuarios.');
            }

            $this->command?->info('El administrador ya existe. Se conservaron sus datos y su contraseña.');
            return;
        }

        $password = config('administrador.password');
        Validator::make(
            ['password' => $password],
            ['password' => ['required', 'string', 'min:12']]
        )->validate();

        DB::transaction(function () use ($nombre, $username, $password) {
            $rol = Role::firstOrCreate(
                ['nombre' => 'Administrador'],
                ['descripcion' => 'Acceso completo al sistema.', 'estado' => true]
            );

            if (!$rol->estado) {
                throw new RuntimeException('El rol Administrador está inactivo. No se creó la cuenta.');
            }

            User::create([
                'role_id' => $rol->id,
                'estado' => true,
                'name' => $nombre,
                'username' => $username,
                'email' => null,
                'password' => Hash::make($password),
            ]);
        });

        $this->command?->info('Administrador creado correctamente. Ya puede iniciar sesión con su nombre de usuario.');
    }
}
