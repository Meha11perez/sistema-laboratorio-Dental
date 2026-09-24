<?php

namespace App\Http\Controllers;

use App\Models\Tecnico;
use App\Models\User;
use App\Models\EtapaProduccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TecnicoController extends Controller
{
    public function index(Request $request)
    {
        $query = Tecnico::with('user')
            ->withCount('etapasProduccion');


        // =================================================
        // BUSCAR
        // =================================================
        if ($request->filled('buscar')) {

            $buscar = $request->buscar;

            $query->where(function ($q) use ($buscar) {

                $q->where(
                    'especialidad',
                    'like',
                    '%' . $buscar . '%'
                )
                ->orWhere(
                    'telefono',
                    'like',
                    '%' . $buscar . '%'
                )
                ->orWhereHas(
                    'user',
                    function ($usuario) use ($buscar) {

                        $usuario
                            ->where(
                                'name',
                                'like',
                                '%' . $buscar . '%'
                            )
                            ->orWhere(
                                'email',
                                'like',
                                '%' . $buscar . '%'
                            );
                    }
                );
            });
        }


        // =================================================
        // ESTADO
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
        $tecnicos = $query
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();


        // =================================================
        // INDICADORES
        // =================================================
        $totalTecnicos = Tecnico::count();

        $tecnicosActivos = Tecnico::where(
            'estado',
            true
        )->count();

        $tecnicosInactivos = Tecnico::where(
            'estado',
            false
        )->count();

        $tecnicosConEtapas = Tecnico::whereHas(
            'etapasProduccion'
        )->count();


        return view(
            'administracion.tecnicos.index',
            compact(
                'tecnicos',
                'totalTecnicos',
                'tecnicosActivos',
                'tecnicosInactivos',
                'tecnicosConEtapas'
            )
        );
    }
    public function create()
    {
        $usuarios = User::whereHas(
            'role',
            function ($query) {
                $query->where('nombre', 'Técnico');
            }
        )
        ->where('estado', true)
        ->whereDoesntHave('tecnico')
        ->orderBy('name')
        ->get();


        $etapas = EtapaProduccion::where(
            'estado',
            true
        )
        ->orderBy('orden')
        ->get();


        return view(
            'administracion.tecnicos.create',
            compact(
                'usuarios',
                'etapas'
            )
        );
    }


    public function store(Request $request)
    {
        $datos = $request->validate([
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                'unique:tecnicos,user_id',
            ],

            'especialidad' => [
                'nullable',
                'string',
                'max:150',
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],

            'fecha_ingreso' => [
                'nullable',
                'date',
            ],

            'etapas' => [
                'nullable',
                'array',
            ],

            'etapas.*' => [
                'integer',
                'exists:etapas_produccion,id',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | VERIFICAR USUARIO
        |--------------------------------------------------------------------------
        */

        $usuario = User::with('role')
            ->find($datos['user_id']);


        if (
            !$usuario
            || !$usuario->estado
            || $usuario->role?->nombre !== 'Técnico'
        ) {

            return back()
                ->withErrors([
                    'user_id' =>
                        'El usuario seleccionado no corresponde a un técnico activo.',
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | CREAR TÉCNICO
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use ($datos) {

                $tecnico = Tecnico::create([
                    'user_id' =>
                        $datos['user_id'],

                    'especialidad' =>
                        $datos['especialidad'] ?? null,

                    'telefono' =>
                        $datos['telefono'] ?? null,

                    'fecha_ingreso' =>
                        $datos['fecha_ingreso'] ?? null,

                    'estado' => true,
                ]);


                /*
                |--------------------------------------------------------------------------
                | ASIGNAR ETAPAS
                |--------------------------------------------------------------------------
                */

                $tecnico
                    ->etapasProduccion()
                    ->sync(
                        $datos['etapas'] ?? []
                    );
            }
        );


        return redirect()
            ->route(
                'administracion.tecnicos.index'
            )
            ->with(
                'success',
                'Técnico registrado correctamente.'
            );
    }
public function edit(Tecnico $tecnico)
    {
        $tecnico->load([
            'user',
            'etapasProduccion',
        ]);


        $etapas = EtapaProduccion::where(
            'estado',
            true
        )
        ->orderBy('orden')
        ->get();


        return view(
            'administracion.tecnicos.edit',
            compact(
                'tecnico',
                'etapas'
            )
        );
    }
    public function update(Request $request, Tecnico $tecnico) 
    {
        $datos = $request->validate([
            'especialidad' => [
                'nullable',
                'string',
                'max:150',
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],

            'fecha_ingreso' => [
                'nullable',
                'date',
            ],

            'etapas' => [
                'nullable',
                'array',
            ],

            'etapas.*' => [
                'integer',
                'exists:etapas_produccion,id',
            ],
        ]);


        DB::transaction(
            function () use (
                $datos,
                $tecnico
            ) {

                $tecnico->update([
                    'especialidad' =>
                        $datos['especialidad'] ?? null,

                    'telefono' =>
                        $datos['telefono'] ?? null,

                    'fecha_ingreso' =>
                        $datos['fecha_ingreso'] ?? null,
                ]);


                $tecnico
                    ->etapasProduccion()
                    ->sync(
                        $datos['etapas'] ?? []
                    );
            }
        );


        return redirect()
            ->route(
                'administracion.tecnicos.index'
            )
            ->with(
                'success',
                'Técnico actualizado correctamente.'
            );
    }
public function toggleEstado(Tecnico $tecnico)
    {
        /*
        |--------------------------------------------------------------------------
        | SI SE VA A DESACTIVAR
        |--------------------------------------------------------------------------
        | No permitimos desactivar al técnico si todavía tiene
        | órdenes activas asignadas.
        */

        if ($tecnico->estado) {

            $tieneOrdenesActivas = $tecnico
                ->ordenesActuales()
                ->whereHas(
                    'estadoOrden',
                    function ($query) {

                        $query->whereNotIn(
                            'nombre',
                            [
                                'Terminado',
                                'Entregado',
                                'Cancelado',
                            ]
                        );
                    }
                )
                ->exists();


            if ($tieneOrdenesActivas) {

                return back()->with(
                    'error',
                    'No puede desactivar este técnico porque tiene órdenes activas asignadas.'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | CAMBIAR ESTADO
        |--------------------------------------------------------------------------
        */

        $tecnico->update([
            'estado' => !$tecnico->estado,
        ]);


        return back()->with(
            'success',
            $tecnico->estado
                ? 'Técnico activado correctamente.'
                : 'Técnico desactivado correctamente.'
        );
    }
}