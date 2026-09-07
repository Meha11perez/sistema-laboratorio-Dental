<?php

namespace App\Http\Controllers;

use App\Models\RutaMensajeria;
use App\Models\User;
use Illuminate\Http\Request;

class RutaMensajeriaController extends Controller
{
    public function index(Request $request)
    {
        $query = RutaMensajeria::with([
            'mensajero',
            'detalles',
        ]);

        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $rutas = $query
            ->orderByDesc('fecha')
            ->paginate(15)
            ->withQueryString();

        return view('mensajeria.index', compact('rutas'));
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
    public function reprogramarForm(DetalleMensajeria $detalle)
    {
        if ($detalle->estado !== 'Reprogramada') {
            return redirect()
                ->route(
                    'mensajeria.show',
                    $detalle->ruta_mensajeria_id
                )
                ->with(
                    'error',
                    'Solo se pueden reprogramar visitas marcadas como Reprogramada.'
                );
        }

        $rutasDisponibles = RutaMensajeria::whereIn(
                'estado',
                ['Pendiente', 'En ruta']
            )
            ->where(
                'id',
                '!=',
                $detalle->ruta_mensajeria_id
            )
            ->orderBy('fecha')
            ->get();

            return view(
                'mensajeria.detalles.reprogramar',
                compact(
                    'detalle',
                    'rutasDisponibles'
            )
        );
    }
    public function reprogramar(Request $request,
     DetalleMensajeria $detalle
   
        ) {
            $datos = $request->validate([
                'ruta_mensajeria_id' => [
                    'required',
                    'exists:rutas_mensajeria,id',
                ],
            ]);


        $nuevaRuta = RutaMensajeria::findOrFail(
            $datos['ruta_mensajeria_id']
        );


        if (
            $nuevaRuta->estado === 'Finalizada' ||
            $nuevaRuta->estado === 'Cancelada'
        ) {
            return redirect()
                ->back()
                ->withErrors([
                    'ruta_mensajeria_id' =>
                        'No se puede mover la visita a una ruta finalizada o cancelada.',
                ]);
        }


        $siguienteOrden = $nuevaRuta
            ->detalles()
            ->max('orden_visita');

        $siguienteOrden = $siguienteOrden
            ? $siguienteOrden + 1
            : 1;


        DetalleMensajeria::create([
            'ruta_mensajeria_id' =>
                $nuevaRuta->id,

            'orden_trabajo_id' =>
                $detalle->orden_trabajo_id,

            'odontologo_id' =>
                $detalle->odontologo_id,

            'clinica_id' =>
                $detalle->clinica_id,

            'tipo_movimiento' =>
                $detalle->tipo_movimiento,

            'orden_visita' =>
                $siguienteOrden,

            'direccion_referencia' =>
                $detalle->direccion_referencia,

            'estado' =>
                'Pendiente',

            'hora_realizada' =>
                null,

            'recibido_por' =>
                null,

            'observaciones' =>
                'Visita reprogramada desde la ruta anterior. '
                . ($detalle->observaciones ?? ''),
        ]);


        return redirect()
            ->route(
                'mensajeria.show',
                $nuevaRuta
            )
            ->with(
                'success',
                'Visita reprogramada correctamente.'
            );
    }
}