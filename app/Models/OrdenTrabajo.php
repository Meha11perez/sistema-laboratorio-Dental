<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class OrdenTrabajo extends Model
{
     protected $table = 'ordenes_trabajo';

    protected $fillable = [
        'codigo',
        'codigo_caja',
        'odontologo_id',
        'paciente_id',
        'tipo_protesis_id',
        'estado_orden_id',
        'etapa_actual_id',
        'registrado_por',
        'tecnico_actual_id',
        'fecha_ingreso',
        'fecha_entrega_estimada',
        'fecha_entrega_real',
        'fecha_entrega_programada',
        'cantidad',
        'especificaciones',
        'observaciones',
        'color',
        'prioridad',
        'total',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'fecha_entrega_estimada' => 'date',
        'fecha_entrega_real' => 'date',
        'fecha_entrega_programada'=>'date',
        'cantidad' => 'integer',
        'total' => 'decimal:2',
    ];

    public function odontologo(): BelongsTo
    {
        return $this->belongsTo(Odontologo::class);
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function tipoProtesis(): BelongsTo
    {
        return $this->belongsTo(TipoProtesis::class);
    }

    public function estadoOrden(): BelongsTo
    {
        return $this->belongsTo(EstadoOrden::class);
    }

    public function etapaActual(): BelongsTo
    {
        return $this->belongsTo(
            EtapaProduccion::class,
            'etapa_actual_id'
        );
    }

    public function usuarioRegistro(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'registrado_por'
        );
    }

    public function tecnicoActual(): BelongsTo
    {
        return $this->belongsTo(
            Tecnico::class,
            'tecnico_actual_id'
        );
    }   

    public function historialProduccion(): HasMany
    {
        return $this->hasMany(HistorialProduccion::class);
    }
    
    public function movimientoInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }
    public function materiales(): HasMany
    {
        return $this->hasMany(OrdenMaterial::class);
    }
    public function garantia(): HasOne
    {
        return $this->hasOne(Garantia::class);
    }
    public function devoluciones(): HasMany
    {
        return $this->hasMany(Devolucion::class);
    }
    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }
    public function detallesMensajeria(): HasMany
    {
        return $this->hasMany(DetalleMensajeria::class);
    }
}
