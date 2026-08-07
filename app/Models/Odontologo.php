<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
