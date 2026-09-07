<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CuentaOdontologo extends Model
{
    protected $table = 'cuentas_odontologos';

    protected $fillable = [
        'odontologo_id',
        'modalidad_pago',
        'limite_credito',
        'saldo_pendiente',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'limite_credito' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function odontologo(): BelongsTo
    {
        return $this->belongsTo(Odontologo::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function getCreditoDisponibleAttribute(): float
    {
        if ($this->modalidad_pago !== 'Crédito') {
            return 0;
       }

       return max(
            0,                   
            (float) $this->limite_credito
            - (float) $this->saldo_pendiente
        );
    }

    public function getLimiteSuperadoAttribute(): bool
    {
        if ($this->modalidad_pago !== 'Crédito') {
              return false;
          }

        return (float) $this->saldo_pendiente
        > (float) $this->limite_credito;
    }

}
