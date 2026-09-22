<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cobertura extends Model
{
    protected $fillable = [
        'cliente_id', 'oficina_id', 'transportadora_id', 'bodega_id',
        'modalidad_entrega', 'ciudad', 'estado', 'activa',
        'vigencia_desde', 'vigencia_hasta', 'observaciones',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'vigencia_desde' => 'date',
        'vigencia_hasta' => 'date',
    ];

    public function cliente() { return $this->belongsTo(Cliente::class); }
    public function oficina() { return $this->belongsTo(Oficina::class); }
    public function transportadora() { return $this->belongsTo(Transportadora::class); }
    public function bodega() { return $this->belongsTo(Bodega::class); }
}
