<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialEstadoOrden extends Model
{
    protected $table = 'historial_estados_orden';

    protected $fillable = [
        'orden_trabajo_id',
        'estado_orden_id',
        'registrado_por',
        'motivo',
        'observaciones',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function ordenTrabajo(): BelongsTo
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function estadoOrden(): BelongsTo
    {
        return $this->belongsTo(EstadoOrden::class);
    }

    public function usuarioRegistro(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}