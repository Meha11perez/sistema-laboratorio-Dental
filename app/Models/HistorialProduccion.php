<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialProduccion extends Model
{
    protected $table = 'historial_produccion';

    protected $fillable = [
        'orden_trabajo_id',
        'etapa_produccion_id',
        'tecnico_id',
        'registrado_por',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'observaciones',
        'duracion_minutos',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];

    public function ordenTrabajo(): BelongsTo
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function etapaProduccion(): BelongsTo
    {
        return $this->belongsTo(EtapaProduccion::class);
    }

    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(Tecnico::class);
    }

    public function usuarioRegistro(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'registrado_por'
        );
    }
}