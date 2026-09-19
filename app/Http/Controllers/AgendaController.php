<?php

namespace App\Http\Controllers;

use App\Models\OrdenTrabajo;
use App\Models\Odontologo;
use App\Models\Paciente;
use App\Models\EstadoOrden;
use App\Models\Tecnico;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        $usuario = auth()->user();

        $esTecnico =
            $usuario?->role?->nombre === 'Técnico';

        /*
        |--------------------------------------------------------------------------
        | FECHA
        |--------------------------------------------------------------------------
        | Técnico:
        | Siempre ve únicamente la agenda de HOY.
        |
        | Administrador / Recepción:
        | Pueden consultar cualquier fecha.
        */
        if ($esTecnico) {

            $fecha = Carbon::today(
                'America/Guatemala'
            )->toDateString();

        } else {

            $fecha = $request->input(
                'fecha',
                Carbon::today(
                    'America/Guatemala'
                )->toDateString()
            );
        }


        $query = OrdenTrabajo::with([
            'odontologo.clinica',
            'paciente',
            'tipoProtesis',
            'estadoOrden',
            'etapaActual',
            'tecnicoActual.user',
        ])
        ->whereDate(
            'fecha_entrega_estimada',
            $fecha
        )
        ->whereHas(
            'estadoOrden',
            function ($query) {
                $query->where(
                    'nombre',
                    '!=',
                    'Cancelado'
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | TÉCNICO
        |--------------------------------------------------------------------------
        | Solo ve órdenes asignadas actualmente a él.
        */
        if ($esTecnico) {

            $tecnico = Tecnico::where(
                'user_id',
                $usuario->id
            )->first();

            if (!$tecnico) {
                abort(
                    403,
                    'El usuario no tiene un técnico asociado.'
                );
            }

            $query->where(
                'tecnico_actual_id',
                $tecnico->id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTROS
        |--------------------------------------------------------------------------
        | Los filtros administrativos solamente aplican a
        | Administrador / Recepción.
        */
        if (!$esTecnico) {

            if ($request->filled('odontologo')) {
                $query->where(
                    'odontologo_id',
                    $request->odontologo
                );
            }

            if ($request->filled('paciente')) {
                $query->where(
                    'paciente_id',
                    $request->paciente
                );
            }

            if ($request->filled('estado')) {
                $query->where(
                    'estado_orden_id',
                    $request->estado
                );
            }
        }


        if ($request->filled('prioridad')) {
            $query->where(
                'prioridad',
                $request->prioridad
            );
        }


        if ($request->filled('categoria')) {

            $query->whereHas(
                'tipoProtesis',
                function ($q) use ($request) {

                    $q->where(
                        'categoria',
                        $request->categoria
                    );
                }
            );
        }


        $ordenes = $query
            ->orderBy('fecha_ingreso')
            ->get();


        $removibles = $ordenes
            ->filter(
                fn ($orden) =>
                    strtolower(
                        $orden->tipoProtesis?->categoria ?? ''
                    ) === 'removible'
            )
            ->groupBy(
                fn ($orden) =>
                    $orden->etapaActual?->nombre
                    ?? 'Sin etapa asignada'
            );


        $fijas = $ordenes
            ->filter(
                fn ($orden) =>
                    strtolower(
                        $orden->tipoProtesis?->categoria ?? ''
                    ) === 'fija'
            )
            ->groupBy(
                fn ($orden) =>
                    $orden->etapaActual?->nombre
                    ?? 'Sin etapa asignada'
            );


        $ortodoncia = $ordenes
            ->filter(
                fn ($orden) =>
                    strtolower(
                        $orden->tipoProtesis?->categoria ?? ''
                    ) === 'ortodoncia'
            )
            ->groupBy(
                fn ($orden) =>
                    $orden->etapaActual?->nombre
                    ?? 'Sin etapa asignada'
            );


        $odontologos = Odontologo::where(
            'estado',
            true
        )
        ->orderBy('nombre')
        ->get();


        $pacientes = Paciente::where(
            'estado',
            true
        )
        ->orderBy('nombre')
        ->get();


        $estados = EstadoOrden::where(
            'estado',
            true
        )
        ->orderBy('orden')
        ->get();


        return view(
            'agenda.index',
            compact(
                'fecha',
                'removibles',
                'fijas',
                'ortodoncia',
                'odontologos',
                'pacientes',
                'estados',
                'esTecnico'
            )
        );
    }
}