<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenMaterial extends Model
{
    protected $table='orden_material';

    protected $fillable=[
        'orden_trabajo_id',
        'material_id',
        'cantidad',
        'costo_unitario',
        'subtotal'
    ];

    protected $casts=[
        'cantidad'=>'decimal:2',
        'costo_unitario'=>'decimal:2',
        'subtotal'=>'decimal:2'
    ];

    public function ordenTrabajo(): BelongsTo
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}