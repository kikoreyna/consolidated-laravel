<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Consolidado extends Model
{
    protected $table = 'consolidados';

    protected $fillable = [
        'numero',
        'palets',
        'cliente_id',
        'cliente_alias_numero',
        'notificacion',
        'cerrado',
        'cerrado_at',
        'cerrado_por',
    ];

    protected $casts = [
        'cliente_alias_numero' => 'boolean',
        'notificacion' => 'datetime',
        'cerrado' => 'boolean',
        'cerrado_at' => 'datetime',
    ];

    public function entradas()
    {
        return $this->hasMany(Entrada::class, 'consolidado_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function cerradoPor()
    {
        return $this->belongsTo(User::class, 'cerrado_por');
    }

    public function movimientos()
    {
        return $this->hasMany(ConsolidadoMovimiento::class)->orderBy('ocurrido_at', 'desc');
    }
}
