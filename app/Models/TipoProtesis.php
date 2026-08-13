<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoProtesis extends Model
{
    protected $table = 'tipos_protesis';

    protected $fillable = [
        'nombre',
        'categoria',
        'descripcion',
        'estado',
    ];
    protected $casts = [
        'estado' => 'boolean',
    ];

    public function ordenesTrabajo(): HasMany
        {
            return $this->hasMany(OrdenTrabajo::class);
        }
}
