<?php

namespace App\Models;

use App\Support\ImporteCajaChica;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CajaChicaGasto extends Model
{
    public const ESTADOS = ['Registrado', 'Pendiente de autorización', 'Autorizado', 'Rechazado', 'Anulado'];
    public const CONTABILIZADOS = ['Registrado', 'Autorizado'];
    protected $table = 'caja_chica_gastos';
    protected $fillable = [
        'caja_chica_periodo_id', 'clave_registro', 'fecha_gasto', 'concepto', 'beneficiario',
        'responsable', 'cantidad_milesimas', 'precio_unitario_centavos', 'numero_documento',
        'monto_centavos', 'metodo_pago', 'tipo_comprobante', 'nit', 'referencia', 'comprobante_path',
        'observaciones', 'estado', 'requiere_autorizacion', 'registrado_por', 'autorizado_por',
        'fecha_autorizacion', 'motivo_decision', 'version',
    ];
    protected $casts = [
        'fecha_gasto' => 'date', 'monto_centavos' => 'integer', 'requiere_autorizacion' => 'boolean',
        'fecha_autorizacion' => 'datetime', 'version' => 'integer',
        'cantidad_milesimas' => 'integer', 'precio_unitario_centavos' => 'integer',
    ];

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(CajaChicaPeriodo::class, 'caja_chica_periodo_id');
    }

    public function usuarioRegistro(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function usuarioAutorizacion(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autorizado_por');
    }

    public function correcciones(): HasMany
    {
        return $this->hasMany(CajaChicaCorreccion::class);
    }

    public function getMontoAttribute(): string
    {
        return ImporteCajaChica::decimal($this->monto_centavos);
    }

    public function getCantidadAttribute(): string
    {
        return ImporteCajaChica::cantidadDecimal($this->cantidad_milesimas);
    }

    public function getPrecioUnitarioAttribute(): string
    {
        return ImporteCajaChica::decimal($this->precio_unitario_centavos);
    }

    public function datosHistorial(): array
    {
        $datos = $this->only([
            'caja_chica_periodo_id', 'concepto', 'beneficiario', 'monto_centavos', 'metodo_pago',
            'responsable', 'cantidad_milesimas', 'precio_unitario_centavos', 'numero_documento',
            'tipo_comprobante', 'nit', 'referencia', 'comprobante_path', 'observaciones', 'estado',
            'requiere_autorizacion', 'registrado_por', 'autorizado_por', 'motivo_decision', 'version',
        ]);
        $datos['fecha_gasto'] = $this->fecha_gasto->toDateString();
        $datos['fecha_autorizacion'] = $this->fecha_autorizacion?->toDateTimeString();

        return $datos;
    }
}
