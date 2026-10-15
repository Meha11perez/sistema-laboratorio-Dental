<?php

namespace App\Services;

use App\Models\{CajaChicaCorreccion, CajaChicaGasto, CajaChicaPeriodo, User};
use App\Support\ImporteCajaChica;
use Carbon\CarbonImmutable;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CajaChica
{
    public const LIMITE_CENTAVOS = 500000;

    public static function acceso(User $usuario, bool $soloAdministrador = false): void
    {
        $roles = $soloAdministrador ? ['Administrador'] : ['Administrador', 'Contabilidad'];
        abort_unless($usuario->estado && $usuario->role?->estado && in_array($usuario->role?->nombre, $roles, true), 403);
    }

    public static function mes(string $fecha): string
    {
        return CarbonImmutable::parse($fecha, 'America/Guatemala')->startOfMonth()->toDateString();
    }

    private static function periodoBloqueado(string $mes): CajaChicaPeriodo
    {
        // La clave única y este bloqueo serializan los registros del mismo mes.
        CajaChicaPeriodo::query()->insertOrIgnore([
            'mes' => $mes, 'limite_centavos' => self::LIMITE_CENTAVOS,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return CajaChicaPeriodo::where('mes', $mes)->lockForUpdate()->firstOrFail();
    }

    public static function gastado(CajaChicaPeriodo $periodo, ?int $excluir = null): int
    {
        return (int) $periodo->gastos()->whereIn('estado', CajaChicaGasto::CONTABILIZADOS)
            ->when($excluir !== null, fn ($q) => $q->whereKeyNot($excluir))->sum('monto_centavos');
    }

    public static function resumen(string $mes): array
    {
        $periodo = CajaChicaPeriodo::where('mes', self::mes($mes))->first();
        $limite = $periodo?->limite_centavos ?? self::LIMITE_CENTAVOS;
        $gastado = $periodo ? self::gastado($periodo) : 0;
        $pendientes = $periodo ? $periodo->gastos()->where('estado', 'Pendiente de autorización') : null;

        return [
            'limite' => $limite, 'gastado' => $gastado, 'disponible' => max(0, $limite - $gastado),
            'exceso' => max(0, $gastado - $limite), 'pendiente' => $pendientes ? (int) (clone $pendientes)->sum('monto_centavos') : 0,
            'cantidad_pendiente' => $pendientes ? $pendientes->count() : 0,
        ];
    }

    public static function registrar(array $datos, User $usuario, ?string $archivo): CajaChicaGasto
    {
        self::acceso($usuario);
        try {
            return DB::transaction(function () use ($datos, $usuario, $archivo) {
                $periodo = self::periodoBloqueado(self::mes($datos['fecha_gasto']));
                if ($existente = CajaChicaGasto::where('clave_registro', $datos['clave_registro'])->first()) {
                    abort_unless((int) $existente->registrado_por === (int) $usuario->id, 403);
                    return $existente;
                }
                $campos = self::camposGasto($datos, $archivo);
                $monto = $campos['monto_centavos'];
                $excede = self::gastado($periodo) + $monto > $periodo->limite_centavos;

                return CajaChicaGasto::create(array_merge($campos, [
                    'caja_chica_periodo_id' => $periodo->id, 'clave_registro' => $datos['clave_registro'],
                    'estado' => $excede ? 'Pendiente de autorización' : 'Registrado',
                    'requiere_autorizacion' => $excede, 'registrado_por' => $usuario->id,
                ]));
            }, 3);
        } catch (UniqueConstraintViolationException $error) {
            $existente = CajaChicaGasto::where('clave_registro', $datos['clave_registro'])->first();
            if (!$existente) {
                throw $error;
            }
            abort_unless((int) $existente->registrado_por === (int) $usuario->id, 403);
            return $existente;
        }
    }

    private static function camposGasto(array $datos, ?string $archivo): array
    {
        $cantidad = ImporteCajaChica::cantidadMilesimas($datos['cantidad']);
        $precio = ImporteCajaChica::centavos($datos['precio_unitario']);
        return [
            'fecha_gasto' => $datos['fecha_gasto'], 'concepto' => $datos['concepto'],
            'beneficiario' => $datos['beneficiario'] ?? null,
            'responsable' => $datos['responsable'], 'cantidad_milesimas' => $cantidad,
            'precio_unitario_centavos' => $precio, 'monto_centavos' => ImporteCajaChica::total($cantidad, $precio),
            'metodo_pago' => $datos['metodo_pago'], 'tipo_comprobante' => $datos['tipo_comprobante'],
            'nit' => $datos['nit'] ?? null, 'referencia' => $datos['referencia'] ?? null,
            'numero_documento' => $datos['numero_documento'] ?? null,
            'comprobante_path' => $archivo, 'observaciones' => $datos['observaciones'] ?? null,
        ];
    }

    public static function resolverExcedente(int $id, string $decision, string $motivo, User $usuario): CajaChicaGasto
    {
        self::acceso($usuario, true);
        self::validarDecision($decision, $motivo);

        return DB::transaction(function () use ($id, $decision, $motivo, $usuario) {
            $referencia = CajaChicaGasto::findOrFail($id);
            self::periodoBloqueado(self::mes($referencia->fecha_gasto->toDateString()));
            $gasto = CajaChicaGasto::whereKey($id)->lockForUpdate()->firstOrFail();
            if ($gasto->estado !== 'Pendiente de autorización') {
                self::error('Esta solicitud ya fue atendida o el gasto cambió. Recargue la página.');
            }
            if ($gasto->correcciones()->where('estado', 'Pendiente')->exists()) {
                self::error('Resuelva primero la solicitud de corrección de este gasto.');
            }
            $gasto->update([
                'estado' => $decision === 'aprobar' ? 'Autorizado' : 'Rechazado',
                'autorizado_por' => $usuario->id, 'fecha_autorizacion' => now(),
                'motivo_decision' => $motivo, 'version' => $gasto->version + 1,
            ]);

            return $gasto;
        }, 3);
    }

    public static function solicitarCorreccion(int $id, array $datos, User $usuario, ?string $archivo): CajaChicaCorreccion
    {
        self::acceso($usuario);
        try {
            return DB::transaction(function () use ($id, $datos, $usuario, $archivo) {
                $referencia = CajaChicaGasto::findOrFail($id);
                self::periodoBloqueado(self::mes($referencia->fecha_gasto->toDateString()));
                $gasto = CajaChicaGasto::whereKey($id)->lockForUpdate()->firstOrFail();
                if ($existente = CajaChicaCorreccion::where('clave_registro', $datos['clave_registro'])->first()) {
                    abort_unless((int) $existente->solicitado_por === (int) $usuario->id && (int) $existente->caja_chica_gasto_id === $id, 403);
                    return $existente;
                }
                if (in_array($gasto->estado, ['Anulado', 'Rechazado'], true)) {
                    self::error('Un gasto anulado o rechazado no admite nuevas correcciones.');
                }
                if ($gasto->version !== (int) $datos['version_gasto']) {
                    self::error('El gasto cambió desde que abrió el formulario. Consulte de nuevo su detalle.');
                }
                if ($gasto->correcciones()->where('estado', 'Pendiente')->exists()) {
                    self::error('Este gasto ya tiene una corrección pendiente.');
                }
                $propuestos = $datos['tipo'] === 'Corregir'
                    ? self::camposGasto($datos, $archivo ?? $gasto->comprobante_path) : null;
                if ($propuestos !== null) {
                    $anteriores = $gasto->datosHistorial();
                    $hayCambios = array_filter($propuestos, fn ($valor, $campo) => $valor !== ($anteriores[$campo] ?? null), ARRAY_FILTER_USE_BOTH);
                    if (!$hayCambios) {
                        self::error('No hay cambios para solicitar. Modifique el dato que necesita corregir.');
                    }
                }

                return CajaChicaCorreccion::create([
                    'caja_chica_gasto_id' => $gasto->id, 'clave_registro' => $datos['clave_registro'],
                    'version_gasto' => $gasto->version, 'tipo' => $datos['tipo'],
                    'datos_anteriores' => $gasto->datosHistorial(), 'datos_propuestos' => $propuestos,
                    'motivo_solicitud' => $datos['motivo_solicitud'], 'estado' => 'Pendiente',
                    'solicitado_por' => $usuario->id,
                ]);
            }, 3);
        } catch (UniqueConstraintViolationException $error) {
            $existente = CajaChicaCorreccion::where('clave_registro', $datos['clave_registro'])->first();
            if (!$existente) {
                throw $error;
            }
            abort_unless((int) $existente->solicitado_por === (int) $usuario->id && (int) $existente->caja_chica_gasto_id === $id, 403);
            return $existente;
        }
    }

    public static function resolverCorreccion(int $id, string $decision, string $motivo, User $usuario): CajaChicaCorreccion
    {
        self::acceso($usuario, true);
        self::validarDecision($decision, $motivo);

        return DB::transaction(function () use ($id, $decision, $motivo, $usuario) {
            $referencia = CajaChicaCorreccion::with('gasto')->findOrFail($id);
            $meses = [self::mes($referencia->gasto->fecha_gasto->toDateString())];
            if ($referencia->tipo === 'Corregir') {
                $meses[] = self::mes($referencia->datos_propuestos['fecha_gasto']);
            }
            sort($meses);
            $periodos = [];
            foreach (array_unique($meses) as $mes) {
                $periodos[$mes] = self::periodoBloqueado($mes);
            }
            $gasto = CajaChicaGasto::whereKey($referencia->caja_chica_gasto_id)->lockForUpdate()->firstOrFail();
            $solicitud = CajaChicaCorreccion::whereKey($id)->lockForUpdate()->firstOrFail();
            if ($solicitud->estado !== 'Pendiente') {
                self::error('Esta corrección ya fue atendida. Recargue la página.');
            }
            if ($gasto->version !== $solicitud->version_gasto) {
                self::error('El gasto cambió después de solicitar esta corrección.');
            }
            if ($decision === 'aprobar') {
                if ($solicitud->tipo === 'Anular') {
                    $gasto->update(['estado' => 'Anulado', 'version' => $gasto->version + 1]);
                } else {
                    $datos = $solicitud->datos_propuestos;
                    $periodo = $periodos[self::mes($datos['fecha_gasto'])];
                    $excede = self::gastado($periodo, $gasto->id) + $datos['monto_centavos'] > $periodo->limite_centavos;
                    $gasto->update(array_merge($datos, [
                        'caja_chica_periodo_id' => $periodo->id, 'estado' => $excede ? 'Autorizado' : 'Registrado',
                        'requiere_autorizacion' => $excede, 'autorizado_por' => $usuario->id,
                        'fecha_autorizacion' => now(), 'motivo_decision' => 'Corrección #'.$solicitud->id.' aprobada: '.$motivo,
                        'version' => $gasto->version + 1,
                    ]));
                }
            }
            $solicitud->update([
                'estado' => $decision === 'aprobar' ? 'Aprobada' : 'Rechazada',
                'resuelto_por' => $usuario->id, 'fecha_resolucion' => now(), 'motivo_resolucion' => $motivo,
                'datos_resultantes' => $decision === 'aprobar' ? $gasto->datosHistorial() : null,
            ]);

            return $solicitud;
        }, 3);
    }

    private static function validarDecision(string $decision, string $motivo): void
    {
        if (!in_array($decision, ['aprobar', 'rechazar'], true) || trim($motivo) === '') {
            self::error('Seleccione una decisión e indique el motivo.');
        }
    }

    private static function error(string $mensaje): never
    {
        throw ValidationException::withMessages(['caja_chica' => $mensaje]);
    }
}
