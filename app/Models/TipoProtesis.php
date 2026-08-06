<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoProtesis extends Model
{
    protected $table = 'tipo_protesis';

    protected $fillable = [
        'nombre',
        'categoria',
        'descripcion',
        'estado',
    ];
    protected $casts = [
        'estado' => 'boolean',
    ];
}
