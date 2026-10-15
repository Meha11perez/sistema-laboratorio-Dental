<?php

namespace App\Services;

use App\Models\{Compra, CompraCorreccion, Material, MovimientoInventario, User};
use App\Support\ImporteCajaChica as Importe;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\{DB, Validator};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RegistroCompra
{
    public static function acceso(User $usuario, bool $admin = false): void
    {
        abort_unless($usuario->estado && $usuario->role?->estado && in_array($usuario->role?->nombre,
            $admin ? ['Administrador'] : ['Administrador', 'Contabilidad'], true), 403);
    }

    public static function validar(array $datos): array
    {
        return Validator::make($datos, [
            'fecha' => ['required', 'date_format:Y-m-d', 'before_or_equal:'.now('America/Guatemala')->toDateString()],
            'proveedor' => ['required', 'string', 'max:150'],
            'nit' => ['required', 'string', 'max:30'],
            'serie' => ['nullable', 'string', 'max:50'],
            'numero_factura' => ['required', 'string', 'max:100'],
            'metodo_pago' => ['required', 'in:Efectivo,Transferencia,Depósito,Cheque,Otro'],
            'referencia' => ['nullable', 'required_if:metodo_pago,Transferencia,Depósito,Cheque', 'string', 'max:150'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
            'detalles' => ['required', 'array', 'min:1', 'max:50'],
            'detalles.*.tipo' => ['required', 'in:Material,Servicio'],
            'detalles.*.material_id' => ['nullable', 'required_if:detalles.*.tipo,Material', function ($atributo, $valor, $fallar) {
                if ($valor === 'nuevo') return;
                if (!filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) || !Material::whereKey($valor)->exists()) {
                    $fallar('Seleccione un material existente o la opción «El material no está en el listado».');
                }
            }],
            'detalles.*.material_nombre' => ['nullable', 'required_if:detalles.*.material_id,nuevo', 'string', 'max:150'],
            'detalles.*.material_unidad' => ['nullable', 'required_if:detalles.*.material_id,nuevo', 'string', 'max:50'],
            'detalles.*.descripcion' => ['required', 'string', 'max:255'],
            'detalles.*.cantidad' => ['required', 'regex:/^\d{1,6}(?:\.\d{1,2})?$/D', 'numeric', 'min:0.01', 'max:999999.99'],
            'detalles.*.precio' => ['required', 'regex:/^\d{1,6}(?:\.\d{1,2})?$/D', 'numeric', 'min:0.01', 'max:999999.99'],
        ], ['detalles.*.cantidad.regex' => 'La cantidad admite punto decimal y hasta dos decimales.',
            'detalles.*.precio.regex' => 'El precio admite punto decimal y hasta dos decimales.'])->validate();
    }

    private static function campos(array $datos, string $archivo, ?Compra $actual = null): array
    {
        $datos = self::validar($datos);
        $detalles = [];
        foreach ($datos['detalles'] as $i => $linea) {
            $nuevo = $linea['tipo'] === 'Material' && ($linea['material_id'] ?? null) === 'nuevo';
            $material = $linea['tipo'] === 'Material' && !$nuevo ? Material::findOrFail($linea['material_id']) : null;
            $nombreNuevo = $nuevo ? preg_replace('/\s+/u', ' ', trim($linea['material_nombre'])) : null;
            $unidadNueva = $nuevo ? preg_replace('/\s+/u', ' ', trim($linea['material_unidad'])) : null;
            if ($nuevo && ($nombreNuevo === '' || $unidadNueva === '')) self::error('detalles.'.$i.'.material_nombre', 'Ingrese nombre y unidad del material solicitado.');
            $detalleAnterior = $actual?->detalles->get($i);
            $yaUsado = $actual && $actual->detalles->contains('material_id', $material?->id);
            if ($material && !$material->estado && !$yaUsado) self::error('detalles.'.$i.'.material_id', 'Seleccione un material activo.');
            $cantidad = Importe::centavos($linea['cantidad']);
            $precio = Importe::centavos($linea['precio']);
            $total = intdiv($cantidad * $precio + 50, 100);
            if ($total < 1) self::error('detalles.'.$i.'.precio', 'El total de cada línea debe ser al menos Q0.01.');
            $detalles[] = ['tipo' => $linea['tipo'], 'material_id' => $material?->id,
                'material_solicitado' => $nombreNuevo ?? ($material && (int) ($detalleAnterior?->material_id ?? 0) === (int) $material->id ? $detalleAnterior?->material_solicitado : null),
                'descripcion' => trim($linea['descripcion']),
                'unidad' => $unidadNueva ?? $material?->unidad_medida ?? 'Servicio', 'cantidad_centesimas' => $cantidad, 'precio_centavos' => $precio, 'total_centavos' => $total];
        }
        $campos = [
            'fecha' => $datos['fecha'], 'proveedor' => trim($datos['proveedor']),
            'nit' => strtoupper(preg_replace('/[\s-]+/u', '', $datos['nit'])),
            'serie' => mb_strtoupper(trim($datos['serie'] ?? '')), 'numero_factura' => mb_strtoupper(trim($datos['numero_factura'])),
            'metodo_pago' => $datos['metodo_pago'], 'referencia' => trim($datos['referencia'] ?? '') ?: null,
            'observaciones' => trim($datos['observaciones'] ?? '') ?: null, 'factura_path' => $archivo,
            'total_centavos' => array_sum(array_column($detalles, 'total_centavos')), 'detalles' => $detalles,
        ];
        foreach (['proveedor', 'nit', 'numero_factura', 'factura_path'] as $campo) {
            if ($campos[$campo] === '') self::error($campo, 'Este campo es obligatorio.');
        }
        return $campos;
    }

    public static function registrar(array $datos, User $usuario, string $archivo): Compra
    {
        self::acceso($usuario);
        Validator::make($datos, ['clave_registro' => ['required', 'uuid']])->validate();
        try {
            return DB::transaction(function () use ($datos, $usuario, $archivo) {
                if ($existente = Compra::where('clave_registro', $datos['clave_registro'])->first()) {
                    abort_unless((int) $existente->registrado_por === (int) $usuario->id, 403);
                    return $existente;
                }
                $campos = self::campos($datos, $archivo);
                $lineas = $campos['detalles']; unset($campos['detalles']);
                $compra = Compra::create($campos + ['clave_registro' => $datos['clave_registro'], 'registrado_por' => $usuario->id, 'estado' => 'Registrada', 'version' => 1]);
                $compra->detalles()->createMany($lineas);
                self::historial($compra, $usuario, 'Registro', 'Compra registrada con factura. No se han ingresado materiales al inventario.');
                return $compra;
            }, 3);
        } catch (UniqueConstraintViolationException $e) {
            $existente = Compra::where('clave_registro', $datos['clave_registro'])->first();
            if (!$existente) self::error('numero_factura', 'Ya existe una compra con este NIT, serie y número de factura.');
            abort_unless((int) $existente->registrado_por === (int) $usuario->id, 403);
            return $existente;
        }
    }

    public static function recibir(int $id, int $version, string $motivo, User $usuario): Compra
    {
        self::acceso($usuario, true);
        self::motivo($motivo);
        return DB::transaction(function () use ($id, $version, $motivo, $usuario) {
            $compra = Compra::whereKey($id)->lockForUpdate()->firstOrFail();
            if ($compra->estado === 'Anulada') self::error('compra', 'La compra está anulada.');
            // Reintentar una recepción ya realizada no crea otra entrada.
            if ($compra->fecha_recepcion) return $compra;
            self::version($compra, $version);
            self::sinSolicitud($compra);
            $lineas = $compra->detalles()->where('tipo', 'Material')->get();
            if ($lineas->isEmpty()) self::error('compra', 'Esta compra solo contiene servicios; no requiere entrada de inventario.');
            // Las altas se crean únicamente aquí, tras la autorización del Administrador.
            // La restricción única de nombre reutiliza el catálogo si otra compra ya lo creó.
            foreach ($lineas->whereNull('material_id')->sortBy('material_solicitado') as $linea) {
                if (!$linea->material_solicitado) self::error('compra', 'Un material no tiene nombre solicitado. Corrija la compra antes de recibir.');
                $material = Material::firstOrCreate(['nombre' => $linea->material_solicitado], [
                    'codigo' => 'CMP-'.str_replace('-', '', (string) Str::uuid()), 'unidad_medida' => $linea->unidad,
                    'stock_actual' => '0.00', 'stock_minimo' => '0.00', 'costo_unitario' => Importe::decimal($linea->precio_centavos),
                    'estado' => true, 'descripcion' => 'Creado al recibir la compra #'.$compra->id.'.',
                ]);
                if ($material->wasRecentlyCreated) self::asignarCodigoCorto($material);
                $linea->update(['material_id' => $material->id]);
            }
            $materiales = Material::whereIn('id', $lineas->pluck('material_id'))->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            foreach ($lineas as $linea) {
                if ($linea->movimiento_entrada_id) self::error('compra', 'Existe una recepción previa. Revise el historial.');
                $material = $materiales->get($linea->material_id);
                if (!$material || !$material->estado) self::error('compra', 'Un material está inactivo. Revise el catálogo antes de recibirlo.');
                if (mb_strtolower(trim($material->unidad_medida)) !== mb_strtolower(trim($linea->unidad))) {
                    self::error('compra', 'La unidad de «'.$material->nombre.'» no coincide con el catálogo. Solicite corregir la compra antes de recibir; no se convertirán cantidades automáticamente.');
                }
                $anterior = Importe::centavos($material->stock_actual);
                $nuevo = $anterior + $linea->cantidad_centesimas;
                if ($nuevo > 9999999999) self::error('compra', 'La entrada supera el máximo de existencias que admite el inventario.');
                $movimiento = MovimientoInventario::create([
                    'material_id' => $material->id, 'registrado_por' => $usuario->id, 'tipo_movimiento' => 'Entrada',
                    'cantidad' => Importe::decimal($linea->cantidad_centesimas), 'stock_anterior' => Importe::decimal($anterior),
                    'stock_nuevo' => Importe::decimal($nuevo), 'fecha_movimiento' => now(),
                    'observaciones' => 'Recepción de compra #'.$compra->id.', detalle #'.$linea->id.'. '.$motivo,
                ]);
                $material->update(['stock_actual' => Importe::decimal($nuevo)]);
                $linea->update(['movimiento_entrada_id' => $movimiento->id]);
            }
            $compra->update(['recibido_por' => $usuario->id, 'fecha_recepcion' => now(), 'version' => $compra->version + 1]);
            self::historial($compra, $usuario, 'Recepción', $motivo);
            return $compra;
        }, 3);
    }

    public static function solicitar(int $id, array $datos, User $usuario, ?string $archivo): CompraCorreccion
    {
        self::acceso($usuario);
        Validator::make($datos, ['clave_registro' => ['required', 'uuid'], 'version_compra' => ['required', 'integer', 'min:1'],
            'tipo' => ['required', 'in:Corregir,Anular'], 'motivo' => ['required', 'string', 'min:5', 'max:1000']])->validate();
        self::motivo($datos['motivo']);
        try {
            return DB::transaction(function () use ($id, $datos, $usuario, $archivo) {
                $compra = Compra::whereKey($id)->lockForUpdate()->firstOrFail();
                if ($existente = CompraCorreccion::where('clave_registro', $datos['clave_registro'])->first()) {
                    abort_unless((int) $existente->solicitado_por === (int) $usuario->id && (int) $existente->compra_id === $id, 403);
                    return $existente;
                }
                if ($compra->estado === 'Anulada') self::error('compra', 'Una compra anulada conserva su historial y no admite modificaciones.');
                self::version($compra, (int) $datos['version_compra']); self::sinSolicitud($compra);
                $antes = $compra->instantanea();
                $propuestos = $datos['tipo'] === 'Corregir' ? self::campos($datos, $archivo ?? $compra->factura_path, $compra) : null;
                self::restriccionesRecibida($compra, $datos['tipo'], $propuestos);
                if ($propuestos !== null && !array_filter($propuestos, fn ($v, $k) => $v !== ($antes[$k] ?? null), ARRAY_FILTER_USE_BOTH)) {
                    self::error('compra', 'No hay cambios para solicitar.');
                }
                $solicitud = $compra->correcciones()->create([
                    'clave_registro' => $datos['clave_registro'], 'version_compra' => $compra->version, 'tipo' => $datos['tipo'],
                    'anteriores' => $antes, 'propuestos' => $propuestos, 'motivo' => trim($datos['motivo']),
                    'solicitado_por' => $usuario->id, 'estado' => 'Pendiente',
                ]);
                self::historial($compra, $usuario, 'Solicitud de '.$datos['tipo'], $datos['motivo']);
                return $solicitud;
            }, 3);
        } catch (UniqueConstraintViolationException $e) {
            $existente = CompraCorreccion::where('clave_registro', $datos['clave_registro'])->first();
            if (!$existente) throw $e;
            abort_unless((int) $existente->solicitado_por === (int) $usuario->id && (int) $existente->compra_id === $id, 403);
            return $existente;
        }
    }

    public static function resolver(int $id, string $decision, string $motivo, User $usuario): CompraCorreccion
    {
        self::acceso($usuario, true); self::motivo($motivo);
        if (!in_array($decision, ['aprobar', 'rechazar'], true)) self::error('decision', 'Decisión no válida.');
        try {
            return DB::transaction(function () use ($id, $decision, $motivo, $usuario) {
                $referencia = CompraCorreccion::findOrFail($id);
                $compra = Compra::whereKey($referencia->compra_id)->lockForUpdate()->firstOrFail();
                $solicitud = CompraCorreccion::whereKey($id)->lockForUpdate()->firstOrFail();
                if ($solicitud->estado !== 'Pendiente') self::error('compra', 'Esta solicitud ya fue atendida.');
                self::version($compra, $solicitud->version_compra);
                if ($decision === 'aprobar') {
                    self::restriccionesRecibida($compra, $solicitud->tipo, $solicitud->propuestos);
                    if ($solicitud->tipo === 'Anular') {
                        $compra->update(['estado' => 'Anulada', 'version' => $compra->version + 1]);
                    } else {
                        $campos = $solicitud->propuestos;
                        $lineas = $campos['detalles']; unset($campos['detalles']);
                        $compra->update($campos + ['version' => $compra->version + 1]);
                        if ($compra->fecha_recepcion) {
                            foreach ($compra->detalles as $i => $linea) $linea->update($lineas[$i]);
                        } else {
                            $compra->detalles()->delete();
                            $compra->detalles()->createMany($lineas);
                        }
                    }
                }
                $solicitud->update(['estado' => $decision === 'aprobar' ? 'Aprobada' : 'Rechazada',
                    'resuelto_por' => $usuario->id, 'fecha_resolucion' => now(), 'motivo_resolucion' => trim($motivo),
                    'aplicados' => $decision === 'aprobar' ? $compra->fresh()->instantanea() : null]);
                self::historial($compra->fresh(), $usuario, 'Solicitud '.$solicitud->estado, $motivo);
                return $solicitud;
            }, 3);
        } catch (UniqueConstraintViolationException $e) {
            self::error('numero_factura', 'La factura propuesta ya existe. Rechace la solicitud y registre una nueva con los datos correctos.');
        }
    }

    private static function asignarCodigoCorto(Material $material): void
    {
        // El ID ya fue asignado por la base. El código temporal solo existe
        // dentro de la transacción y se reemplaza antes de confirmar el alta.
        $numero = (int) $material->id;
        for ($intento = 0; $intento < 1000; $intento++, $numero++) {
            $codigo = 'MAT-'.str_pad((string) $numero, 3, '0', STR_PAD_LEFT);
            if (Material::where('codigo', $codigo)->whereKeyNot($material->id)->exists()) continue;
            try {
                // El índice único es la protección final ante otra escritura simultánea.
                DB::transaction(fn () => $material->update(['codigo' => $codigo]));
                return;
            } catch (UniqueConstraintViolationException $error) {
                if (!Material::where('codigo', $codigo)->whereKeyNot($material->id)->exists()) throw $error;
            }
        }
        self::error('compra', 'No se pudo reservar un código de material. Revise el catálogo e intente nuevamente.');
    }

    private static function restriccionesRecibida(Compra $compra, string $tipo, ?array $campos): void
    {
        if (!$compra->fecha_recepcion) return;
        if ($tipo === 'Anular') self::error('compra', 'La compra ya tiene entradas de inventario. No puede anularse desde Compras; requiere revisar los movimientos con el Administrador.');
        $original = $compra->instantanea()['detalles'];
        $firma = fn ($lineas) => array_map(fn ($d) => [$d['tipo'], $d['material_id'], $d['unidad'], $d['cantidad_centesimas']], $lineas);
        if ($firma($original) !== $firma($campos['detalles'])) self::error('compra', 'Después de recibir, no se pueden cambiar materiales, unidades ni cantidades. Los precios y datos de factura sí admiten solicitud.');
    }

    private static function version(Compra $compra, int $version): void
    {
        if ($compra->version !== $version) self::error('compra', 'La compra cambió. Recargue su detalle antes de continuar.');
    }

    private static function sinSolicitud(Compra $compra): void
    {
        if ($compra->correcciones()->where('estado', 'Pendiente')->exists()) self::error('compra', 'Resuelva primero la solicitud pendiente de esta compra.');
    }

    private static function motivo(string $motivo): void
    {
        if (mb_strlen(trim($motivo)) < 5 || mb_strlen($motivo) > 1000) self::error('motivo', 'Ingrese un motivo de entre 5 y 1000 caracteres.');
    }

    private static function historial(Compra $compra, User $usuario, string $accion, string $motivo): void
    {
        $compra->unsetRelation('detalles');
        $compra->historiales()->create(['usuario_id' => $usuario->id, 'accion' => $accion, 'motivo' => trim($motivo), 'datos' => $compra->instantanea()]);
    }

    private static function error(string $campo, string $mensaje): never
    {
        throw ValidationException::withMessages([$campo => $mensaje]);
    }
}
