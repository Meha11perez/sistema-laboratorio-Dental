<?php

namespace App\Http\Controllers;

use App\Models\EstadoOrden;
use App\Models\HistorialEstadoOrden;
use App\Models\HistorialProduccion;
use App\Models\OrdenTrabajo;
use App\Services\RevisionProduccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RevisionProduccionController extends Controller
{
    private function verificarAcceso(): void
    {
        abort_unless(in_array(auth()->user()?->role?->nombre, ['Administrador', 'Recepcion'], true), 403);
    }

    public function index()
    {
        $this->verificarAcceso();
        $pendientes = RevisionProduccion::pendientes()
            ->with(['ordenTrabajo.paciente', 'ordenTrabajo.odontologo', 'etapaProduccion', 'tecnico.user'])
            ->orderBy('fecha_fin')->orderBy('id')->paginate(15);

        $revisadas = HistorialProduccion::whereNotNull('fecha_revision')
            ->with(['ordenTrabajo', 'etapaProduccion', 'usuarioRevision'])
            ->orderByDesc('fecha_revision')->orderByDesc('id')->limit(10)->get();

        return view('produccion.revisiones.index', compact('pendientes', 'revisadas'));
    }

    public function show(HistorialProduccion $historial)
    {
        $this->verificarAcceso();

        if (!RevisionProduccion::pendientes()->whereKey($historial->id)->exists()) {
            return redirect()->route('produccion.revisiones.index')
                ->with('error', 'Esta etapa ya fue atendida o no tiene una revisión pendiente vigente.');
        }

        $historial->load(['ordenTrabajo.paciente', 'ordenTrabajo.odontologo', 'ordenTrabajo.etapaActual', 'etapaProduccion', 'tecnico.user']);
        $orden = $historial->ordenTrabajo;
        $siguienteEtapa = RevisionProduccion::siguienteEtapa($orden);
        $tecnicos = $siguienteEtapa ? RevisionProduccion::tecnicosDisponibles()->get()->sortBy('user.name') : collect();

        return view('produccion.revisiones.show', compact('historial', 'orden', 'siguienteEtapa', 'tecnicos'));
    }

    public function update(Request $request, HistorialProduccion $historial)
    {
        $this->verificarAcceso();
        $datos = $request->validate([
            'accion' => ['required', 'in:avanzar,terminar'],
            'tecnico_actual_id' => ['nullable', 'required_if:accion,avanzar', 'integer'],
            'observaciones_revision' => ['nullable', 'string', 'max:1000'],
        ]);

        $terminada = DB::transaction(function () use ($datos, $historial) {
            // Todas las operaciones del flujo bloquean primero la misma orden.
            $orden = OrdenTrabajo::whereKey($historial->orden_trabajo_id)->lockForUpdate()->firstOrFail();
            $historial = HistorialProduccion::whereKey($historial->id)->lockForUpdate()->firstOrFail();

            if (!RevisionProduccion::pendientes()->whereKey($historial->id)->exists()) {
                throw ValidationException::withMessages(['produccion' => 'Esta revisión ya fue atendida o la orden cambió. Actualice el listado de pendientes.']);
            }

            $siguiente = RevisionProduccion::siguienteEtapa($orden);
            $terminada = $siguiente === null;
            if (($terminada && $datos['accion'] !== 'terminar') || (!$terminada && $datos['accion'] !== 'avanzar')) {
                throw ValidationException::withMessages(['produccion' => 'La acción no coincide con la siguiente etapa configurada. Actualice la página de revisión.']);
            }

            $tecnico = null;
            if ($siguiente) {
                $tecnico = RevisionProduccion::tecnicosDisponibles()->whereKey($datos['tecnico_actual_id'])->first();
                if (!$tecnico) {
                    throw ValidationException::withMessages(['tecnico_actual_id' => 'Seleccione un técnico activo con una cuenta de Técnico activa.']);
                }
            }

            $estado = EstadoOrden::where('nombre', $terminada ? 'Terminado' : 'Pendiente')->firstOrFail();
            $fecha = now();
            $historial->update([
                'pendiente_revision' => false,
                'revisado_por' => auth()->id(),
                'fecha_revision' => $fecha,
            ]);

            $orden->update([
                'estado_orden_id' => $estado->id,
                'etapa_actual_id' => $siguiente?->id ?? $orden->etapa_actual_id,
                'tecnico_actual_id' => $tecnico?->id,
            ]);

            if ($siguiente) {
                HistorialProduccion::create([
                    'orden_trabajo_id' => $orden->id,
                    'etapa_produccion_id' => $siguiente->id,
                    'tecnico_id' => $tecnico->id,
                    'registrado_por' => auth()->id(),
                    'fecha_inicio' => $fecha,
                    'fecha_fin' => null,
                    'estado' => 'Pendiente',
                    'observaciones' => 'Etapa asignada tras la revisión; pendiente de inicio por el técnico.',
                    'pendiente_revision' => false,
                ]);
            }

            $texto = $terminada
                ? 'Trabajo revisado y marcado como Terminado; pendiente de programar su entrega.'
                : 'Avance confirmado a la etapa ' . $siguiente->nombre . '. Técnico asignado: ' . $tecnico->user->name . '.';
            if (!empty($datos['observaciones_revision'])) {
                $texto .= ' Observaciones de revisión: ' . $datos['observaciones_revision'];
            }

            HistorialEstadoOrden::create([
                'orden_trabajo_id' => $orden->id,
                'estado_orden_id' => $estado->id,
                'registrado_por' => auth()->id(),
                'motivo' => null,
                'observaciones' => $texto,
                'fecha' => $fecha,
            ]);

            return $terminada;
        });

        return redirect()->route('produccion.revisiones.index')->with('success', $terminada
            ? 'Orden marcada como Terminada. Ya puede programar su entrega en Mensajería.'
            : 'Siguiente etapa asignada correctamente. El técnico puede iniciar el trabajo.');
    }
}
