<?php

namespace App\Http\Controllers;

use App\Models\OrdenTrabajo;

class DashboardController extends Controller
{
    public function index()
    {
        $ordenesPendientes = OrdenTrabajo::whereHas('estadoOrden', function ($query) {
            $query->where('nombre', 'Pendiente');
        })->count();

        $ordenesEnProceso = OrdenTrabajo::whereHas('estadoOrden', function ($query) {
            $query->where('nombre', 'En proceso');
        })->count();

        $ordenesEntregadas = OrdenTrabajo::whereHas('estadoOrden', function ($query) {
            $query->where('nombre', 'Entregado');
        })->count();

        $trabajosHoy = OrdenTrabajo::whereDate(
                'fecha_entrega_estimada',
                today()
            )
            ->whereHas('estadoOrden', function ($query) {
                $query->where('nombre', '!=', 'Cancelado');
            })
            ->count();

        return view('dashboard', compact(
            'ordenesPendientes',
            'ordenesEnProceso',
            'ordenesEntregadas',
            'trabajosHoy'
        ));
    }
}