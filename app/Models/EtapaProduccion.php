<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EtapaProduccion extends Model
{
    protected $table = 'etapas_produccion';

    protected $fillable = [
        'nombre',
        'descripcion',
        'orden',
        'estado',
        'areas_trabajo',
        'es_final',
    ];

    protected $casts = [
        'orden' => 'integer',
        'estado' => 'boolean',
        'areas_trabajo' => 'array',
        'es_final' => 'boolean',
    ];

    public function permiteArea(string $area): bool
    {
        return in_array($area, $this->areas_trabajo ?? [], true);
    }

    public function historialesProduccion(): HasMany
    {
        return $this->hasMany(HistorialProduccion::class);
    }

    public function ordenesActuales(): HasMany
    {
        return $this->hasMany(OrdenTrabajo::class, 'etapa_actual_id');
    }

    public function tecnicos(): BelongsToMany
    {
        return $this->belongsToMany(
            Tecnico::class,
            'etapa_tecnico',
            'etapa_produccion_id',
            'tecnico_id'
        )->withTimestamps();
    }
}
