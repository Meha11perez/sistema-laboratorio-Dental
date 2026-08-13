<?php

namespace App\Http\Controllers;
use App\Models\HistorialProduccion;
use App\Models\HistorialEstadoOrden;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\OrdenTrabajo;
use App\Models\Odontologo;
use App\Models\Paciente;
use App\Models\TipoProtesis;
use App\Models\EstadoOrden;
use App\Models\EtapaProduccion;
use App\Models\Tecnico;


class OrdenTrabajoController extends Controller
{
    public function index()
    {
        $ordenes = OrdenTrabajo::with([
            'paciente',
            'odontologo',
            'tipoProtesis',
            'estadoOrden',
            'tecnicoActual', 
        ])
        ->latest()
        ->paginate(10);

        return view('ordenes.index', compact('ordenes'));
    }

    public function create()
    {
        $odontologos = Odontologo::where('estado', true)
            ->orderBy('nombre')
            ->get();

        $pacientes = Paciente::where('estado', true)
            ->orderBy('nombre')
            ->get();

        $tiposProtesis = TipoProtesis::where('estado', true)
            ->orderBy('categoria')
            ->orderBy('nombre')
            ->get();

        $estados = EstadoOrden::where('estado', true)
            ->orderBy('orden')
            ->get();

        $etapas = EtapaProduccion::where('estado', true)
            ->orderBy('orden')
            ->get();

        $tecnicos = Tecnico::with('user')
            ->where('estado', true)
            ->get();

        return view('ordenes.create', compact(
            'odontologos',
            'pacientes',
            'tiposProtesis',
            'estados',
            'etapas',
            'tecnicos'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo_caja' => 'nullable|string|max:20',
            'odontologo_id' => 'required|exists:odontologos,id',
            'paciente_id' => 'required|exists:pacientes,id',
            'tipo_protesis_id' => 'required|exists:tipos_protesis,id',
            'etapa_actual_id' => 'nullable|exists:etapas_produccion,id',
            'tecnico_actual_id' => 'nullable|exists:tecnicos,id',

            'fecha_ingreso' => 'required|date',
            'fecha_entrega_estimada' => 'nullable|date|after_or_equal:fecha_ingreso',

            'cantidad' => 'required|integer|min:1',
            'especificaciones' => 'required|string',
            'observaciones' => 'nullable|string',
            'color' => 'nullable|string|max:100',

            'prioridad' => 'required|in:Normal,Urgente',
            'total' => 'nullable|numeric|min:0',
        ]);

        $estadoInicial = EstadoOrden::where('nombre', 'Pendiente')->firstOrFail();

        $ultimaOrden = OrdenTrabajo::latest('id')->first();

        $numero = $ultimaOrden
            ? $ultimaOrden->id + 1
            : 1;

        $codigo = 'ORD-' . now()->format('Y') . '-' . str_pad(
            $numero,
            6,
            '0',
            STR_PAD_LEFT
        );

        $orden = OrdenTrabajo::create([
            'codigo' => $codigo,

            'codigo_caja' => $request->codigo_caja,

            'odontologo_id' => $request->odontologo_id,
            'paciente_id' => $request->paciente_id,
            'tipo_protesis_id' => $request->tipo_protesis_id,

            'estado_orden_id' => $estadoInicial->id,

            'etapa_actual_id' => $request->etapa_actual_id,

            'registrado_por' => auth()->id(),

            'tecnico_actual_id' => $request->tecnico_actual_id,

            'fecha_ingreso' => $request->fecha_ingreso,
            'fecha_entrega_estimada' => $request->fecha_entrega_estimada,

            'cantidad' => $request->cantidad,

            'especificaciones' => $request->especificaciones,
            'observaciones' => $request->observaciones,
            'color' => $request->color,

            'prioridad' => $request->prioridad,

            'total' => $request->total ?? 0,
        ]);

        //creamos un historial de produccion si la orden tiene una etapa asignada

        if ($orden->etapa_actual_id) {

            HistorialProduccion::create([
                'orden_trabajo_id' => $orden->id,

                'etapa_produccion_id' =>
                    $orden->etapa_actual_id,

                'tecnico_id' =>
                    $orden->tecnico_actual_id,

                'registrado_por' =>
                    auth()->id(),

                'fecha_inicio' =>
                    now(),

                'fecha_fin' =>
                    null,

                'estado' =>
                    'En proceso',

                'observaciones' =>
                    'Etapa inicial de la orden.',
            ]);
        }

        HistorialEstadoOrden::create([
            'orden_trabajo_id' => $orden->id,
            'estado_orden_id' => $estadoInicial->id,
            'registrado_por' => auth()->id(),
            'motivo' => null,
            'observaciones' => 'Estado inicial de la orden.',
            'fecha' => now(),
        ]);

        return redirect()
            ->route('ordenes.index')
            ->with('success', 'Orden de trabajo registrada correctamente.');
    }
    public function show(OrdenTrabajo $orden)
    {
        $orden->load([
            'paciente',
            'odontologo',
            'tipoProtesis',
            'estadoOrden',
            'etapaActual',
            'tecnicoActual',
            'usuarioRegistro',

            'historialProduccion.etapaProduccion',
            'historialProduccion.tecnico.user',

            'historialEstados.estadoOrden',
            'historialEstados.usuarioRegistro',
        ]);

        return view('ordenes.show', compact('orden'));
    }
    public function edit(OrdenTrabajo $orden)
    {
        if ($orden->estadoOrden?->nombre === 'Cancelado') {
            return redirect()
                ->route('ordenes.show', $orden)
                ->with('error', 'Una orden cancelada no puede ser editada.');
        }

        $odontologos = Odontologo::where('estado', true)
            ->orderBy('nombre')
            ->get();

        $pacientes = Paciente::where('estado', true)
            ->orderBy('nombre')
            ->get();

        $tiposProtesis = TipoProtesis::where('estado', true)
            ->orderBy('categoria')
            ->orderBy('nombre')
            ->get();

        $estados = EstadoOrden::where('estado', true)
            ->orderBy('orden')
            ->get();

        $etapas = EtapaProduccion::where('estado', true)
            ->orderBy('orden')
            ->get();

        $tecnicos = Tecnico::with('user')
            ->where('estado', true)
            ->get();

        return view('ordenes.edit', compact(
            'orden',
            'odontologos',
            'pacientes',
            'tiposProtesis',
            'estados',
            'etapas',
            'tecnicos'
        ));
    }
   public function update(Request $request, OrdenTrabajo $orden)
    {
        if ($orden->estadoOrden?->nombre === 'Cancelado') {
            return redirect()
                ->route('ordenes.show', $orden)
                ->with('error', 'Una orden cancelada no puede ser modificada.');
        }

        $datos = $request->validate([
            'codigo_caja' => 'nullable|string|max:20',

            'odontologo_id' => 'required|exists:odontologos,id',
            'paciente_id' => 'required|exists:pacientes,id',
            'tipo_protesis_id' => 'required|exists:tipos_protesis,id',

            'estado_orden_id' => 'required|exists:estados_orden,id',

            'etapa_actual_id' => 'nullable|exists:etapas_produccion,id',
            'tecnico_actual_id' => 'nullable|exists:tecnicos,id',

            'fecha_ingreso' => 'required|date',

            'fecha_entrega_estimada' =>
                'nullable|date|after_or_equal:fecha_ingreso',

            'fecha_entrega_real' =>
                'nullable|date|after_or_equal:fecha_ingreso',

            'cantidad' => 'required|integer|min:1',

            'especificaciones' => 'required|string',
            'observaciones' => 'nullable|string',

            'color' => 'nullable|string|max:100',

            'prioridad' => 'required|in:Normal,Urgente',

            'total' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($datos, $orden) {

            $etapaAnterior = $orden->etapa_actual_id;
            $tecnicoAnterior = $orden->tecnico_actual_id;
            $estadoAnterior = $orden->estado_orden_id;
            
            $orden->update($datos);

            // ===============================
                // SI CAMBIÓ EL ESTADO DE LA ORDEN
                // ===============================
                if ($estadoAnterior != $orden->estado_orden_id) {

                    HistorialEstadoOrden::create([
                        'orden_trabajo_id' => $orden->id,
                        'estado_orden_id' => $orden->estado_orden_id,
                        'registrado_por' => auth()->id(),
                        'motivo' => null,
                        'observaciones' => 'Cambio de estado registrado desde la edición de la orden.',
                        'fecha' => now(),
                    ]);
                }
                    
                        // ===============================
                        // SI CAMBIÓ LA ETAPA
                        // ===============================
                    if ($etapaAnterior != $orden->etapa_actual_id) {

                        // Cerramos la etapa anterior
                        HistorialProduccion::where('orden_trabajo_id', $orden->id)
                            ->whereNull('fecha_fin')
                            ->update([
                                'fecha_fin' => now(),
                                'estado' => 'Completado',
                            ]);

                            // Creamos la nueva etapa
                            if ($orden->etapa_actual_id) {

                                HistorialProduccion::create([
                                    'orden_trabajo_id' => $orden->id,
                                    'etapa_produccion_id' => $orden->etapa_actual_id,
                                    'tecnico_id' => $orden->tecnico_actual_id,
                                    'registrado_por' => auth()->id(),
                                    'fecha_inicio' => now(),
                                    'fecha_fin' => null,
                                    'estado' => 'En proceso',
                                    'observaciones' => 'Cambio de etapa registrado.',
                                    ]);
                                }
                            
                            }

                        // ===============================
                        // SI SOLO CAMBIÓ EL TÉCNICO
                        // ===============================
                        elseif ($tecnicoAnterior != $orden->tecnico_actual_id) {

                            HistorialProduccion::where('orden_trabajo_id', $orden->id)
                                ->whereNull('fecha_fin')
                                ->latest('id')
                                ->first()
                                ?->update([
                                    'tecnico_id' => $orden->tecnico_actual_id,
                                    'observaciones' => 'Técnico reasignado durante la etapa.',
                                ]);
                        }
                    });

                    return redirect()
                        ->route('ordenes.show', $orden)
                        ->with(
                            'success',
                            'Orden actualizada correctamente.'
                        );
                }
    public function confirmarCancelacion(OrdenTrabajo $orden)
    {
        if ($orden->estadoOrden?->nombre === 'Cancelado') {
            return redirect()
                ->route('ordenes.show', $orden)
                ->with('error', 'La orden ya se encuentra cancelada.');
        }

        return view('ordenes.cancelar', compact('orden'));
    }


    // ==========================================
    // CANCELAR LA ORDEN
    // ==========================================
    public function cancelar(Request $request, OrdenTrabajo $orden)
    {
        if ($orden->estadoOrden?->nombre === 'Cancelado') {
            return redirect()
                ->route('ordenes.show', $orden)
                ->with('error', 'La orden ya se encuentra cancelada.');
        }

        $datos = $request->validate([
            'motivo' => 'required|in:odontologo,paciente,error,duplicada,imposibilidad,otro',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        $motivos = [
            'odontologo' => 'Cancelación solicitada por odontólogo',
            'paciente' => 'Cancelación solicitada por paciente',
            'error' => 'Orden registrada por error',
            'duplicada' => 'Orden duplicada',
            'imposibilidad' => 'Imposibilidad de continuar',
            'otro' => 'Otro motivo',
        ];

        $estadoCancelado = EstadoOrden::where('nombre', 'Cancelado')
            ->firstOrFail();

        DB::transaction(function () use (
            $orden,
            $estadoCancelado,
            $datos,
            $motivos
        ) {

            // Cierra la etapa de producción que esté activa
            HistorialProduccion::where('orden_trabajo_id', $orden->id)
                ->whereNull('fecha_fin')
                ->update([
                    'fecha_fin' => now(),
                    'estado' => 'Pausado',
                ]);

            // Cambia la orden a Cancelado
            $orden->update([
                'estado_orden_id' => $estadoCancelado->id,
                'etapa_actual_id' => null,
                'tecnico_actual_id' => null,
            ]);

            // Registra por qué se canceló
            HistorialEstadoOrden::create([
                'orden_trabajo_id' => $orden->id,
                'estado_orden_id' => $estadoCancelado->id,
                'registrado_por' => auth()->id(),
                'motivo' => $motivos[$datos['motivo']],
                'observaciones' => $datos['observaciones'] ?? null,
                'fecha' => now(),
            ]);
        }); 

        return redirect()
            ->route('ordenes.show', $orden)
            ->with('success', 'La orden fue cancelada correctamente.');
    }
}