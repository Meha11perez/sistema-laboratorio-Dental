<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['fecha' => 'date', 'fecha_recepcion' => 'datetime', 'total_centavos' => 'integer', 'version' => 'integer'];

    public function detalles() { return $this->hasMany(CompraDetalle::class)->orderBy('id'); }
    public function correcciones() { return $this->hasMany(CompraCorreccion::class)->orderByDesc('id'); }
    public function historiales() { return $this->hasMany(CompraHistorial::class)->orderByDesc('id'); }
    public function registrador() { return $this->belongsTo(User::class, 'registrado_por'); }
    public function receptor() { return $this->belongsTo(User::class, 'recibido_por'); }

    public function instantanea(): array
    {
        $this->loadMissing('detalles');
        return array_merge($this->only(['proveedor', 'nit', 'serie', 'numero_factura', 'metodo_pago', 'referencia', 'factura_path', 'observaciones', 'total_centavos', 'estado', 'version']), [
            'fecha' => $this->fecha->toDateString(),
            'detalles' => $this->detalles->map(fn ($d) => $d->only(['tipo', 'material_id', 'material_solicitado', 'descripcion', 'unidad', 'cantidad_centesimas', 'precio_centavos', 'total_centavos']))->all(),
        ]);
    }
}
