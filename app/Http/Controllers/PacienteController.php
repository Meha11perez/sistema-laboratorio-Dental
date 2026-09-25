<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Odontologo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PacienteController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Paciente::with([
            'odontologo.clinica'
        ]);


        /*
        |--------------------------------------------------------------------------
        | BUSCAR
        |--------------------------------------------------------------------------
        */

        if ($request->filled('buscar')) {

            $buscar = $request->buscar;

            $query->where(function ($q) use ($buscar) {

                $q->where(
                    'nombre',
                    'like',
                    '%' . $buscar . '%'
                )
                ->orWhere(
                    'apellido',
                    'like',
                    '%' . $buscar . '%'
                )
                ->orWhere(
                    'telefono',
                    'like',
                    '%' . $buscar . '%'
                )
                ->orWhereHas(
                    'odontologo',
                    function ($odontologo) use ($buscar) {

                        $odontologo->where(
                            'nombre',
                            'like',
                            '%' . $buscar . '%'
                        );

                    }
                )
                ->orWhereHas(
                    'odontologo.clinica',
                    function ($clinica) use ($buscar) {

                        $clinica->where(
                            'nombre',
                            'like',
                            '%' . $buscar . '%'
                        );

                    }
                );

            });
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO ODONTÓLOGO
        |--------------------------------------------------------------------------
        */

        if ($request->filled('odontologo_id')) {

            $query->where(
                'odontologo_id',
                $request->odontologo_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO ESTADO
        |--------------------------------------------------------------------------
        */

        if ($request->filled('estado')) {

            if ($request->estado === 'activo') {

                $query->where(
                    'estado',
                    true
                );
            }

            if ($request->estado === 'inactivo') {

                $query->where(
                    'estado',
                    false
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | LISTADO
        |--------------------------------------------------------------------------
        */

        $pacientes = $query
            ->withCount('ordenesTrabajo')
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->paginate(10)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | ODONTÓLOGOS PARA FILTRO
        |--------------------------------------------------------------------------
        */

        $odontologos = Odontologo::query()
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | INDICADORES
        |--------------------------------------------------------------------------
        */

        $totalPacientes =
            Paciente::count();

        $pacientesActivos =
            Paciente::where(
                'estado',
                true
            )->count();

        $pacientesInactivos =
            Paciente::where(
                'estado',
                false
            )->count();

        $pacientesConOrdenes =
            Paciente::whereHas(
                'ordenesTrabajo'
            )->count();


        return view(
            'administracion.pacientes.index',
            compact(
                'pacientes',
                'odontologos',
                'totalPacientes',
                'pacientesActivos',
                'pacientesInactivos',
                'pacientesConOrdenes'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREAR
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $odontologos = Odontologo::with('clinica')
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();


        return view(
            'administracion.pacientes.create',
            compact('odontologos')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GUARDAR
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $datos = $request->validate([

            'odontologo_id' => [
                'required',

                Rule::exists(
                    'odontologos',
                    'id'
                )->where(
                    'estado',
                    true
                ),
            ],

            'nombre' => [
                'required',
                'string',
                'max:150',
            ],

            'apellido' => [
                'nullable',
                'string',
                'max:150',
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],

            'observaciones' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);


        Paciente::create([
            'odontologo_id' =>
                $datos['odontologo_id'],

            'nombre' =>
                $datos['nombre'],

            'apellido' =>
                $datos['apellido'] ?? null,

            'telefono' =>
                $datos['telefono'] ?? null,

            'observaciones' =>
                $datos['observaciones'] ?? null,

            'estado' => true,
        ]);


        return redirect()
            ->route(
                'administracion.pacientes.index'
            )
            ->with(
                'success',
                'Paciente registrado correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | EDITAR
    |--------------------------------------------------------------------------
    */

    public function edit(Paciente $paciente)
    {
        $odontologos = Odontologo::with('clinica')
            ->where(function ($query) use ($paciente) {

                $query->where(
                    'estado',
                    true
                )
                ->orWhere(
                    'id',
                    $paciente->odontologo_id
                );

            })
            ->orderBy('nombre')
            ->get();


        return view(
            'administracion.pacientes.edit',
            compact(
                'paciente',
                'odontologos'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Paciente $paciente
    ) {
        $datos = $request->validate([

            'odontologo_id' => [
                'required',
                'exists:odontologos,id',
            ],

            'nombre' => [
                'required',
                'string',
                'max:150',
            ],

            'apellido' => [
                'nullable',
                'string',
                'max:150',
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],

            'observaciones' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);


        $paciente->update([
            'odontologo_id' =>
                $datos['odontologo_id'],

            'nombre' =>
                $datos['nombre'],

            'apellido' =>
                $datos['apellido'] ?? null,

            'telefono' =>
                $datos['telefono'] ?? null,

            'observaciones' =>
                $datos['observaciones'] ?? null,
        ]);


        return redirect()
            ->route(
                'administracion.pacientes.index'
            )
            ->with(
                'success',
                'Paciente actualizado correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ACTIVAR / INACTIVAR
    |--------------------------------------------------------------------------
    */

    public function toggleEstado(Paciente $paciente)
    {
        /*
        | Si está activo y se intenta inactivar,
        | comprobamos que no tenga órdenes activas.
        */

        if ($paciente->estado) {

            $tieneOrdenesActivas = $paciente
                ->ordenesTrabajo()
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
                    'No puede desactivar este paciente porque tiene órdenes activas asociadas.'
                );
            }
        }


        $paciente->update([
            'estado' =>
                !$paciente->estado,
        ]);


        return back()->with(
            'success',
            $paciente->estado
                ? 'Paciente activado correctamente.'
                : 'Paciente desactivado correctamente.'
        );
    }
}