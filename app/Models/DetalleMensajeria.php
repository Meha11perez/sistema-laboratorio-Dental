<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleMensajeria extends Model
{
    protected $table = 'detalles_mensajeria';

    protected $fillable = [
        'ruta_mensajeria_id',
        'orden_trabajo_id',
        'odontologo_id',
        'clinica_id',
        'tipo_movimiento',
        'orden_visita',
        'direccion_referencia',
        'estado',
        'hora_realizada',
        'recibido_por',
        'observaciones',
    ];

    public function rutaMensajeria(): BelongsTo
    {
        return $this->belongsTo(RutaMensajeria::class);
    }

    public function ordenTrabajo(): BelongsTo
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function odontologo(): BelongsTo
    {
        return $this->belongsTo(Odontologo::class);
    }

    public function clinica(): BelongsTo
    {
        return $this->belongsTo(Clinica::class);
    }
}