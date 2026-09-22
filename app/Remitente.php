<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Remitente extends Model
{
    protected $table = 'remitentes';

    protected $fillable = [
        'nombre',
        'contacto',
        'telefono',
        'correo_electronico',
        'direccion',
        'codigo_postal',
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
