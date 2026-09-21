<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    protected $table = 'entradas';

    protected $fillable = [
        'numero',
        'alias_cliente_numero',
        'cliente_id',
        'consolidado_id',
        'vuelta',
        'recibido_at',
        'conductor_id',
        'vehiculo_id',
        'cruce_at',
        'reempacador_id',
        'codigor_id',
        'reempacado_at',
        'created_by',
        'updated_by',
        'remitente_id',
        'destinatario_id',
        'transportadora_id',
        'oficina_id',
        'modalidad_entrega',
    ];

    protected $casts = [
        'alias_cliente_numero' => 'boolean',
        'recibido_at' => 'datetime',
        'cruce_at' => 'datetime',
        'reempacado_at' => 'datetime',
    ];

    public function consolidado()
    {
        return $this->belongsTo(Consolidado::class, 'consolidado_id');
    }
    
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
    
    public function conductor()
    {
        return $this->belongsTo(Conductor::class, 'conductor_id');
    }
    
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }
    
    public function reempacador()
    {
        return $this->belongsTo(Reempacador::class, 'reempacador_id');
    }
    
    public function codigor()
    {
        return $this->belongsTo(Codigor::class, 'codigor_id');
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function remitente()
    {
        return $this->belongsTo(Remitente::class, 'remitente_id');
    }

    public function destinatario()
    {
        return $this->belongsTo(Destinatario::class, 'destinatario_id');
    }

    public function transportadora()
    {
        return $this->belongsTo(Transportadora::class, 'transportadora_id');
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id');
    }
}
