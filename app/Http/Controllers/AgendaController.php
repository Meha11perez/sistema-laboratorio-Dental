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
        | Siempre consulta únicamente la agenda del día actual.
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


        /*
        |--------------------------------------------------------------------------
        | CONSULTA BASE
        |--------------------------------------------------------------------------
        */
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
        | El técnico únicamente puede visualizar
        | las órdenes asignadas actualmente a él.
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
        | FILTROS ADMINISTRATIVOS
        |--------------------------------------------------------------------------
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


            /*
            |--------------------------------------------------------------------------
            | ÁREA
            |--------------------------------------------------------------------------
            | Se utiliza area_trabajo y ya no
            | tipo_protesis.categoria.
            */
            if ($request->filled('area')) {

                $areasValidas = [
                    'removible',
                    'fija',
                    'cromo_cobalto',
                    'ortodoncia',
                ];

                if (
                    in_array(
                        $request->area,
                        $areasValidas,
                        true
                    )
                ) {

                    $query->where(
                        'area_trabajo',
                        $request->area
                    );
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | PRIORIDAD
        |--------------------------------------------------------------------------
        */
        if ($request->filled('prioridad')) {

            $query->where(
                'prioridad',
                $request->prioridad
            );
        }


        /*
        |--------------------------------------------------------------------------
        | OBTENER ÓRDENES
        |--------------------------------------------------------------------------
        */
        $ordenes = $query
            ->orderByDesc('prioridad')
            ->orderBy('fecha_ingreso')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | CLASIFICACIÓN POR ÁREA
        |--------------------------------------------------------------------------
        | Ya no dependemos de tipoProtesis->categoria.
        |
        | Las 4 áreas son independientes:
        |
        | - Prótesis Removibles
        | - Prótesis Fijas
        | - Cromo Cobalto
        | - Aparatos de Ortodoncia
        |--------------------------------------------------------------------------
        */


        // ==========================================
        // PRÓTESIS REMOVIBLES
        // ==========================================
        $removibles = $ordenes
            ->filter(
                fn ($orden) =>
                    $orden->area_trabajo ===
                    'removible'
            )
            ->groupBy(
                fn ($orden) =>
                    $orden->etapaActual?->nombre
                    ?? 'Sin etapa asignada'
            );


        // ==========================================
        // PRÓTESIS FIJAS
        // ==========================================
        $fijas = $ordenes
            ->filter(
                fn ($orden) =>
                    $orden->area_trabajo ===
                    'fija'
            )
            ->groupBy(
                fn ($orden) =>
                    $orden->etapaActual?->nombre
                    ?? 'Sin etapa asignada'
            );


        // ==========================================
        // CROMO COBALTO
        // ==========================================
        $cromoCobalto = $ordenes
            ->filter(
                fn ($orden) =>
                    $orden->area_trabajo ===
                    'cromo_cobalto'
            )
            ->groupBy(
                fn ($orden) =>
                    $orden->etapaActual?->nombre
                    ?? 'Sin etapa asignada'
            );


        // ==========================================
        // APARATOS DE ORTODONCIA
        // ==========================================
        $ortodoncia = $ordenes
            ->filter(
                fn ($orden) =>
                    $orden->area_trabajo ===
                    'ortodoncia'
            )
            ->groupBy(
                fn ($orden) =>
                    $orden->etapaActual?->nombre
                    ?? 'Sin etapa asignada'
            );


        /*
        |--------------------------------------------------------------------------
        | DATOS PARA FILTROS
        |--------------------------------------------------------------------------
        */
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


        /*
        |--------------------------------------------------------------------------
        | VISTA
        |--------------------------------------------------------------------------
        */
        return view(
            'agenda.index',
            compact(
                'fecha',
                'removibles',
                'fijas',
                'cromoCobalto',
                'ortodoncia',
                'odontologos',
                'pacientes',
                'estados',
                'esTecnico'
            )
        );
    }
}