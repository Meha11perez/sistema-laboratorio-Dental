<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::withCount('users');


        // BUSCAR
        if ($request->filled('buscar')) {

            $buscar = $request->buscar;

            $query->where(function ($q) use ($buscar) {

                $q->where(
                    'nombre',
                    'like',
                    '%' . $buscar . '%'
                )
                ->orWhere(
                    'descripcion',
                    'like',
                    '%' . $buscar . '%'
                );
            });
        }


        // ESTADO
        if ($request->filled('estado')) {

            if ($request->estado === 'activo') {
                $query->where('estado', true);
            }

            if ($request->estado === 'inactivo') {
                $query->where('estado', false);
            }
        }


        $roles = $query
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();


        $totalRoles = Role::count();

        $rolesActivos = Role::where(
            'estado',
            true
        )->count();

        $rolesInactivos = Role::where(
            'estado',
            false
        )->count();


        return view(
            'administracion.roles.index',
            compact(
                'roles',
                'totalRoles',
                'rolesActivos',
                'rolesInactivos'
            )
        );
    }
    public function edit(Role $role)
{
    return view(
        'administracion.roles.edit',
        compact('role')
    );
}


public function update(
    Request $request,
    Role $role
) {
    $datos = $request->validate([
        'descripcion' => [
            'nullable',
            'string',
            'max:255',
        ],
    ]);

    $role->update([
        'descripcion' => $datos['descripcion'] ?? null,
    ]);

    return redirect()
        ->route('administracion.roles.index')
        ->with(
            'success',
            'Rol actualizado correctamente.'
        );
}
public function toggleEstado(Role $role)
    {
        // El rol Administrador siempre debe permanecer activo
        if (
            $role->nombre === 'Administrador'
            && $role->estado
        ) {
            return back()->with(
                'error',
                'El rol Administrador no puede ser desactivado.'
            );
        }


        // Si vamos a desactivar, comprobar que no tenga usuarios
        if (
            $role->estado
            && $role->users()->exists()
        ) {
            return back()->with(
                'error',
                'No puede desactivar este rol porque tiene usuarios asignados.'
            );
        }


        $role->update([
            'estado' => !$role->estado,
        ]);


        return back()->with(
            'success',
            $role->estado
                ? 'Rol activado correctamente.'
                : 'Rol desactivado correctamente.'
        );
    }
}