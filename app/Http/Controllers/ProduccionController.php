<?php

namespace App\Http\Controllers;

use App\Models\OrdenTrabajo;
use App\Models\EstadoOrden;
use App\Models\EtapaProduccion;
use App\Models\Tecnico;
use Illuminate\Http\Request;

class ProduccionController extends Controller
{
    public function index(Request $request)
    {
        // ==========================================
        // SEGURIDAD
        // ==========================================
        $rol = auth()->user()?->role?->nombre;

        if (!in_array($rol, ['Administrador', 'Recepcion'], true)) {
            abort(
                403,
                'No tiene permiso para consultar el módulo de producción.'
            );
        }


        // ==========================================
        // ESTADOS QUE YA NO FORMAN PARTE
        // DE PRODUCCIÓN ACTIVA
        // ==========================================
        $estadosCerrados = [
            'Terminado',
            'Entregado',
            'Cancelado',
        ];


        // ==========================================
        // SIN ASIGNAR
        // ==========================================
        $sinAsignar = OrdenTrabajo::whereNull('tecnico_actual_id')
            ->whereHas('estadoOrden', function ($query) use ($estadosCerrados) {

                $query->whereNotIn(
                    'nombre',
                    $estadosCerrados
                );
            })
            ->count();


        // ==========================================
        // EN PRODUCCIÓN
        // ==========================================
        $enProduccion = OrdenTrabajo::whereHas(
                'estadoOrden',
                function ($query) {

                    $query->where(
                        'nombre',
                        'En proceso'
                    );
                }
            )
            ->count();


        // ==========================================
        // PRÓXIMAS A ENTREGAR
        // Hoy + próximos 2 días
        // ==========================================
        $proximasEntrega = OrdenTrabajo::whereNotNull(
                'fecha_entrega_estimada'
            )
            ->whereDate(
                'fecha_entrega_estimada',
                '>=',
                now()->toDateString()
            )
            ->whereDate(
                'fecha_entrega_estimada',
                '<=',
                now()->addDays(2)->toDateString()
            )
            ->whereHas(
                'estadoOrden',
                function ($query) use ($estadosCerrados) {

                    $query->whereNotIn(
                        'nombre',
                        $estadosCerrados
                    );
                }
            )
            ->count();


        // ==========================================
        // ATRASADAS
        // Fecha estimada ya pasó y siguen activas
        // ==========================================
        $atrasadas = OrdenTrabajo::whereNotNull(
                'fecha_entrega_estimada'
            )
            ->whereDate(
                'fecha_entrega_estimada',
                '<',
                now()->toDateString()
            )
            ->whereHas(
                'estadoOrden',
                function ($query) use ($estadosCerrados) {

                    $query->whereNotIn(
                        'nombre',
                        $estadosCerrados
                    );
                }
            )
            ->count();


        // ==========================================
        // CONSULTA PRINCIPAL
        // SOLO PRODUCCIÓN ACTIVA
        // ==========================================
        $query = OrdenTrabajo::with([
            'paciente',
            'tipoProtesis',
            'estadoOrden',
            'etapaActual',
            'tecnicoActual.user',
        ])
        ->whereHas(
            'estadoOrden',
            function ($query) use ($estadosCerrados) {

                $query->whereNotIn(
                    'nombre',
                    $estadosCerrados
                );
            }
        );


        // ==========================================
        // BUSCAR
        // ==========================================
        if ($request->filled('buscar')) {

            $buscar = $request->buscar;

            $query->where(function ($q) use ($buscar) {

                $q->where(
                    'codigo',
                    'like',
                    '%' . $buscar . '%'
                );

                $q->orWhereHas(
                    'paciente',
                    function ($paciente) use ($buscar) {

                        $paciente->where(
                            'nombre',
                            'like',
                            '%' . $buscar . '%'
                        );
                    }
                );
            });
        }


        // ==========================================
        // ETAPA
        // ==========================================
        if ($request->filled('etapa')) {

            $query->where(
                'etapa_actual_id',
                $request->etapa
            );
        }


        // ==========================================
        // TÉCNICO
        // ==========================================
        if ($request->filled('tecnico')) {

            if ($request->tecnico === 'sin_asignar') {

                $query->whereNull(
                    'tecnico_actual_id'
                );

            } else {

                $query->where(
                    'tecnico_actual_id',
                    $request->tecnico
                );
            }
        }


        // ==========================================
        // ESTADO
        // ==========================================
        if ($request->filled('estado')) {

            $query->where(
                'estado_orden_id',
                $request->estado
            );
        }


        // ==========================================
        // SOLO ATRASADAS
        // ==========================================
        if ($request->filtro === 'atrasadas') {

            $query->whereDate(
                'fecha_entrega_estimada',
                '<',
                now()->toDateString()
            );
        }


        // ==========================================
        // LISTADO
        // ==========================================
        $ordenes = $query
            ->orderByRaw(
                'fecha_entrega_estimada IS NULL'
            )
            ->orderBy('fecha_entrega_estimada')
            ->orderByDesc('prioridad')
            ->paginate(10)
            ->withQueryString();


        // ==========================================
        // FILTROS
        // ==========================================
        $estados = EstadoOrden::where('estado', true)
            ->whereNotIn(
                'nombre',
                $estadosCerrados
            )
            ->orderBy('orden')
            ->get();


        $etapas = EtapaProduccion::where(
                'estado',
                true
            )
            ->orderBy('orden')
            ->get();


        $tecnicos = Tecnico::with('user')
            ->where('estado', true)
            ->get();


        return view(
            'produccion.index',
            compact(
                'ordenes',
                'estados',
                'etapas',
                'tecnicos',
                'sinAsignar',
                'enProduccion',
                'proximasEntrega',
                'atrasadas'
            )
        );
    }
}