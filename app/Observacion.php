<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Observacion extends Model
{
    protected $table = 'entrada_observaciones';

    protected $fillable = [
        'contenido',
        'user_id',
        'entrada_id',
    ];
}
