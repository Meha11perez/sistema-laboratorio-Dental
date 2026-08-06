<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class EtapaProduccion extends Model
{
     protected $table = 'etapas_produccion';

    protected $fillable = [
        'nombre',
        'descripcion',
        'orden',
        'estado',
    ];

    protected $casts = [
        'orden' => 'integer',
        'estado' => 'boolean',
    ];

    public function historialesProduccion(): HasMany
    {
        return $this->hasMany(HistorialProduccion::class);
    }
}
