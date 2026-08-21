<?php

namespace App\Http\Controllers;

use App\Models\OrdenTrabajo;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        $fecha = $request->input('fecha', now()->toDateString());

        $query = OrdenTrabajo::with([
            'odontologo.clinica',
            'paciente',
            'tipoProtesis',
            'estadoOrden',
            'etapaActual',
            'tecnicoActual.user',
        ])
        ->whereDate('fecha_entrega_estimada', $fecha)
        ->whereHas('estadoOrden', function ($query) {
            $query->where('nombre', '!=', 'Cancelado');
        });

        if ($request->filled('odontologo')) {
            $query->where('odontologo_id', $request->odontologo);
        }

        if ($request->filled('paciente')) {
            $query->where('paciente_id', $request->paciente);
        }

        if ($request->filled('estado')) {
            $query->where('estado_orden_id', $request->estado);
        }
        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }
        if (auth()->user()->role?->nombre === 'Tecnico') {
            $query->whereHas('tecnicoActual', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }

        if ($request->filled('categoria')) {
            $query->whereHas('tipoProtesis', function ($q) use ($request) {
                $q->where('categoria', $request->categoria);
            });
        }

        $ordenes = $query
            ->orderBy('fecha_ingreso')
            ->get();

        $removibles = $ordenes
            ->filter(fn ($orden) =>
                strtolower($orden->tipoProtesis?->categoria ?? '') === 'removible'
            )
            ->groupBy(fn ($orden) =>
                $orden->etapaActual?->nombre ?? 'Sin etapa asignada'
            );

        $fijas = $ordenes
            ->filter(fn ($orden) =>
                strtolower($orden->tipoProtesis?->categoria ?? '') === 'fija'
            )
            ->groupBy(fn ($orden) =>
                $orden->etapaActual?->nombre ?? 'Sin etapa asignada'
            );

        $ortodoncia = $ordenes
            ->filter(fn ($orden) =>
                strtolower($orden->tipoProtesis?->categoria ?? '') === 'ortodoncia'
            )
            ->groupBy(fn ($orden) =>
                $orden->etapaActual?->nombre ?? 'Sin etapa asignada'
            );

        $odontologos = \App\Models\Odontologo::where('estado', true)
            ->orderBy('nombre')
            ->get();

        $pacientes = \App\Models\Paciente::where('estado', true)
            ->orderBy('nombre')
            ->get();

        $estados = \App\Models\EstadoOrden::where('estado', true)
            ->orderBy('orden')
            ->get();

        return view('agenda.index', compact(
            'fecha',
            'removibles',
            'fijas',
            'ortodoncia',
            'odontologos',
            'pacientes',
            'estados'
        ));
    }
}