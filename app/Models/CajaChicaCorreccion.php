<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CajaChicaCorreccion extends Model
{
    protected $table = 'caja_chica_correcciones';
    protected $fillable = [
        'caja_chica_gasto_id', 'clave_registro', 'version_gasto', 'tipo', 'datos_anteriores',
        'datos_propuestos', 'datos_resultantes', 'motivo_solicitud', 'estado', 'solicitado_por', 'resuelto_por',
        'fecha_resolucion', 'motivo_resolucion',
    ];
    protected $casts = [
        'datos_anteriores' => 'array', 'datos_propuestos' => 'array', 'datos_resultantes' => 'array',
        'fecha_resolucion' => 'datetime', 'version_gasto' => 'integer',
    ];

    public function gasto(): BelongsTo
    {
        return $this->belongsTo(CajaChicaGasto::class, 'caja_chica_gasto_id');
    }

    public function usuarioSolicitud(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solicitado_por');
    }

    public function usuarioResolucion(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resuelto_por');
    }
}
