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

    public function oficinas()
    {
        return $this->hasMany(Oficina::class, 'transportadora_id');
    }
}
