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
}
