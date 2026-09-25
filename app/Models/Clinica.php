<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Clinica extends Model
{
    protected $fillable = [
        'nombre',
        'telefono',
        'correo',
        'direccion',
        'nit',
        'asistente_secretaria',
        'departamento',
        'municipio',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function odontologos(): HasMany
    {
        return $this->hasMany(Odontologo::class);
    }
}