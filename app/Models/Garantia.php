<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Garantia extends Model
{
    protected $fillable = [
        'orden_trabajo_id',
        'fecha_inicio',
        'fecha_vencimiento',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_vencimiento' => 'date',
    ];

    public function ordenTrabajo(): BelongsTo
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function devoluciones(): HasMany
    {
        return $this->hasMany(Devolucion::class);
    }
}