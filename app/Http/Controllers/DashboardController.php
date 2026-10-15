<?php

namespace App\Http\Controllers;

use App\Models\OrdenTrabajo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $usuario = auth()->user();
        $rol = $usuario?->role?->nombre;

        if (in_array($rol, ['Recepcion', 'Contabilidad', 'Técnico'], true)) {
            return redirect()->route('agenda.index');
        }
        if ($rol === 'Mensajero') {
            return redirect()->route('mensajeria.index');
        }
        abort_unless($rol === 'Administrador', 403, 'No tiene permisos para realizar esta acción.');

        $datos = $request->validate(['mes' => ['nullable', 'date_format:Y-m']]);
        $hoy = now('America/Guatemala');
        $fechaHoy = $hoy->toDateString();
        $fechaTexto = $hoy->format('d/m/Y');
        $mesSeleccionado = ($datos['mes'] ?? '') ?: $hoy->format('Y-m');
        $inicioMes = Carbon::createFromFormat('!Y-m', $mesSeleccionado, 'America/Guatemala');
        $finMes = $inicioMes->copy()->endOfMonth();
        $mesTexto = $inicioMes->copy()->locale('es')->isoFormat('MMMM [de] YYYY');
        $puedeGestionarOrdenes = true; // El panel continúa reservado al Administrador.

        // El período corresponde a la fecha de ingreso; los estados son los actuales.
        $ordenesMes = OrdenTrabajo::whereBetween('fecha_ingreso', [
            $inicioMes->toDateString(), $finMes->toDateString(),
        ]);
        $totalOrdenesMes = (clone $ordenesMes)->count();
        $contarEstado = fn (string $nombre): int => (clone $ordenesMes)
            ->whereHas('estadoOrden', fn ($q) => $q->where('nombre', $nombre))->count();

        $ordenesPendientes = $contarEstado('Pendiente');
        $ordenesEnProceso = $contarEstado('En proceso');
        $ordenesTerminadas = $contarEstado('Terminado');
        $ordenesEntregadas = $contarEstado('Entregado');
        $ordenesCanceladas = $contarEstado('Cancelado');

        $trabajosHoy = (clone $ordenesMes)
            ->whereDate('fecha_entrega_estimada', $fechaHoy)
            ->whereHas('estadoOrden', fn ($q) => $q->where('nombre', '!=', 'Cancelado'))->count();

        $ordenesAbiertasGeneral = OrdenTrabajo::whereHas('estadoOrden', function ($q) {
            $q->whereNotIn('nombre', ['Cancelado', 'Entregado']);
        });
        $pendientesMesesAnteriores = (clone $ordenesAbiertasGeneral)
            ->where('fecha_ingreso', '<', $inicioMes->toDateString())->count();

        $ordenesAbiertas = (clone $ordenesMes)->whereHas('estadoOrden', function ($q) {
            $q->whereNotIn('nombre', ['Cancelado', 'Entregado']);
        });
        $totalActivas = (clone $ordenesAbiertas)->count();
        $ordenesAtrasadas = (clone $ordenesAbiertas)
            ->whereDate('fecha_entrega_estimada', '<', $fechaHoy)->count();
        $porEntregarHoy = (clone $ordenesAbiertas)
            ->whereDate('fecha_entrega_estimada', $fechaHoy)->count();

        $nombresAreas = [
            'removible' => 'Removibles', 'fija' => 'Fijas',
            'cromo_cobalto' => 'Cromo Cobalto', 'ortodoncia' => 'Ortodoncia',
        ];
        $codigosAreas = ['removible' => 'PR', 'fija' => 'PF', 'cromo_cobalto' => 'CC', 'ortodoncia' => 'AO'];
        $conteosAreas = (clone $ordenesAbiertas)->select('area_trabajo')
            ->selectRaw('COUNT(*) as total')->groupBy('area_trabajo')->pluck('total', 'area_trabajo');
        $resumenAreas = collect($nombresAreas)->map(function ($nombre, $area) use ($conteosAreas, $codigosAreas, $totalActivas) {
            $total = (int) $conteosAreas->get($area, 0);
            return [
                'nombre' => $nombre, 'codigo' => $codigosAreas[$area], 'total' => $total,
                'porcentaje' => $totalActivas > 0 ? round(($total / $totalActivas) * 100) : 0,
            ];
        });
        $ordenesSinArea = $totalActivas - (int) $resumenAreas->sum('total');

        $resumenEtapas = (clone $ordenesAbiertas)->select('etapa_actual_id')
            ->selectRaw('COUNT(*) as cantidad_ordenes')->groupBy('etapa_actual_id')->with('etapaActual')->get()
            ->sortBy(fn ($orden) => $orden->etapaActual?->orden ?? PHP_INT_MAX)->values();

        $entregasPorAtender = (clone $ordenesAbiertas)
            ->whereDate('fecha_entrega_estimada', '<=', $fechaHoy)
            ->with(['paciente', 'odontologo', 'estadoOrden', 'etapaActual'])
            ->orderBy('fecha_entrega_estimada')->orderBy('id')->limit(8)->get();

        return view('dashboard', compact(
            'usuario', 'rol', 'fechaHoy', 'fechaTexto', 'puedeGestionarOrdenes',
            'mesSeleccionado', 'mesTexto', 'totalOrdenesMes', 'ordenesCanceladas',
            'pendientesMesesAnteriores', 'ordenesPendientes', 'ordenesEnProceso',
            'ordenesTerminadas', 'ordenesEntregadas', 'trabajosHoy', 'totalActivas',
            'ordenesAtrasadas', 'porEntregarHoy', 'nombresAreas', 'resumenAreas',
            'ordenesSinArea', 'resumenEtapas', 'entregasPorAtender'
        ));
    }
}
