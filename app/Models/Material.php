<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
        protected $fillable = [
            'nombre',
            'descripcion',
            'unidad_medida',
            'stock_actual',
            'stock_minimo',
            'costo_unitario',
            'estado',
        ];

        protected $casts = [
            'stock_actual' => 'decimal:2',
            'stock_minimo' => 'decimal:2',
            'costo_unitario' => 'decimal:2',
            'estado' => 'boolean',
        ];

        public function inventariosTecnicos(): HasMany
        {
            return $this->hasMany(InventarioTecnico::class);
        }

        public function movimientosInventario(): HasMany
        {
            return $this->hasMany(MovimientoInventario::class);
        }
        public function ordenesMaterial(): HasMany
        {
            return $this->hasMany(OrdenMaterial::class);
        }
}