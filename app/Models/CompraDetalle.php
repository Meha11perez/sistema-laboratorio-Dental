<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompraDetalle extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['cantidad_centesimas' => 'integer', 'precio_centavos' => 'integer', 'total_centavos' => 'integer'];
    public function compra() { return $this->belongsTo(Compra::class); }
    public function material() { return $this->belongsTo(Material::class); }
    public function movimiento() { return $this->belongsTo(MovimientoInventario::class, 'movimiento_entrada_id'); }
}
