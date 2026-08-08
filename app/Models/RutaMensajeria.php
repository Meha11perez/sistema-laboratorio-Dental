<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RutaMensajeria extends Model
{
    protected $table = 'rutas_mensajeria';

    protected $fillable = [
        'mensajero_id',
        'fecha',
        'estado',
        'hora_salida',
        'hora_regreso',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function mensajero(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'mensajero_id'
        );
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleMensajeria::class);
    }
}