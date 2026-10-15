<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompraHistorial extends Model
{
    protected $table = 'compra_historiales';
    protected $guarded = ['id'];
    protected $casts = ['datos' => 'array'];
    public function compra() { return $this->belongsTo(Compra::class); }
    public function usuario() { return $this->belongsTo(User::class, 'usuario_id'); }
}
