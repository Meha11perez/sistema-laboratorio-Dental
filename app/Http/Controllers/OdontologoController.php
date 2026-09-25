<?php

namespace App\Http\Controllers;

use App\Models\Odontologo;
use App\Models\Clinica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OdontologoController extends Controller
{
    public function index(Request $request)
    {
        $query = Odontologo::with('clinica');


        // =================================================
        // BUSCAR
        // =================================================

        if ($request->filled('buscar')) {

            $buscar = $request->buscar;

            $query->where(function ($q) use ($buscar) {

                $q->where(
                    'nombre',
                    'like',
                    '%' . $buscar . '%'
                )
                ->orWhere(
                    'telefono',
                    'like',
                    '%' . $buscar . '%'
                )
                ->orWhere(
                    'correo',
                    'like',
                    '%' . $buscar . '%'
                )
                ->orWhere(
                    'numero_colegiado',
                    'like',
                    '%' . $buscar . '%'
                )
                ->orWhereHas(
                    'clinica',
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


        // =================================================
        // CLÍNICA
        // =================================================

        if ($request->filled('clinica_id')) {

            $query->where(
                'clinica_id',
                $request->clinica_id
            );
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

        $odontologos = $query
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();


        // =================================================
        // CLÍNICAS PARA FILTRO
        // =================================================

        $clinicas = Clinica::where(
            'estado',
            true
        )
        ->orderBy('nombre')
        ->get();


        // =================================================
        // INDICADORES
        // =================================================

        $totalOdontologos = Odontologo::count();

        $odontologosActivos = Odontologo::where(
            'estado',
            true
        )->count();

        $odontologosInactivos = Odontologo::where(
            'estado',
            false
        )->count();

        $odontologosConClinica = Odontologo::whereNotNull(
            'clinica_id'
        )->count();


        return view(
            'administracion.odontologos.index',
            compact(
                'odontologos',
                'clinicas',
                'totalOdontologos',
                'odontologosActivos',
                'odontologosInactivos',
                'odontologosConClinica'
            )
        );
    }
    public function create()
{
    $clinicas = Clinica::where('estado', true)
        ->orderBy('nombre')
        ->get();

    return view(
        'administracion.odontologos.create',
        compact('clinicas')
    );
}

public function store(Request $request)
{
    $datos = $request->validate([
        'clinica_id' => [
            'nullable',
            'integer',
            'exists:clinicas,id',
        ],

        'nombre' => [
            'required',
            'string',
            'max:150',
        ],

        'telefono' => [
            'nullable',
            'string',
            'max:20',
        ],

        'correo' => [
            'nullable',
            'email',
            'max:150',
        ],

        'numero_colegiado' => [
            'nullable',
            'string',
            'max:50',
        ],
    ]);


    DB::transaction(function () use ($datos) {

        /*
        |--------------------------------------------------------------------------
        | CREAR ODONTÓLOGO
        |--------------------------------------------------------------------------
        */

        $odontologo = Odontologo::create([
            'clinica_id' =>
                $datos['clinica_id'] ?? null,

            'nombre' =>
                $datos['nombre'],

            'telefono' =>
                $datos['telefono'] ?? null,

            'correo' =>
                $datos['correo'] ?? null,

            'numero_colegiado' =>
                $datos['numero_colegiado'] ?? null,

            'estado' => true,
        ]);


        /*
        |--------------------------------------------------------------------------
        | GENERAR CÓDIGO ÚNICO
        |--------------------------------------------------------------------------
        |
        | Ejemplos:
        | ID 1  = CLI-0001
        | ID 25 = CLI-0025
        |
        */

        $codigoCliente =
            'CLI-' .
            str_pad(
                $odontologo->id,
                4,
                '0',
                STR_PAD_LEFT
            );


        $odontologo->update([
            'codigo_cliente' => $codigoCliente,
        ]);

    });


    return redirect()
        ->route(
            'administracion.odontologos.index'
        )
        ->with(
            'success',
            'Odontólogo registrado correctamente.'
        );
}

public function edit(Odontologo $odontologo)
    {
        $clinicas = Clinica::where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view(
            'administracion.odontologos.edit',
            compact(
                'odontologo',
                'clinicas'
            )
        );
    }


    public function update(
        Request $request,
        Odontologo $odontologo
    ) {
        $datos = $request->validate([
            'clinica_id' => [
                'nullable',
                'integer',
                'exists:clinicas,id',
            ],

            'nombre' => [
                'required',
                'string',
                'max:150',
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],

            'correo' => [
                'nullable',
                'email',
                'max:150',
            ],

            'numero_colegiado' => [
                'nullable',
                'string',
                'max:50',
            ],
        ]);


        $odontologo->update([
            'clinica_id' =>
                $datos['clinica_id'] ?? null,

            'nombre' =>
                $datos['nombre'],

            'telefono' =>
                $datos['telefono'] ?? null,

            'correo' =>
                $datos['correo'] ?? null,

            'numero_colegiado' =>
                $datos['numero_colegiado'] ?? null,
        ]);


        return redirect()
            ->route(
                'administracion.odontologos.index'
            )
            ->with(
                'success',
                'Odontólogo actualizado correctamente.'
            );
    }

    public function toggleEstado(Odontologo $odontologo) 
        {
            /*
            |--------------------------------------------------------------------------
            | DESACTIVAR
            |--------------------------------------------------------------------------
            | Evitamos desactivar al odontólogo si todavía
            | tiene órdenes activas.
            */

            if ($odontologo->estado) {

                $tieneOrdenesActivas = $odontologo
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
                        'No puede desactivar este odontólogo porque tiene órdenes activas asociadas.'
                    );
                }
            }

            $odontologo->update([
                'estado' => !$odontologo->estado,
            ]);

            return back()->with(
                'success',
                $odontologo->estado
                    ? 'Odontólogo activado correctamente.'
                    : 'Odontólogo desactivado correctamente.'
            );
        }
}