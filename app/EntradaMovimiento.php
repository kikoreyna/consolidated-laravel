<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EntradaMovimiento extends Model
{
    protected $table = 'entrada_movimientos';

    protected $fillable = [
        'entrada_id',
        'tipo',
        'usuario_id',
        'ocurrido_at',
        'bodega_id',
        'conductor_id',
        'vehiculo_id',
        'vuelta',
        'reempacador_id',
        'codigor_id',
        'observacion',
        'datos',
    ];

    protected $casts = [
        'ocurrido_at' => 'datetime',
        'datos' => 'array',
    ];

    public function entrada() { return $this->belongsTo(Entrada::class); }
    public function usuario() { return $this->belongsTo(User::class, 'usuario_id'); }
    public function bodega() { return $this->belongsTo(Bodega::class); }
    public function conductor() { return $this->belongsTo(Conductor::class); }
    public function vehiculo() { return $this->belongsTo(Vehiculo::class); }
    public function reempacador() { return $this->belongsTo(Reempacador::class); }
    public function codigor() { return $this->belongsTo(Codigor::class); }
}
