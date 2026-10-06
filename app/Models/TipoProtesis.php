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
        'area_trabajo',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function permiteArea(string $area): bool
    {
        return $this->area_trabajo === $area;
    }

    public function ordenesTrabajo(): HasMany
    {
        return $this->hasMany(OrdenTrabajo::class);
    }
}
