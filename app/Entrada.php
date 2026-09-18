<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    protected $table = 'entradas';

    protected $fillable = [
        'numero',
        'alias_cliente_numero',
        'cliente_id',
        'consolidado_id',
        'vuelta',
        'recibido_at',
        'conductor_id',
        'vehiculo_id',
        'cruce_at',
        'reempacador_id',
        'codigor_id',
        'reempacado_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'alias_cliente_numero' => 'boolean',
        'recibido_at' => 'datetime',
        'cruce_at' => 'datetime',
        'reempacado_at' => 'datetime',
    ];
}
