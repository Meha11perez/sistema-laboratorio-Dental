<?php

namespace App\Services;

use App\Models\EtapaProduccion;
use App\Models\HistorialProduccion;
use App\Models\OrdenTrabajo;
use App\Models\Tecnico;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class RevisionProduccion
{
    public static function pendientes(): Builder
    {
        $tabla = (new HistorialProduccion)->getTable();
        $tablaOrden = (new OrdenTrabajo)->getTable();

        return HistorialProduccion::query()
            ->where('pendiente_revision', true)
            ->where('estado', 'Completado')
            ->whereNotNull('fecha_fin')
            ->whereNull('fecha_revision')
            ->whereHas('ordenTrabajo', function (Builder $q) use ($tabla, $tablaOrden) {
                $q->whereNull('tecnico_actual_id')
                    ->whereColumn($tablaOrden . '.etapa_actual_id', $tabla . '.etapa_produccion_id')
                    ->whereHas('estadoOrden', fn (Builder $estado) => $estado->where('nombre', 'Pendiente'));
            })
            ->whereNotExists(function ($q) use ($tabla) {
                $q->selectRaw('1')->from($tabla . ' as hp_posterior')
                    ->whereColumn('hp_posterior.orden_trabajo_id', $tabla . '.orden_trabajo_id')
                    ->whereColumn('hp_posterior.id', '>', $tabla . '.id');
            });
    }

    public static function tienePendiente(OrdenTrabajo $orden): bool
    {
        return HistorialProduccion::where('orden_trabajo_id', $orden->id)
            ->where('pendiente_revision', true)->exists();
    }

    public static function validarTrabajoTecnico(OrdenTrabajo $orden, Tecnico $tecnico): void
    {
        abort_unless((int) $orden->tecnico_actual_id === (int) $tecnico->id, 403, 'Esta orden no está asignada a usted.');

        if (!$orden->etapa_actual_id) {
            throw ValidationException::withMessages(['produccion' => 'La orden no tiene una etapa asignada.']);
        }

        if (in_array($orden->estadoOrden?->nombre, ['Terminado', 'Entregado', 'Cancelado'], true)) {
            throw ValidationException::withMessages(['produccion' => 'Esta orden ya no puede modificarse en producción.']);
        }

        if (self::tienePendiente($orden)) {
            throw ValidationException::withMessages(['produccion' => 'La etapa completada está pendiente de revisión por Recepción o Administración.']);
        }
    }

    public static function validarEdicion(OrdenTrabajo $orden, array $datos): void
    {
        if (!self::tienePendiente($orden)) {
            return;
        }

        foreach (['area_trabajo', 'tipo_protesis_id', 'etapa_actual_id', 'tecnico_actual_id', 'estado_orden_id'] as $campo) {
            if (array_key_exists($campo, $datos) && (string) ($orden->{$campo} ?? '') !== (string) ($datos[$campo] ?? '')) {
                throw ValidationException::withMessages([
                    'produccion' => 'Esta orden tiene una etapa pendiente de revisión. Confirme su avance desde Revisiones de producción antes de cambiar el área, la etapa, el técnico o el estado.',
                ]);
            }
        }
    }

    public static function siguienteEtapa(OrdenTrabajo $orden): ?EtapaProduccion
    {
        $actual = $orden->etapaActual;

        if (!$actual || !$actual->permiteArea((string) $orden->area_trabajo)) {
            throw ValidationException::withMessages(['produccion' => 'La etapa actual no corresponde al área de la orden. El administrador debe revisar la configuración.']);
        }

        if ($actual->es_final) {
            return null;
        }

        $siguientes = EtapaProduccion::where('estado', true)
            ->where('orden', '>', $actual->orden)->orderBy('orden')->orderBy('id')->get()
            ->filter(fn (EtapaProduccion $etapa) => $etapa->permiteArea((string) $orden->area_trabajo))->values();

        $siguiente = $siguientes->first();
        if ($siguiente && $siguientes->where('orden', $siguiente->orden)->count() > 1) {
            throw ValidationException::withMessages(['produccion' => 'Hay varias etapas del área con el mismo orden. El administrador debe revisar su configuración.']);
        }

        return $siguiente;
    }

    public static function tecnicosDisponibles(): Builder
    {
        return Tecnico::with('user')->where('estado', true)
            ->whereHas('user', function (Builder $q) {
                $q->where('estado', true)->whereHas('role', function (Builder $rol) {
                    $rol->where('nombre', 'Técnico')->where('estado', true);
                });
            });
    }
}
