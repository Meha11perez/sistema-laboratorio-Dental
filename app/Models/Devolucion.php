<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Devolucion extends Model
{
    protected $fillable = [
        'orden_trabajo_id',
        'garantia_id',
        'tecnico_responsable_id',
        'registrado_por',
        'tipo',
        'motivo',
        'fecha_devolucion',
        'requiere_repeticion',
        'perdida_estimada',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_devolucion' => 'datetime',
        'requiere_repeticion' => 'boolean',
        'perdida_estimada' => 'decimal:2',
    ];

    public function ordenTrabajo(): BelongsTo
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function garantia(): BelongsTo
    {
        return $this->belongsTo(Garantia::class);
    }

    public function tecnicoResponsable(): BelongsTo
    {
        return $this->belongsTo(
            Tecnico::class,
            'tecnico_responsable_id'
        );
    }

    public function usuarioRegistro(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'registrado_por'
        );
    }
}