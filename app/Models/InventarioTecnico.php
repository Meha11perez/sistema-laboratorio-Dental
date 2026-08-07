<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventarioTecnico extends Model
{
    protected $table = 'inventarios_tecnicos';

    protected $fillable = [
        'tecnico_id',
        'material_id',
        'cantidad',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
    ];

    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(Tecnico::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}