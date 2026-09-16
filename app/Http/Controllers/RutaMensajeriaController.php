<?php

namespace App\Http\Controllers;

use App\Models\RutaMensajeria;
use App\Models\DetalleMensajeria;
use App\Models\Odontologo;
use App\Models\Clinica;
use App\Models\User;
use Illuminate\Http\Request;

class RutaMensajeriaController extends Controller
{
    public function index(Request $request)
    {
        $query = RutaMensajeria::with('mensajero')
            ->withCount('detalles');

        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $rutas = $query
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(5)
            ->withQueryString();

        return view(
            'mensajeria.index',
            compact('rutas')
        );
    }
    public function create()
    {
        /*
         * Por ahora cargamos usuarios activos.
         * Después podemos filtrar exclusivamente
         * por rol Mensajero si tu estructura de roles
         * ya lo permite.
         */
        $mensajeros = User::orderBy('name')->get();

        return view(
            'mensajeria.create',
            compact('mensajeros')
        );
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'mensajero_id' => [
                'required',
                'exists:users,id',
            ],

            'fecha' => [
                'required',
                'date',
            ],

            'observaciones' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $ruta = RutaMensajeria::create([
            'mensajero_id' => $datos['mensajero_id'],
            'fecha' => $datos['fecha'],
            // Toda ruta nueva inicia pendiente
            'estado' => 'Pendiente',
            // Se llenan automáticamente
            'hora_salida' => null,
            'hora_regreso' => null,

            'observaciones' =>
                $datos['observaciones'] ?? null,
        ]);

        return redirect()
            ->route('mensajeria.show', $ruta)
            ->with(
                'success',
                'Ruta de mensajería registrada correctamente.'
            );
    }
    public function show(RutaMensajeria $ruta)
    {
        $ruta->load([
            'mensajero',

            'detalles.ordenTrabajo.paciente',
            'detalles.odontologo',
            'detalles.clinica',
            'detalles.visitaReprogramada',

        ]);

        return view(
            'mensajeria.show',
            compact('ruta')
        );
    }
    public function iniciar(RutaMensajeria $ruta)
    {
        if ($ruta->estado !== 'Pendiente') {
            return redirect()
                ->route('mensajeria.show', $ruta)
                ->with('error', 'Solo se pueden iniciar rutas pendientes.');
        }

        $ruta->update([
            'estado' => 'En ruta',
            'hora_salida' => now()->format('H:i:s'),
            'hora_regreso' => null,
        ]);

        return redirect()
            ->route('mensajeria.show', $ruta)
            ->with('success', 'Ruta iniciada correctamente.');
    }
    public function finalizar(RutaMensajeria $ruta)
    {
        if ($ruta->estado !== 'En ruta') {
            return redirect()
                ->route('mensajeria.show', $ruta)
                ->with('error', 'Solo se pueden finalizar rutas que estén en curso.');
        }

        $pendientes = $ruta->detalles()
            ->where('estado', 'Pendiente')
            ->exists();

        if ($pendientes) {
            return redirect()
                ->route('mensajeria.show', $ruta)
                ->with(
                    'error',
                    'No puede finalizar la ruta mientras existan visitas pendientes.'
                );
        }

        $ruta->update([
            'estado' => 'Finalizada',
            'hora_regreso' => now()->format('H:i:s'),
        ]);

        return redirect()
            ->route('mensajeria.show', $ruta)
            ->with('success', 'Ruta finalizada correctamente.');
        }
    public function recolecciones(Request $request)
    {
        $query = DetalleMensajeria::with([
            'rutaMensajeria.mensajero',
            'ordenTrabajo',
            'odontologo',
            'clinica',
        ])
            ->where('tipo_movimiento', 'Recolección');

        // FILTRO POR FECHA
        if ($request->filled('fecha')) {
            $query->whereHas(
                'rutaMensajeria',
                function ($q) use ($request) {
                    $q->whereDate(
                        'fecha',
                        $request->fecha
                    );
                }
            );
        }

        // FILTRO POR ESTADO
        if ($request->filled('estado')) {
            $query->where(
                'estado',
                $request->estado
            );
        }

        // FILTRO POR ODONTÓLOGO
        if ($request->filled('odontologo_id')) {
            $query->where(
                'odontologo_id',
                $request->odontologo_id
            );
        }

        // FILTRO POR CLÍNICA
        if ($request->filled('clinica_id')) {
            $query->where(
                'clinica_id',
                $request->clinica_id
            );
        }

        $recolecciones = $query
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $odontologos = Odontologo::where(
            'estado',
            true
        )
            ->orderBy('nombre')
            ->get();

        $clinicas = Clinica::where(
            'estado',
            true
        )
            ->orderBy('nombre')
            ->get();

        return view(
            'mensajeria.recolecciones',
            compact(
                'recolecciones',
                'odontologos',
                'clinicas'
            )
        );
    }
    public function showRecoleccion(\App\Models\DetalleMensajeria $detalle)
    {
        if ($detalle->tipo_movimiento !== 'Recolección') {
            abort(404);
        }

        $detalle->load([
            'rutaMensajeria.mensajero',
            'ordenTrabajo.paciente',
            'odontologo',
            'clinica',
            'visitaOrigen',
            'visitaReprogramada',
        ]);

        return view(
            'mensajeria.recolecciones.show',
            compact('detalle')
        );
    }
    public function entregas(Request $request)
    {
        $query = DetalleMensajeria::with([
            'rutaMensajeria.mensajero',
            'ordenTrabajo.paciente',
            'odontologo',
            'clinica',
        ])
            ->where('tipo_movimiento', 'Entrega');

        // FILTRO POR FECHA
        if ($request->filled('fecha')) {
            $query->whereHas(
                'rutaMensajeria',
                function ($q) use ($request) {
                    $q->whereDate(
                        'fecha',
                        $request->fecha
                    );
                }
            );
        }

        // FILTRO POR ESTADO
        if ($request->filled('estado')) {
            $query->where(
                'estado',
                $request->estado
            );
        }

        // FILTRO POR ODONTÓLOGO
        if ($request->filled('odontologo_id')) {
            $query->where(
                'odontologo_id',
                $request->odontologo_id
            );
        }

        // FILTRO POR CLÍNICA
        if ($request->filled('clinica_id')) {
            $query->where(
                'clinica_id',
                $request->clinica_id
            );
        }

        $entregas = $query
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $odontologos = Odontologo::where(
            'estado',
            true
        )
            ->orderBy('nombre')
            ->get();

        $clinicas = Clinica::where(
            'estado',
            true
        )
            ->orderBy('nombre')
            ->get();

        return view(
            'mensajeria.entregas',
            compact(
                'entregas',
                'odontologos',
                'clinicas'
            )
        );
    }
    public function showEntrega(DetalleMensajeria $detalle)
    {
        if ($detalle->tipo_movimiento !== 'Entrega') {
            abort(404);
        }

        $detalle->load([
            'rutaMensajeria.mensajero',
            'ordenTrabajo.paciente',
            'odontologo',
            'clinica',
            'visitaOrigen',
            'visitaReprogramada',
        ]);

        return view(
            'mensajeria.entregas.show',
            compact('detalle')
        );
    }
}