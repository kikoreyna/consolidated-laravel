<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConsolidadoMovimiento extends Model
{
    protected $table = 'consolidado_movimientos';

    protected $fillable = [
        'consolidado_id',
        'tipo',
        'usuario_id',
        'ocurrido_at',
        'observacion',
        'datos',
    ];

    protected $casts = [
        'ocurrido_at' => 'datetime',
        'datos' => 'array',
    ];

    public function consolidado() { return $this->belongsTo(Consolidado::class); }
    public function usuario() { return $this->belongsTo(User::class, 'usuario_id'); }
}
