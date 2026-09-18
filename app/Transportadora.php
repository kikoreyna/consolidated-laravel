<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transportadora extends Model
{
    protected $table = 'transportadoras';

    protected $fillable = [
        'nombre',
        'web',
        'telefono',
        'notas',
    ];
}
