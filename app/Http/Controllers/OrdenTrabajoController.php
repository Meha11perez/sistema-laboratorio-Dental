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
use App\Models\Material;
use App\Models\InventarioTecnico;
use App\Models\OrdenMaterial;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class OrdenTrabajoController extends Controller
{ 
public function index()
    {
        $query = OrdenTrabajo::with([
            'paciente',
            'odontologo',
            'tipoProtesis',
            'estadoOrden',
            'etapaActual',
            'tecnicoActual.user',
        ]);

        /*
        |--------------------------------------------------------------------------
        | TÉCNICO
        |--------------------------------------------------------------------------
        | Solo puede ver órdenes actualmente asignadas a él.
        */

       if ($this->esTecnico()) {

        $tecnico =
            $this->obtenerTecnicoAutenticado();

        $query->where(
            'tecnico_actual_id',
            $tecnico->id
        );

        $query->whereHas(
            'estadoOrden',
            function ($q) {

                $q->whereNotIn(
                    'nombre',
                    [
                        'Terminado',
                        'Entregado',
                        'Cancelado',
                    ]
                ); 
            }
        );
    }

        $ordenes = $query
            ->latest()
            ->paginate(10);

        return view(
            'ordenes.index',
            compact('ordenes')
        );
    }
private function esTecnico(): bool
    {
        return auth()->user()?->role?->nombre === 'Técnico';
    }

private function obtenerTecnicoAutenticado(): Tecnico
    {
        $tecnico = Tecnico::where(
            'user_id',
            auth()->id()
        )->first();

        if (!$tecnico) {
            abort(
                403,
                'El usuario no tiene un técnico asociado.'
            );
        }

        return $tecnico;
    }

private function verificarOrdenTecnico(
        OrdenTrabajo $orden
    ): void
    {
        if (!$this->esTecnico()) {
            return;
        }

        $tecnico = $this->obtenerTecnicoAutenticado();

        if (
            $orden->tecnico_actual_id !== $tecnico->id
        ) {
            abort(
                403,
                'No tiene permiso para consultar esta orden.'
            );
        }
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
        // Si es Técnico, verifica que la orden sea realmente suya.
        $this->verificarOrdenTecnico($orden);


        // =====================================================
        // CARGAR INFORMACIÓN COMPLETA DE LA ORDEN
        // =====================================================
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

            // Materiales ya consumidos en esta orden
            'materialesUtilizados.material',
        ]);


        // =====================================================
        // VARIABLES PARA INVENTARIO DEL TÉCNICO
        // =====================================================
        $inventarioTecnico = collect();

        $puedeRegistrarMaterial = false;


        // =====================================================
        // SOLO SI EL USUARIO ES TÉCNICO
        // =====================================================
        if ($this->esTecnico()) {

            $tecnico = $this->obtenerTecnicoAutenticado();


            // Solamente puede consumir materiales si:
            // 1. La orden sigue asignada a él.
            // 2. La orden está En proceso.
            // 3. Tiene una etapa activa.
            if (
                $orden->tecnico_actual_id === $tecnico->id
                &&
                $orden->estadoOrden?->nombre === 'En proceso'
                &&
                $orden->etapa_actual_id !== null
            ) {

                $puedeRegistrarMaterial = true;


                // Materiales que todavía tiene disponibles
                $inventarioTecnico = InventarioTecnico::with('material')
                    ->where(
                        'tecnico_id',
                        $tecnico->id
                    )
                    ->where(
                        'cantidad',
                        '>',
                        0
                    )
                    ->orderByDesc('cantidad')
                    ->get();
            }
        }


        return view(
            'ordenes.show',
            compact(
                'orden',
                'inventarioTecnico',
                'puedeRegistrarMaterial'
            )
        );
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
    public function iniciarEtapa(OrdenTrabajo $orden)
{
    if (!$this->esTecnico()) {
        abort(403);
    }

    $tecnico = $this->obtenerTecnicoAutenticado();

    if ($orden->tecnico_actual_id !== $tecnico->id) {
        abort(
            403,
            'Esta orden no está asignada a usted.'
        );
    }

    if (!$orden->etapa_actual_id) {
        return back()->with(
            'error',
            'La orden no tiene una etapa asignada.'
        );
    }

    $orden->loadMissing('estadoOrden');

    if (
        in_array(
            $orden->estadoOrden?->nombre,
            [
                'Terminado',
                'Entregado',
                'Cancelado',
            ],
            true
        )
    ) {
        return back()->with(
            'error',
            'Esta orden ya no puede iniciar producción.'
        );
    }

    $estadoEnProceso = EstadoOrden::where(
        'nombre',
        'En proceso'
    )->firstOrFail();


    DB::transaction(function () use (
        $orden,
        $tecnico,
        $estadoEnProceso
    ) {

        if (
            $orden->estado_orden_id !==
            $estadoEnProceso->id
        ) {

            $orden->update([
                'estado_orden_id' =>
                    $estadoEnProceso->id,
            ]);

            HistorialEstadoOrden::create([
                'orden_trabajo_id' =>
                    $orden->id,

                'estado_orden_id' =>
                    $estadoEnProceso->id,

                'registrado_por' =>
                    auth()->id(),

                'motivo' =>
                    null,

                'observaciones' =>
                    'El técnico inició el trabajo de la etapa.',

                'fecha' =>
                    now(),
            ]);
        }


        $historial =
            HistorialProduccion::where(
                'orden_trabajo_id',
                $orden->id
            )
            ->whereNull('fecha_fin')
            ->latest('id')
            ->first();


        if (!$historial) {

            HistorialProduccion::create([
                'orden_trabajo_id' =>
                    $orden->id,

                'etapa_produccion_id' =>
                    $orden->etapa_actual_id,

                'tecnico_id' =>
                    $tecnico->id,

                'registrado_por' =>
                    auth()->id(),

                'fecha_inicio' =>
                    now(),

                'fecha_fin' =>
                    null,

                'estado' =>
                    'En proceso',

                'observaciones' =>
                    'Etapa iniciada por el técnico.',
            ]);

        } else {

            $historial->update([
                'estado' => 'En proceso',
            ]);
        }
    });


    return back()->with(
        'success',
        'Etapa iniciada correctamente.'
    );
}

    public function completarEtapa(OrdenTrabajo $orden)
    {
        // Solo Técnico
        if (!$this->esTecnico()) {
            abort(403);
        }

        $tecnico = $this->obtenerTecnicoAutenticado();

        // Verificar que la orden realmente esté asignada a este técnico
        if ($orden->tecnico_actual_id !== $tecnico->id) {
            abort(
                403,
                'Esta orden no está asignada a usted.'
            );
        }

        // La orden debe tener etapa activa
        if (!$orden->etapa_actual_id) {
            return back()->with(
                'error',
                'La orden no tiene una etapa activa.'
            );
        }

        // Cargar relaciones necesarias
        $orden->loadMissing([
            'etapaActual',
            'estadoOrden',
        ]);

        // No permitir completar órdenes cerradas
        if (
            in_array(
                $orden->estadoOrden?->nombre,
                [
                    'Terminado',
                    'Entregado',
                    'Cancelado',
                ],
                true
            )
        ) {
            return back()->with(
                'error',
                'Esta orden ya no puede modificarse en producción.'
            );
        }

        $esEtapaFinal =
            $orden->etapaActual?->nombre === 'Terminados';


        DB::transaction(function () use (
            $orden,
            $tecnico,
            $esEtapaFinal
        ) {

            // =====================================================
            // CERRAR HISTORIAL DE PRODUCCIÓN ACTUAL
            // =====================================================
            $historial = HistorialProduccion::where(
                    'orden_trabajo_id',
                    $orden->id
                )
                ->where(
                    'etapa_produccion_id',
                    $orden->etapa_actual_id
                )
                ->where(
                    'tecnico_id',
                    $tecnico->id
                )
                ->whereNull('fecha_fin')
                ->latest('id')
                ->first();


            if (!$historial) {
                throw \Illuminate\Validation\ValidationException
                    ::withMessages([
                        'produccion' =>
                            'No se encontró una etapa activa para completar.',
                    ]);
            }


            $historial->update([
                'fecha_fin' => now(),
                'estado' => 'Completado',
            ]);


            // =====================================================
            // SI LA ETAPA ES TERMINADOS
            // LA PRODUCCIÓN COMPLETA FINALIZA
            // =====================================================
            if ($esEtapaFinal) {

                $estadoTerminado = EstadoOrden::where(
                    'nombre',
                    'Terminado'
                )->firstOrFail();


                $orden->update([
                    'estado_orden_id' =>
                        $estadoTerminado->id,

                    'tecnico_actual_id' =>
                        null,
                ]);


                HistorialEstadoOrden::create([
                    'orden_trabajo_id' =>
                        $orden->id,

                    'estado_orden_id' =>
                        $estadoTerminado->id,

                    'registrado_por' =>
                        auth()->id(),

                    'motivo' =>
                        null,

                    'observaciones' =>
                        'Producción finalizada por el técnico.',

                    'fecha' =>
                        now(),
                ]);


            } else {
                        // La etapa terminó.
                        // La orden queda esperando la siguiente asignación.
                        $estadoPendiente = EstadoOrden::where(
                            'nombre',
                            'Pendiente'
                        )->firstOrFail();

                        $orden->update([
                            'estado_orden_id' => $estadoPendiente->id,
                            'tecnico_actual_id' => null,
                        ]);


                        HistorialEstadoOrden::create([
                            'orden_trabajo_id' => $orden->id,
                            'estado_orden_id' => $estadoPendiente->id,
                            'registrado_por' => auth()->id(),
                            'motivo' => null,
                            'observaciones' =>
                                'Etapa completada. Pendiente de asignación de la siguiente etapa.',
                            'fecha' => now(),
                        ]);
                    }
        });

        // Si terminó completamente
        if ($esEtapaFinal) {

            return redirect()
                ->route('ordenes.index')
                ->with(
                    'success',
                    'Producción finalizada correctamente.'
                );
        }

        // Si solamente terminó su etapa
        return redirect()
            ->route('ordenes.index')
            ->with(
                'success',
                'Etapa completada correctamente. La orden queda pendiente de la siguiente asignación.'
            );
    }
    public function registrarMaterial(
        Request $request,
        OrdenTrabajo $orden
    ) {
        // SOLO TÉCNICO

        if (!$this->esTecnico()) {
            abort(403);
        }

        $tecnico =
            $this->obtenerTecnicoAutenticado();
        
        // LA ORDEN DEBE SER DEL TÉCNICO
        
        if (
            $orden->tecnico_actual_id !== $tecnico->id
        ) {
            abort(
                403,
                'Esta orden no está asignada a usted.'
            );
        }

        // NO MODIFICAR ÓRDENES FINALIZADAS

        $orden->loadMissing('estadoOrden');

        if (
            in_array(
                $orden->estadoOrden?->nombre,
                [
                    'Terminado',
                    'Entregado',
                    'Cancelado',
                ],
                true
            )
        ) {
            return back()->with(
                'error',
                'No se pueden registrar materiales en una orden finalizada.'
            );
        }

    // VALIDACIÓN

    $datos = $request->validate([
        'material_id' => [
            'required',
            'exists:materiales,id',
        ],

        'cantidad' => [
            'required',
            'numeric',
            'min:0.01',
        ],
    ]);


    DB::transaction(function () use (
        $datos,
        $orden,
        $tecnico
    ) {

        // INVENTARIO ACTUAL DEL TÉCNICO
        
        $inventario = InventarioTecnico::where(
            'tecnico_id',
            $tecnico->id
        )
        ->where(
            'material_id',
            $datos['material_id']
        )
        ->lockForUpdate()
        ->first();


        if (!$inventario) {

            throw \Illuminate\Validation\ValidationException::withMessages([
                'material_id' =>
                    'Este material no está asignado a su inventario.',
            ]);
        }


        $cantidadSolicitada =
            (float) $datos['cantidad'];

        $stockAnterior =
            (float) $inventario->cantidad;


        if (
            $cantidadSolicitada > $stockAnterior
        ) {

            throw \Illuminate\Validation\ValidationException::withMessages([
                'cantidad' =>
                    'No tiene suficiente material disponible. Disponible: '
                    . number_format(
                        $stockAnterior,
                        2
                    ),
            ]);
        }

        // DESCONTAR INVENTARIO DEL TÉCNICO

        $stockNuevo =
            $stockAnterior - $cantidadSolicitada;


        $inventario->update([
            'cantidad' => $stockNuevo,
        ]);

        // =================================================
        // MATERIAL
        // =================================================
        $material = Material::findOrFail(
            $datos['material_id']
        );


        $costoActual =
            (float) $material->costo_unitario;


        // =================================================
        // MATERIAL UTILIZADO EN LA ORDEN
        // =================================================
        $ordenMaterial = OrdenMaterial::where(
            'orden_trabajo_id',
            $orden->id
        )
        ->where(
            'material_id',
            $material->id
        )
        ->lockForUpdate()
        ->first();


        if ($ordenMaterial) {

            $cantidadAnteriorOrden =
                (float) $ordenMaterial->cantidad;

            $subtotalAnterior =
                (float) $ordenMaterial->subtotal;


            $cantidadNuevaOrden =
                $cantidadAnteriorOrden
                + $cantidadSolicitada;


            $subtotalNuevo =
                $subtotalAnterior
                + (
                    $cantidadSolicitada
                    * $costoActual
                );

            $costoPromedio =
                $cantidadNuevaOrden > 0
                    ? $subtotalNuevo
                        / $cantidadNuevaOrden
                    : $costoActual;

            $ordenMaterial->update([
                'cantidad' =>
                    $cantidadNuevaOrden,

                'costo_unitario' =>
                    $costoPromedio,

                'subtotal' =>
                    $subtotalNuevo,
            ]);

        } else {

            OrdenMaterial::create([
                'orden_trabajo_id' =>
                    $orden->id,

                'material_id' =>
                    $material->id,

                'cantidad' =>
                    $cantidadSolicitada,

                'costo_unitario' =>
                    $costoActual,

                'subtotal' =>
                    $cantidadSolicitada
                    * $costoActual,
            ]);
        }

        // =================================================
        // HISTORIAL DEL MOVIMIENTO
        // =================================================
        MovimientoInventario::create([
            'material_id' =>
                $material->id,

            'tecnico_id' =>
                $tecnico->id,

            'orden_trabajo_id' =>
                $orden->id,

            'registrado_por' =>
                auth()->id(),

            'tipo_movimiento' =>
                'Consumo',

            'cantidad' =>
                $cantidadSolicitada,

            'stock_anterior' =>
                $stockAnterior,

            'stock_nuevo' =>
                $stockNuevo,

            'fecha_movimiento' =>
                now(),

            'observaciones' =>
                'Material utilizado en la orden '
                . $orden->codigo,
        ]);

    });


    return redirect()
        ->route(
            'ordenes.show',
            $orden
        )
        ->with(
            'success',
            'Material registrado correctamente en la orden.'
        );
}
}