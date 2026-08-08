<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Abono extends Model
{
    protected $fillable = [
        'pago_id',
        'registrado_por',
        'monto',
        'metodo_pago',
        'fecha_abono',
        'referencia',
        'observaciones',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_abono' => 'datetime',
    ];

    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class);
    }

    public function usuarioRegistro(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'registrado_por'
        );
    }
}