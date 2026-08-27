<?php

namespace App\Http\Controllers;

use App\Models\Garantia;
use App\Models\Devolucion;
use App\Models\OrdenTrabajo;
use Illuminate\Http\Request;

class GarantiaDevolucionController extends Controller
{
    public function index(Request $request)
    {
         // ==========================================
        // CONTADORES DE GARANTÍAS
        // ==========================================

        // Vigentes con más de 15 días disponibles
        $garantiasActivas = Garantia::whereDate(
                'fecha_vencimiento',
                '>',
                now()->addDays(15)->toDateString()
            )
            ->count();

        // Vencen entre hoy y los próximos 15 días
        $garantiasPorVencer = Garantia::whereDate(
                'fecha_vencimiento',
                '>=',
                now()->toDateString()
            )
            ->whereDate(
                'fecha_vencimiento',
                '<=',
                now()->addDays(15)->toDateString()
            )
            ->count();

        // Ya vencieron
        $garantiasVencidas = Garantia::whereDate(
                'fecha_vencimiento',
                '<',
                now()->toDateString()
            )
            ->count();

        $totalDevoluciones = Devolucion::count();

        $totalRepeticiones = OrdenTrabajo::where(
            'tipo_orden',
            'Repeticion'
        )->count();

        // ==========================================
        // CONSULTA DE DEVOLUCIONES
        // ==========================================
        $query = Devolucion::with([
            'ordenTrabajo.paciente',
            'ordenTrabajo.odontologo',
            'ordenTrabajo.tipoProtesis',
            'garantia',
            'tecnicoResponsable.user',
            'usuarioRegistro',
            'repeticion',
        ]);


        // ==========================================
        // FILTRO POR TIPO
        // ==========================================
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }


        // ==========================================
        // FILTRO POR ESTADO
        // ==========================================
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }


        // ==========================================
        // FILTRO POR FECHA
        // ==========================================
        if ($request->filled('fecha')) {
            $query->whereDate(
                'fecha_devolucion',
                $request->fecha
            );
        }


        // ==========================================
        // FILTRO POR GARANTÍA
        // ==========================================
        if ($request->garantia === 'si') {
            $query->whereNotNull('garantia_id');
        }

        if ($request->garantia === 'no') {
            $query->whereNull('garantia_id');
        }


        // ==========================================
        // RESULTADOS
        // ==========================================
        $devoluciones = $query
            ->latest('fecha_devolucion')
            ->paginate(10)
            ->withQueryString();


        return view('garantias.index', compact(
            'garantiasActivas',
            'garantiasPorVencer',
            'garantiasVencidas',
            'totalDevoluciones',
            'totalRepeticiones',
            'devoluciones'
        ));
    }
}