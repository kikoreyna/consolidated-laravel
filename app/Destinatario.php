<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Destinatario extends Model
{
    protected $table = 'destinatarios';

    protected $fillable = [
        'nombre',
        'contacto',
        'telefono',
        'correo_electronico',
        'direccion',
        'codigo_postal',
        'referencias',
        'ciudad',
        'estado',
        'pais',
        'observaciones',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
