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
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function entrada()
    {
        return $this->belongsTo(Entrada::class, 'entrada_id');
    }
}
