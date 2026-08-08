<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paciente extends Model
{
     protected $fillable = [
        'odontologo_id',
        'nombre',
        'apellido',
        'telefono',
        'observaciones',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function odontologo(): BelongsTo
    {
        return $this->belongsTo(Odontologo::class);
    }
    public function ordenesTrabajo(): HasMany
{
    return $this->hasMany(OrdenTrabajo::class);
}
}
