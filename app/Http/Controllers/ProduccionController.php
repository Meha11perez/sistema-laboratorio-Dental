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

        if (!in_array(
            $rol,
            ['Administrador', 'Recepcion'],
            true
        )) {

            abort(
                403,
                'No tiene permiso para consultar el módulo de producción.'
            );
        }


        // ==========================================
        // ESTADOS CERRADOS
        // Ya no forman parte de producción activa
        // ==========================================
        $estadosCerrados = [
            'Terminado',
            'Entregado',
            'Cancelado',
        ];


        // ==========================================
        // ÁREAS VÁLIDAS
        // ==========================================
        $areasValidas = [
            'removible',
            'fija',
            'cromo_cobalto',
            'ortodoncia',
        ];


        // ==========================================
        // SIN ASIGNAR
        // ==========================================
        $sinAsignar = OrdenTrabajo::whereNull(
                'tecnico_actual_id'
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
        // Busca por:
        // - Código general ORD
        // - Código del área PR/PF/CC/AO
        // - Nombre del paciente
        // - Apellido del paciente
        // ==========================================
        if ($request->filled('buscar')) {

            $buscar = trim(
                $request->buscar
            );


            $query->where(
                function ($q) use ($buscar) {

                    $q->where(
                        'codigo',
                        'like',
                        '%' . $buscar . '%'
                    );

                    $q->orWhere(
                        'codigo_area',
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

                            $paciente->orWhere(
                                'apellido',
                                'like',
                                '%' . $buscar . '%'
                            );
                        }
                    );
                }
            );
        }


        // ==========================================
        // ÁREA
        // ==========================================
        if (
            $request->filled('area')
            &&
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

            if (
                $request->tecnico ===
                'sin_asignar'
            ) {

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
        // SOLO ATRASADAS
        // ==========================================
        if (
            $request->filtro ===
            'atrasadas'
        ) {

            $query->whereNotNull(
                'fecha_entrega_estimada'
            );

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
            ->orderBy(
                'fecha_entrega_estimada'
            )
            ->orderByDesc(
                'prioridad'
            )
            ->paginate(10)
            ->withQueryString();


        // ==========================================
        // ESTADOS PARA FILTRO
        // ==========================================
        $estados = EstadoOrden::where(
                'estado',
                true
            )
            ->whereNotIn(
                'nombre',
                $estadosCerrados
            )
            ->orderBy(
                'orden'
            )
            ->get();


        // ==========================================
        // ETAPAS PARA FILTRO
        // ==========================================
        $etapas = EtapaProduccion::where(
                'estado',
                true
            )
            ->orderBy(
                'orden'
            )
            ->get();


        // ==========================================
        // TÉCNICOS PARA FILTRO
        // ==========================================
        $tecnicos = Tecnico::with(
                'user'
            )
            ->where(
                'estado',
                true
            )
            ->get();


        // ==========================================
        // VISTA
        // ==========================================
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