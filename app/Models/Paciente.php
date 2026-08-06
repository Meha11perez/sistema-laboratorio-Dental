<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paciente extends Model
{
     protected $fillable = [
        'odontologo_id',
        'nombre',
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
}
