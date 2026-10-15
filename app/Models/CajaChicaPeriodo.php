<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CajaChicaPeriodo extends Model
{
    protected $table = 'caja_chica_periodos';
    protected $fillable = ['mes', 'limite_centavos'];
    protected $casts = ['mes' => 'date', 'limite_centavos' => 'integer'];

    public function gastos(): HasMany
    {
        return $this->hasMany(CajaChicaGasto::class);
    }
}
