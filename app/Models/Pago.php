<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pago extends Model
{
    protected $fillable = [
        'orden_trabajo_id',
        'odontologo_id',
        'cuenta_odontologo_id',
        'registrado_por',
        'monto_total',
        'monto_pagado',
        'saldo_pendiente',
        'estado_pago',
        'fecha_registro',
        'fecha_vencimiento',
        'observaciones',
    ];

    protected $casts = [
        'monto_total' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
        'fecha_registro' => 'date',
        'fecha_vencimiento' => 'date',
    ];

    public function ordenTrabajo(): BelongsTo
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function odontologo(): BelongsTo
    {
        return $this->belongsTo(Odontologo::class);
    }

    public function cuentaOdontologo(): BelongsTo
    {
        return $this->belongsTo(CuentaOdontologo::class);
    }

    public function usuarioRegistro(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'registrado_por'
        );
    }

    public function abonos(): HasMany
    {
        return $this->hasMany(Abono::class);
    }
}