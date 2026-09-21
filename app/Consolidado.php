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
    ];

    protected $casts = [
        'cliente_alias_numero' => 'boolean',
        'notificacion' => 'datetime',
    ];

    public function entradas()
    {
        return $this->hasMany(Entrada::class, 'consolidado_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}
