<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompraCorreccion extends Model
{
    protected $table = 'compra_correcciones';
    protected $guarded = ['id'];
    protected $casts = ['anteriores' => 'array', 'propuestos' => 'array', 'aplicados' => 'array', 'fecha_resolucion' => 'datetime', 'version_compra' => 'integer'];
    public function compra() { return $this->belongsTo(Compra::class); }
    public function solicitante() { return $this->belongsTo(User::class, 'solicitado_por'); }
    public function resolutor() { return $this->belongsTo(User::class, 'resuelto_por'); }
}
