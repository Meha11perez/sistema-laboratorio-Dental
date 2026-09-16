<?php

namespace App\Http\Controllers;
use App\Models\HistorialProduccion;
use App\Models\HistorialEstadoOrden;
use App\Models\Devolucion;
use App\Models\OrdenTrabajo;
use App\Models\Odontologo;
use App\Models\Paciente;
use App\Models\TipoProtesis;
use App\Models\EstadoOrden;
use App\Models\EtapaProduccion;
use App\Models\Tecnico;
use App\Models\Garantia;
use App\Models\Pago;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;



class OrdenTrabajoController extends Controller
{
    public function index()
    {
        $ordenes = OrdenTrabajo::with([
            'paciente',
            'odontologo',
            'tipoProtesis',
            'estadoOrden',
            'tecnicoActual.user', 
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
        $datos = $request->validate([
        'codigo_caja' => 'nullable|string|max:20',

        'odontologo_id' => 'required|exists:odontologos,id',
        'paciente_id' => 'required|exists:pacientes,id',
        'tipo_protesis_id' => 'required|exists:tipos_protesis,id',

        'etapa_actual_id' => 'nullable|exists:etapas_produccion,id',
        'tecnico_actual_id' => 'nullable|exists:tecnicos,id',

        'fecha_ingreso' => [
            'required',
            'date',
            'after_or_equal:today',
        ],

        'fecha_entrega_estimada' => [
            'nullable',
            'date',
            'after_or_equal:today',
            'after_or_equal:fecha_ingreso',
        ],

        'cantidad' => 'required|integer|min:1',

        'especificaciones' => 'required|string',
        'observaciones' => 'nullable|string',
        'color' => 'nullable|string|max:100',

        'prioridad' => 'required|in:Normal,Urgente',

        'total' => 'nullable|numeric|min:0',

        'tipo_orden' => 'required|in:Nueva,Repeticion',

        'orden_origen_id' => [
            'nullable',
            'required_if:tipo_orden,Repeticion',
            'exists:ordenes_trabajo,id',
        ],

        'devolucion_id' => [
            'nullable',
            'exists:devoluciones,id',
        ],
        ]);

    $estadoInicial = EstadoOrden::where('nombre', 'Pendiente')
        ->firstOrFail();


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

    $orden = null;

    DB::transaction(function () use (
        $datos,
        $estadoInicial,
        $codigo,
        &$orden
    ) {

        // ==========================================
        // CREAR ORDEN
        // ==========================================
        $orden = OrdenTrabajo::create([
            'codigo' => $codigo,

            'codigo_caja' => $datos['codigo_caja'] ?? null,

            'odontologo_id' => $datos['odontologo_id'],
            'paciente_id' => $datos['paciente_id'],
            'tipo_protesis_id' => $datos['tipo_protesis_id'],

            'estado_orden_id' => $estadoInicial->id,

            'etapa_actual_id' => $datos['etapa_actual_id'] ?? null,
            'tecnico_actual_id' => $datos['tecnico_actual_id'] ?? null,

            'registrado_por' => auth()->id(),

            'fecha_ingreso' => $datos['fecha_ingreso'],

            'fecha_entrega_estimada' =>
                $datos['fecha_entrega_estimada'] ?? null,

            'cantidad' => $datos['cantidad'],

            'especificaciones' => $datos['especificaciones'],

            'observaciones' => $datos['observaciones'] ?? null,

            'color' => $datos['color'] ?? null,

            'prioridad' => $datos['prioridad'],

            'total' => $datos['total'] ?? 0,

            'tipo_orden' => $datos['tipo_orden'],

            'orden_origen_id' => $datos['orden_origen_id'] ?? null,
           
            'devolucion_id' => $datos['devolucion_id'] ?? null,
            
        ]);


        // ==========================================
        // HISTORIAL DE PRODUCCIÓN INICIAL
        // ==========================================
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


        // ==========================================
        // HISTORIAL DE ESTADO INICIAL
        // ==========================================
        HistorialEstadoOrden::create([
            'orden_trabajo_id' => $orden->id,

            'estado_orden_id' =>
                $estadoInicial->id,

            'registrado_por' =>
                auth()->id(),

            'motivo' =>
                null,

            'observaciones' =>
                'Estado inicial de la orden.',

            'fecha' =>
                now(),
        ]);
    });


        return redirect()
            ->route('ordenes.index')
            ->with(
                'success',
                $orden->tipo_orden === 'Repeticion'
                    ? 'Repetición registrada correctamente.'
                    : 'Orden de trabajo registrada correctamente.'
            );

        }
    public function show(OrdenTrabajo $orden)
        {
            $orden->load([
                'paciente',
                'odontologo.clinica',
                'tipoProtesis',
                'estadoOrden',
                'etapaActual',
                'tecnicoActual.user',
                'usuarioRegistro',

                'historialProduccion.etapaProduccion',
                'historialProduccion.tecnico.user',

                'historialEstados.estadoOrden',
                'historialEstados.usuarioRegistro',

                'ordenOrigen',
                'repeticiones',

                'garantia',

                'devoluciones.garantia',
                'devoluciones.tecnicoResponsable.user',
                'devoluciones.usuarioRegistro',
                'devoluciones.repeticion',

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

                'fecha_ingreso' => 'required|date|after_or_equal:today',

                'fecha_entrega_estimada' =>
                    'nullable|date|after_or_equal:today|after_or_equal:fecha_ingreso',

                'fecha_entrega_real' =>
                    'nullable|date|after_or_equal:fecha_ingreso',

                'cantidad' => 'required|integer|min:1',

                'especificaciones' => 'required|string',
                'observaciones' => 'nullable|string',

                'color' => 'nullable|string|max:100',

                'prioridad' => 'required|in:Normal,Urgente',

                'total' => 'nullable|numeric|min:0',
            ]);
            
                $pago = Pago::where(
                        'orden_trabajo_id',
                        $orden->id
                    )->first();

                if ($pago) {

                    $montoPagado = (float) $pago->monto_pagado;
                    $nuevoTotal = (float) ($datos['total'] ?? 0);

                    if ($nuevoTotal < $montoPagado) {

                        return redirect()
                            ->back()
                            ->withErrors([
                                'total' =>
                                    'El nuevo total no puede ser menor al monto ya pagado de Q '
                                    . number_format($montoPagado, 2)
                                    . '.',
                            ])
                            ->withInput();
                    }
                }

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

                // ==========================================
                // CREAR GARANTÍA AL ENTREGAR LA ORDEN
                // ==========================================
                if ($orden->estadoOrden?->nombre === 'Entregado') {

                    if (!$orden->garantia) {

                        Garantia::create([
                            'orden_trabajo_id' => $orden->id,
                            'fecha_inicio' => now()->toDateString(),
                            'fecha_vencimiento' => now()
                                ->addMonths(3)
                                ->toDateString(),
                            'estado' => 'Vigente',
                            'observaciones' => 'Garantía generada automáticamente al entregar la orden.',
                        ]);
                    }
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
    public function rotulo(OrdenTrabajo $orden)
        {
            $orden->load([
                'paciente',
                'odontologo.clinica',
                'tipoProtesis',
            ]);

            return view('ordenes.rotulo', compact('orden'));
        }
    public function repetir(Request $request, OrdenTrabajo $orden)
        {
            if ($orden->estadoOrden?->nombre === 'Cancelado') {
                return redirect()
                    ->route('ordenes.show', $orden)
                    ->with('error', 'No se puede crear una repetición de una orden cancelada.');
            }

            // ID de devolución enviado desde el botón
            $devolucionId = $request->query('devolucion');

            // Si viene desde una devolución, validamos que exista
            if ($devolucionId) {

                    $devolucion = Devolucion::with('repeticion')
                    ->where('id', $devolucionId)
                    ->where('orden_trabajo_id', $orden->id)
                    ->firstOrFail();

                // Si esa devolución ya tiene una repetición,
                // no permitimos crear otra.
                if ($devolucion->repeticion) {

                    return redirect()
                        ->route('ordenes.show', $devolucion->repeticion)
                        ->with(
                            'error',
                            'Esta devolución ya tiene una repetición asociada.'
                        );
                }

                // Además comprobamos que realmente requiera repetición
                if (!$devolucion->requiere_repeticion) {

                    return redirect()
                        ->route('ordenes.show', $orden)
                        ->with(
                            'error',
                            'Esta devolución no está marcada como requerida para repetición.'
                        );
                }
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

            $etapas = EtapaProduccion::where('estado', true)
                ->orderBy('orden')
                ->get();

            $tecnicos = Tecnico::with('user')
                ->where('estado', true)
                ->get();

            return view('ordenes.create', compact(
                'orden',
                'odontologos',
                'pacientes',
                'tiposProtesis',
                'etapas',
                'tecnicos',
                'devolucionId'
            ));
        }
}