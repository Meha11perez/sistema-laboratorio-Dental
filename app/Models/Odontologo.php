<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Odontologo extends Model
{
    protected $fillable = [
        'clinica_id',
        'nombre',
        'telefono',
        'correo',
        'numero_colegiado',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function pacientes(): HasMany
    {
        return $this->hasMany(Paciente::class);
    }

    public function ordenesTrabajo(): HasMany
    {
        return $this->hasMany(OrdenTrabajo::class);
    }

    public function cuenta(): HasOne
    {
        return $this->hasOne(CuentaOdontologo::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function clinica(): BelongsTo
    {
        return $this->belongsTo(Clinica::class);
    }
}