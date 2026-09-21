<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Oficina extends Model
{
    protected $table = 'oficinas';

    protected $fillable = [
        'transportadora_id',
        'nombre',
        'contacto',
        'telefono',
        'observaciones',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    public function transportadora()
    {
        return $this->belongsTo(Transportadora::class, 'transportadora_id');
    }
}
