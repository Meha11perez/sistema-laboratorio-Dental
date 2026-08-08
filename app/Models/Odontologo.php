<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Odontologo extends Model
{
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
}
