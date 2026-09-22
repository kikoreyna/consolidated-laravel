<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bodega extends Model
{
    protected $table = 'bodegas';

    protected $fillable = [
        'nombre',
        'descripcion',
        'codigo',
        'pais',
        'activa',
        'control_usa',
    ];

    protected $casts = ['activa' => 'boolean'];

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'bodega_user');
    }

    public function entradas()
    {
        return $this->hasMany(Entrada::class);
    }
}
