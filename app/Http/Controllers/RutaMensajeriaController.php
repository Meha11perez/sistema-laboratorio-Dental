<?php

namespace App\Http\Controllers;

use App\Models\RutaMensajeria;
use App\Models\DetalleMensajeria;
use App\Models\Odontologo;
use App\Models\Clinica;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class RutaMensajeriaController extends Controller{
    private function esMensajero(): bool
    {
        return auth()->user()?->role?->nombre === 'Mensajero';
    }

    private function verificarRutaMensajero(RutaMensajeria $ruta): void
    {
        if (
            $this->esMensajero() &&
            $ruta->mensajero_id !== auth()->id()
        ) {
            abort(403, 'No tiene permiso para acceder a esta ruta.');
        }
    }
    public function index(Request $request)
    {
        $query = RutaMensajeria::with('mensajero')
            ->withCount('detalles');

       if ($this->esMensajero()) {
            $query->where('mensajero_id', auth()->id())
                ->whereDate(
                    'fecha',
                    '>=',
                    Carbon::today('America/Guatemala')->subMonths(3)
                )
                ->whereDate(
                    'fecha',
                    '<=',
                    Carbon::today('America/Guatemala')
                );
        }

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
        $mensajeros = User::whereHas('role', function ($query) {
            $query->where('nombre', 'Mensajero')
                ->where('estado', true);
        })
        ->orderBy('name')
        ->get();

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
                    Rule::exists('users', 'id')->where(function ($query) {
                        $query->whereIn('role_id', function ($subquery) {
                        $subquery->select('id')
                            ->from('roles')
                            ->where('nombre', 'Mensajero')
                            ->where('estado', true);
                        });
                    }),
                 ],

            'fecha' => [
                'required',
                'date',
                'after_or_equal:today',
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
        $this->verificarRutaMensajero($ruta);

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
        $this->verificarRutaMensajero($ruta);

        if ($ruta->estado !== 'Pendiente') {
            return redirect()
                ->route('mensajeria.show', $ruta)
                ->with(
                    'error',
                    'Solo se pueden iniciar rutas pendientes.'
                );
        }

        $hoy = Carbon::today('America/Guatemala');

        // No permitir iniciar rutas futuras
        if (!$ruta->fecha->isSameDay($hoy)) {
           return redirect()
                ->route('mensajeria.show', $ruta)
                ->with(
                    'error',
                    'Esta ruta solo puede iniciarse en la fecha programada: '
                    . $ruta->fecha->format('d/m/Y') . '.'
            );
        }

        $ruta->update([
            'estado' => 'En ruta',
            'hora_salida' => now('America/Guatemala')->format('H:i:s'),
            'hora_regreso' => null,
        ]);

        return redirect()
            ->route('mensajeria.show', $ruta)
            ->with(
                'success',
                'Ruta iniciada correctamente.'
            );
    }
    public function finalizar(RutaMensajeria $ruta)
    {
        $this->verificarRutaMensajero($ruta);

        // Solo una ruta en curso puede finalizarse
        if ($ruta->estado !== 'En ruta') {
            return redirect()
                ->route('mensajeria.show', $ruta)
                ->with(
                    'error',
                    'Solo se pueden finalizar rutas que estén en curso.'
                );
        }

        // No puede finalizar mientras tenga visitas pendientes
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

        // Finalizar ruta
        $ruta->update([
            'estado' => 'Finalizada',
            'hora_regreso' => now('America/Guatemala')->format('H:i:s'),
        ]);

        return redirect()
            ->route('mensajeria.show', $ruta)
            ->with(
                'success',
                'Ruta finalizada correctamente.'
            );
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
       
        if ($this->esMensajero()) {
            $query->whereHas('rutaMensajeria', function ($q) {
                $q->where('mensajero_id', auth()->id())
                    ->whereDate(
                        'fecha',
                        '>=',
                        Carbon::today('America/Guatemala')->subMonths(3)
                    )
                    ->whereDate(
                        'fecha',
                        '<=',
                        Carbon::today('America/Guatemala')
                    );
            });
        }
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

        $this->verificarRutaMensajero($detalle->rutaMensajeria);

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
            
       if ($this->esMensajero()) {
            $query->whereHas('rutaMensajeria', function ($q) {
                $q->where('mensajero_id', auth()->id())
                    ->whereDate(
                        'fecha',
                        '>=',
                        Carbon::today('America/Guatemala')->subMonths(3)
                    )
                    ->whereDate(
                        'fecha',
                        '<=',
                        Carbon::today('America/Guatemala')
                    );
            });
        }

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

        $this->verificarRutaMensajero($detalle->rutaMensajeria);

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