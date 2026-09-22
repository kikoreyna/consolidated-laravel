<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'contacto',
        'alias',
        'telefono',
        'correo_electronico',
        'direccion',
        'ciudad',
        'estado',
        'pais',
        'notas',
    ];

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'cliente_user')->withPivot('activo');
    }
}
