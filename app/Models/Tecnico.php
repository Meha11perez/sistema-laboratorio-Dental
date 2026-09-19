<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tecnico extends Model
{
    protected $fillable = [
        'user_id',
        'especialidad',
        'telefono',
        'fecha_ingreso',
        'estado',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'estado' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function etapasProduccion(): BelongsToMany
    {
        return $this->belongsToMany(
            EtapaProduccion::class,
            'etapa_tecnico',
            'tecnico_id',
            'etapa_produccion_id'
        )->withTimestamps();
    }


    public function ordenesActuales(): HasMany
    {
        return $this->hasMany(
            OrdenTrabajo::class,
            'tecnico_actual_id'
        );
    }

    public function historialProduccion(): HasMany
    {
        return $this->hasMany(
            HistorialProduccion::class,
            'tecnico_id'
        );
    }
    public function inventarios(): HasMany
    {
        return $this->hasMany(InventarioTecnico::class, 'tecnico_id');
    }
    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'tecnico_id');
    }
    public function devolucionesResponsables(): HasMany
    {
        return $this->hasMany(
            Devolucion::class,
                'tecnico_responsable_id'
        );
    }

}
