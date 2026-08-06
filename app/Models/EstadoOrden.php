<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoOrden extends Model
{
      protected $table = 'estados_orden';

    protected $fillable = [
        'nombre',
        'descripcion',
        'color',
        'orden',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'orden' => 'integer',
    ];

    public function ordenesTrabajo(): HasMany
    {
        return $this->hasMany(OrdenTrabajo::class);
    }
}
