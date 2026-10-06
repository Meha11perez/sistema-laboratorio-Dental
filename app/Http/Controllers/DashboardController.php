<?php

namespace App\Http\Controllers;

use App\Models\OrdenTrabajo;

class DashboardController extends Controller
{
    public function index()
    {
        $usuario = auth()->user();
        $rol = $usuario?->role?->nombre;

        if ($rol === 'Mensajero') {
            return redirect()->route('mensajeria.index');
        }

        if ($rol === 'Técnico') {
            return redirect()->route('agenda.index');
        }

        $hoy = now('America/Guatemala');
        $fechaHoy = $hoy->toDateString();
        $fechaTexto = $hoy->format('d/m/Y');

        $puedeGestionarOrdenes = in_array(
            $rol,
            ['Administrador', 'Recepcion'],
            true
        );

        $contarEstado = function (string $nombre): int {
            return OrdenTrabajo::whereHas(
                'estadoOrden',
                function ($query) use ($nombre) {
                    $query->where('nombre', $nombre);
                }
            )->count();
        };

        $ordenesPendientes = $contarEstado('Pendiente');
        $ordenesEnProceso = $contarEstado('En proceso');
        $ordenesTerminadas = $contarEstado('Terminado');
        $ordenesEntregadas = $contarEstado('Entregado');

        // Órdenes programadas hoy, excepto canceladas.
        $trabajosHoy = OrdenTrabajo::whereDate(
            'fecha_entrega_estimada',
            $fechaHoy
        )
            ->whereHas('estadoOrden', function ($query) {
                $query->where('nombre', '!=', 'Cancelado');
            })
            ->count();

        // Incluye las terminadas que todavía no se han entregado.
        $ordenesAbiertas = OrdenTrabajo::whereHas(
            'estadoOrden',
            function ($query) {
                $query->whereNotIn(
                    'nombre',
                    ['Cancelado', 'Entregado']
                );
            }
        );

        $totalActivas = (clone $ordenesAbiertas)->count();

        $ordenesAtrasadas = (clone $ordenesAbiertas)
            ->whereDate('fecha_entrega_estimada', '<', $fechaHoy)
            ->count();

        $porEntregarHoy = (clone $ordenesAbiertas)
            ->whereDate('fecha_entrega_estimada', $fechaHoy)
            ->count();

        $nombresAreas = [
            'removible' => 'Removibles',
            'fija' => 'Fijas',
            'cromo_cobalto' => 'Cromo Cobalto',
            'ortodoncia' => 'Ortodoncia',
        ];

        $codigosAreas = [
            'removible' => 'PR',
            'fija' => 'PF',
            'cromo_cobalto' => 'CC',
            'ortodoncia' => 'AO',
        ];

        $conteosAreas = (clone $ordenesAbiertas)
            ->select('area_trabajo')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('area_trabajo')
            ->pluck('total', 'area_trabajo');

        $resumenAreas = collect($nombresAreas)->map(
            function ($nombre, $area) use (
                $conteosAreas,
                $codigosAreas,
                $totalActivas
            ) {
                $total = (int) $conteosAreas->get($area, 0);

                return [
                    'nombre' => $nombre,
                    'codigo' => $codigosAreas[$area],
                    'total' => $total,
                    'porcentaje' => $totalActivas > 0
                        ? round(($total / $totalActivas) * 100)
                        : 0,
                ];
            }
        );

        $ordenesSinArea = $totalActivas
            - (int) $resumenAreas->sum('total');

        $resumenEtapas = (clone $ordenesAbiertas)
            ->select('etapa_actual_id')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('etapa_actual_id')
            ->with('etapaActual')
            ->get()
            ->sortBy(function ($orden) {
                return $orden->etapaActual?->orden ?? PHP_INT_MAX;
            })
            ->values();

        $entregasPorAtender = (clone $ordenesAbiertas)
            ->whereDate('fecha_entrega_estimada', '<=', $fechaHoy)
            ->with([
                'paciente',
                'odontologo',
                'estadoOrden',
                'etapaActual',
            ])
            ->orderBy('fecha_entrega_estimada')
            ->orderBy('id')
            ->limit(8)
            ->get();

        return view('dashboard', compact(
            'usuario',
            'rol',
            'fechaHoy',
            'fechaTexto',
            'puedeGestionarOrdenes',
            'ordenesPendientes',
            'ordenesEnProceso',
            'ordenesTerminadas',
            'ordenesEntregadas',
            'trabajosHoy',
            'totalActivas',
            'ordenesAtrasadas',
            'porEntregarHoy',
            'nombresAreas',
            'resumenAreas',
            'ordenesSinArea',
            'resumenEtapas',
            'entregasPorAtender'
        ));
    }
}