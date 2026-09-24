<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
class UserController extends Controller
{
    // =====================================================
    // LISTADO DE USUARIOS
    // =====================================================
    public function index(Request $request)
    {
        $query = User::with('role');

        // =================================================
        // BUSCADOR
        // =================================================
        if ($request->filled('buscar')) {

            $buscar = $request->buscar;

            $query->where(function ($q) use ($buscar) {

                $q->where(
                    'name',
                    'like',
                    '%' . $buscar . '%'
                )
                ->orWhere(
                    'email',
                    'like',
                    '%' . $buscar . '%'
                )
                ->orWhereHas(
                    'role',
                    function ($role) use ($buscar) {

                        $role->where(
                            'nombre',
                            'like',
                            '%' . $buscar . '%'
                        );
                    }
                );
            });
        }


        // =================================================
        // FILTRO POR ROL
        // =================================================
        if ($request->filled('rol')) {

            $query->where(
                'role_id',
                $request->rol
            );
        }


        // =================================================
        // FILTRO POR ESTADO
        // =================================================
        if ($request->filled('estado')) {

            if ($request->estado === 'activo') {
                $query->where('estado', true);
            }

            if ($request->estado === 'inactivo') {
                $query->where('estado', false);
            }
        }


        // =================================================
        // LISTADO
        // =================================================
        $usuarios = $query
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        // =================================================
        // ROLES PARA FILTRO
        // =================================================
        $roles = Role::where('estado', true)
            ->orderBy('nombre')
            ->get();

        // =================================================
        // INDICADORES
        // =================================================
        $totalUsuarios = User::count();

        $usuariosActivos = User::where(
            'estado',
            true
        )->count();

        $usuariosInactivos = User::where(
            'estado',
            false
        )->count();

        $rolesActivos = Role::where(
            'estado',
            true
        )->count();


        return view(
            'administracion.usuarios.index',
            compact(
                'usuarios',
                'roles',
                'totalUsuarios',
                'usuariosActivos',
                'usuariosInactivos',
                'rolesActivos'
            )
        );
    }
    public function edit(User $user)
{
    $roles = Role::where('estado', true)
        ->orderBy('nombre')
        ->get();

    return view(
        'administracion.usuarios.edit',
        compact('user', 'roles')
    );
}


public function update(Request $request, User $user)
{
    $datos = $request->validate([
        'name' => [
            'required',
            'string',
            'max:255',
        ],

        'email' => [
            'required',
            'email',
            'max:255',

            \Illuminate\Validation\Rule::unique(
                'users',
                'email'
            )->ignore($user->id),
        ],

        'role_id' => [
            'required',
            'exists:roles,id',
        ],

        'password' => [
            'nullable',
            'string',
            'min:8',
            'confirmed',
        ],
    ]);


    // No permitir que el administrador
    // cambie su propio rol accidentalmente.
    if (
        auth()->id() === $user->id
        &&
        (int) $datos['role_id']
            !== (int) $user->role_id
    ) {
        return back()
            ->withInput()
            ->withErrors([
                'role_id' =>
                    'No puede cambiar el rol de su propia cuenta.',
            ]);
    }


    $actualizacion = [
        'name' => $datos['name'],
        'email' => $datos['email'],
        'role_id' => $datos['role_id'],
    ];


    // Solo cambia la contraseña
    // cuando el administrador escribe una nueva.
    if (!empty($datos['password'])) {
        $actualizacion['password'] =
            $datos['password'];
    }


    $user->update($actualizacion);


    return redirect()
        ->route('administracion.usuarios.index')
        ->with(
            'success',
            'Usuario actualizado correctamente.'
        );
}

public function toggleEstado(User $user)
    {
        // Evitar que el administrador
        // se desactive a sí mismo.
        if (auth()->id() === $user->id) {

            return back()->with(
                'error',
                'No puede desactivar su propia cuenta.'
            );
        }


        $user->update([
            'estado' => !$user->estado,
        ]);


        return back()->with(
            'success',
            $user->estado
                ? 'Usuario activado correctamente.'
                : 'Usuario desactivado correctamente.'
        );
    }
    public function create()
{
    $roles = Role::where('estado', true)
        ->orderBy('nombre')
        ->get();

    return view(
        'administracion.usuarios.create',
        compact('roles')
    );
}
public function store(Request $request)
    {
        $datos = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'role_id' => [
                'required',
                'exists:roles,id',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);


        User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'role_id' => $datos['role_id'],
            'password' => $datos['password'],
            'estado' => true,
        ]);


        return redirect()
            ->route('administracion.usuarios.index')
            ->with(
                'success',
                'Usuario registrado correctamente.'
            );
    }
}