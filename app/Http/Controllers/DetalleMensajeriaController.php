<?php

namespace App\Http\Controllers;

use App\Models\DetalleMensajeria;
use App\Models\RutaMensajeria;
use App\Models\OrdenTrabajo;
use App\Models\Odontologo;
use App\Models\Clinica;
use App\Models\EstadoOrden;
use App\Models\Garantia;
use App\Models\HistorialEstadoOrden;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DetalleMensajeriaController extends Controller
{
    public function create(RutaMensajeria $ruta)
    {
        if (
            $ruta->estado === 'Finalizada' ||
            $ruta->estado === 'Cancelada'
        ) {
            return redirect()
                ->route('mensajeria.show', $ruta)
                ->with(
                    'error',
                    'No se pueden agregar visitas a una ruta finalizada o cancelada.'
                );
        }

        /*
        * Órdenes disponibles para ENTREGA.
        * Solamente trabajos terminados.
        */
        $ordenes = OrdenTrabajo::with([
            'paciente',
            'odontologo.clinica',
            'estadoOrden',
        ])
            ->whereHas('estadoOrden', function ($query) {
                $query->where('nombre', 'Terminado');
            })
            ->orderByDesc('id')
            ->get();

        $odontologos = Odontologo::with('clinica')
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();

        $clinicas = Clinica::where('estado', true)
            ->orderBy('nombre')
            ->get();

        $siguienteOrden = $ruta->detalles()
            ->max('orden_visita');

        $siguienteOrden = $siguienteOrden
            ? $siguienteOrden + 1
            : 1;

        return view(
            'mensajeria.detalles.create',
            compact(
                'ruta',
                'ordenes',
                'odontologos',
                'clinicas',
                'siguienteOrden'
            )
        );
    }
    public function store(
        Request $request,
        RutaMensajeria $ruta
    ) {
        if (
            $ruta->estado === 'Finalizada' ||
            $ruta->estado === 'Cancelada'
        ) {
            return redirect()
                ->route('mensajeria.show', $ruta)
                ->with(
                    'error',
                    'No se pueden agregar visitas a esta ruta.'
                );
        }

        $datos = $request->validate([
            'orden_trabajo_id' => [
                'nullable',
                'exists:ordenes_trabajo,id',
            ],

            'odontologo_id' => [
                'required',
                'exists:odontologos,id',
            ],

            'clinica_id' => [
                'nullable',
                'exists:clinicas,id',
            ],

            'tipo_movimiento' => [
                'required',
                'in:Entrega,Recolección',
            ],

            'orden_visita' => [
                'required',
                'integer',
                'min:1',
            ],

            'direccion_referencia' => [
                'nullable',
                'string',
                'max:500',
            ],

            'observaciones' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        if (
            $datos['tipo_movimiento'] === 'Entrega' &&
            empty($datos['orden_trabajo_id'])
        ) {
            return redirect()
                ->back()
                ->withErrors([
                    'orden_trabajo_id' =>
                        'Debe seleccionar una orden de trabajo para registrar una entrega.',
                ])
                ->withInput();
        }
        
        if (
            $datos['tipo_movimiento'] === 'Entrega' &&
            !empty($datos['orden_trabajo_id'])
        ) {

            $orden = OrdenTrabajo::with('estadoOrden')
                ->findOrFail($datos['orden_trabajo_id']);

            if ($orden->estadoOrden?->nombre !== 'Terminado') {

                return redirect()
                    ->back()
                    ->withErrors([
                        'orden_trabajo_id' =>
                            'Solo se pueden programar para entrega las órdenes que estén en estado Terminado.',
                    ])
                    ->withInput();
            }
        }

        DetalleMensajeria::create([
            'ruta_mensajeria_id' => $ruta->id,

            'orden_trabajo_id' =>
                $datos['orden_trabajo_id'] ?? null,

            'odontologo_id' =>
                $datos['odontologo_id'],

            'clinica_id' =>
                $datos['clinica_id'] ?? null,

            'tipo_movimiento' =>
                $datos['tipo_movimiento'],

            'orden_visita' =>
                $datos['orden_visita'],

            'direccion_referencia' =>
                $datos['direccion_referencia'] ?? null,

            'estado' =>
                'Pendiente',

            'hora_realizada' =>
                null,

            'recibido_por' =>
                null,

            'observaciones' =>
                $datos['observaciones'] ?? null,
        ]);

        return redirect()
            ->route('mensajeria.show', $ruta)
            ->with(
                'success',
                'Visita agregada correctamente.'
            );
    }
    public function updateEstado(
        Request $request,
        DetalleMensajeria $detalle
    )
    {
        // =====================================================
        // SEGURIDAD: EL MENSAJERO SOLO PUEDE TRABAJAR
        // VISITAS DE UNA RUTA ASIGNADA A ÉL
        // =====================================================

        $detalle->loadMissing('rutaMensajeria');

        if (!$detalle->rutaMensajeria) {
            abort(404, 'La visita no tiene una ruta asociada.');
        }

        if ($detalle->rutaMensajeria->mensajero_id !== auth()->id()) {
            abort(403, 'No tiene permiso para modificar esta visita.');
        }

        // La visita solo puede ejecutarse cuando la ruta está en curso
        if ($detalle->rutaMensajeria->estado !== 'En ruta') {
            return back()->with(
                'error',
                'Solo puede registrar una visita cuando la ruta está en curso.'
            );
        }

        $datos = $request->validate([
            'estado' => [
                'required',
                'in:Realizada,No realizada',
            ],

            'recibido_por' => [
                'nullable',
                'string',
                'max:150',
            ],
            'firma_recibido' => [
                'nullable',
                'string',
            ],
            'observaciones' => [
                'nullable',
                'string',
                'max:1000',
            ], 
        ]);

        if (
            $datos['estado'] === 'Realizada' &&
            $detalle->tipo_movimiento === 'Recolección'
        ) {

            if (empty($datos['recibido_por'])) {

                return back()
                    ->withErrors([
                        'recibido_por' =>
                            'Debe ingresar el nombre de quien entrega.',
                    ])
                    ->withInput();
            }

            if (empty($datos['firma_recibido'])) {

                return back()
                    ->withErrors([
                        'firma_recibido' =>
                            'Debe capturar la firma de quien entrega.',
                    ])
                    ->withInput();
            }
        }

        DB::transaction(function () use ($datos, $detalle) {

            /*
            * Si la visita fue realizada,
            * registramos automáticamente la hora.
            */
            $horaRealizada = $datos['estado'] === 'Realizada'
                ? now()->format('H:i:s')
                : null;


                // ==========================================
                // PROCESAR FIRMA
                // ==========================================
                $rutaFirma = $detalle->firma_recibido;

                if (
                    !empty($datos['firma_recibido']) &&
                    str_starts_with(
                        $datos['firma_recibido'],
                        'data:image/png;base64,'
                    )
                ) {

                    $firmaBase64 = str_replace(
                        'data:image/png;base64,',
                        '',
                        $datos['firma_recibido']
                    );

                    $firmaBase64 = str_replace(
                        ' ',
                        '+',
                        $firmaBase64
                    );

                    $imagenFirma = base64_decode(
                        $firmaBase64,
                        true
                    );

                    if ($imagenFirma === false) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'firma_recibido' =>
                                'La firma recibida no es válida.',
                        ]);
                    }


                    /*
                    * Si ya existía una firma anterior,
                    * eliminamos el archivo.
                    */
                    if (
                        $detalle->firma_recibido &&
                        Storage::disk('public')->exists(
                            $detalle->firma_recibido
                        )
                    ) {
                        Storage::disk('public')->delete(
                            $detalle->firma_recibido
                        );
                    }


                    $nombreArchivo =
                        'firma_visita_'
                        . $detalle->id
                        . '_'
                        . now()->format('Ymd_His')
                        . '.png';


                    $rutaFirma =
                        'firmas/mensajeria/'
                        . $nombreArchivo;


                    Storage::disk('public')->put(
                        $rutaFirma,
                        $imagenFirma
                    );
                }

            // ==========================================
            // ACTUALIZAR VISITA
            // ==========================================
            
            $esRealizada = $datos['estado'] === 'Realizada';
                if (
                    !$esRealizada &&
                    $detalle->firma_recibido &&
                    Storage::disk('public')->exists(
                        $detalle->firma_recibido
                    )
                ) {
                    Storage::disk('public')->delete(
                        $detalle->firma_recibido
                    );
                }

                $detalle->update([
                    'estado' => $datos['estado'],

                    'hora_realizada' =>
                        $esRealizada
                            ? $horaRealizada
                            : null,

                    'recibido_por' =>
                        $esRealizada
                            ? ($datos['recibido_por'] ?? null)
                            : null,

                    'firma_recibido' =>
                        $esRealizada
                            ? $rutaFirma
                            : null,

                    'observaciones' =>
                        $datos['observaciones']
                        ?? $detalle->observaciones,
                ]);

            // ==========================================
            // ENTREGA REALIZADA
            // ==========================================
            if (
                $detalle->tipo_movimiento === 'Entrega' &&
                $datos['estado'] === 'Realizada' &&
                $detalle->orden_trabajo_id
            ) {

                $orden = OrdenTrabajo::with([
                    'estadoOrden',
                    'garantia',
                ])
                    ->findOrFail(
                        $detalle->orden_trabajo_id
                    );


                $estadoEntregado = EstadoOrden::where(
                    'nombre',
                    'Entregado'
                )->firstOrFail();


                /*
                * Solo actualizamos si todavía
                * no se encuentra entregada.
                */
                if (
                    $orden->estado_orden_id !==
                    $estadoEntregado->id
                ) {

                    // ==================================
                    // ACTUALIZAR ORDEN
                    // ==================================
                    $orden->update([
                        'estado_orden_id' =>
                            $estadoEntregado->id,

                        'fecha_entrega_real' =>
                            now(),
                    ]);


                    // ==================================
                    // HISTORIAL DE ESTADO
                    // ==================================
                    HistorialEstadoOrden::create([
                        'orden_trabajo_id' =>
                            $orden->id,

                        'estado_orden_id' =>
                            $estadoEntregado->id,

                        'registrado_por' =>
                            auth()->id(),

                        'motivo' =>
                            'Entrega realizada por mensajería.',

                        'observaciones' =>
                            'La orden fue marcada como entregada al completar la visita de mensajería.',

                        'fecha' =>
                            now(),
                    ]);


                    // ==================================
                    // GARANTÍA
                    // ==================================
                    if (!$orden->garantia) {

                        Garantia::create([
                            'orden_trabajo_id' =>
                                $orden->id,

                            'fecha_inicio' =>
                                now()->toDateString(),

                            'fecha_vencimiento' =>
                                now()
                                    ->addMonths(3)
                                    ->toDateString(),

                            'estado' =>
                                'Vigente',

                            'observaciones' =>
                                'Garantía generada automáticamente por entrega realizada mediante mensajería.',
                        ]);
                    }
                }
            }
        });

        return redirect()
            ->route(
                'mensajeria.show',
                $detalle->ruta_mensajeria_id
            )
            ->with(
                'success',
                'Estado de la visita actualizado correctamente.'
            );
    }
    public function reprogramarForm(DetalleMensajeria $detalle) 
        {
            // Cargar información de la visita
            $detalle->load([
                'ordenTrabajo',
                'odontologo',
                'clinica',
            ]);

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

            $yaFueReprogramada = DetalleMensajeria::where(
                'reprogramada_desde_id',
                $detalle->id
            )->exists();

            if ($yaFueReprogramada) {
                return redirect()
                    ->route(
                        'mensajeria.show',
                        $detalle->ruta_mensajeria_id
                    )
                    ->with(
                        'error',
                        'Esta visita ya fue asignada a una nueva ruta.'
                    );
            }
            /*
            * Solo rutas PENDIENTES.
            * No permitimos agregar una visita nueva
            * a una ruta que ya salió.
            */
            $rutasDisponibles = RutaMensajeria::with('mensajero')
                ->where('estado', 'Pendiente')
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
    public function reprogramar(Request $request, DetalleMensajeria $detalle ) 
    {
        if ($detalle->estado !== 'Reprogramada') {

            return redirect()
                ->route(
                    'mensajeria.show',
                    $detalle->ruta_mensajeria_id
                )
                ->with(
                    'error',
                    'Esta visita no está disponible para reprogramación.'
                );
            }
            $datos = $request->validate([
                'ruta_mensajeria_id' => [
                    'required',
                    'exists:rutas_mensajeria,id',
                ],
            ]);


            $nuevaRuta = RutaMensajeria::findOrFail(
            $datos['ruta_mensajeria_id']
        );

             $yaFueReprogramada = DetalleMensajeria::where(
                'reprogramada_desde_id',
                $detalle->id
            )->exists();

            if ($yaFueReprogramada) {
                return redirect()
                    ->route(
                        'mensajeria.show',
                        $detalle->ruta_mensajeria_id
                    )
                    ->with(
                        'error',
                        'Esta visita ya fue asignada a una nueva ruta.'
                    );
            }

        if (
                $nuevaRuta->estado !== 'Pendiente' ||
                $nuevaRuta->id === $detalle->ruta_mensajeria_id
            ) {

                return redirect()
                    ->back()
                    ->withErrors([
                        'ruta_mensajeria_id' =>
                            'La ruta seleccionada no está disponible para reprogramación.',
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

            'reprogramada_desde_id' => $detalle->id,

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

            'firma_recibido' => null,

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