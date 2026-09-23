<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    protected $table = 'entradas';

    protected $fillable = [
        'numero',
        'alias',
        'observaciones',
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
        'bodega_id',
        'remitente_id',
        'destinatario_id',
        'transportadora_id',
        'oficina_id',
        'modalidad_entrega',
        'destinatario_confirmado',
        'destinatario_confirmado_at',
        'destinatario_confirmado_por',
        'recibido_usa_por',
        'recibido_usa_at',
        'recibido_mexico_por',
        'recibido_mexico_at',
        'reempacado_por',
        'peso_usa',
        'largo_usa',
        'ancho_usa',
        'alto_usa',
        'volumen_usa',
        'peso_mexico',
        'largo_mexico',
        'ancho_mexico',
        'alto_mexico',
        'volumen_mexico',
        'control_mexico_tipo',
        'control_mexico_completado_at',
        'control_mexico_completado_por',
        'control_usa_tipo',
        'control_usa_completado_at',
        'control_usa_completado_por',
        'peso_cliente',
        'largo_cliente',
        'ancho_cliente',
        'alto_cliente',
        'volumen_cliente',
        'codigo_rastreo',
        'codigo_confirmacion',
        'status_salida',
        'incidente_salida',
        'notas_salida',
    ];

    protected $casts = [
        'alias_cliente_numero' => 'boolean',
        'recibido_at' => 'datetime',
        'cruce_at' => 'datetime',
        'reempacado_at' => 'datetime',
        'destinatario_confirmado' => 'boolean',
        'destinatario_confirmado_at' => 'datetime',
        'recibido_usa_at' => 'datetime',
        'recibido_mexico_at' => 'datetime',
        'peso_usa' => 'decimal:3',
        'largo_usa' => 'decimal:2',
        'ancho_usa' => 'decimal:2',
        'alto_usa' => 'decimal:2',
        'volumen_usa' => 'decimal:2',
        'peso_mexico' => 'decimal:3',
        'largo_mexico' => 'decimal:2',
        'ancho_mexico' => 'decimal:2',
        'alto_mexico' => 'decimal:2',
        'volumen_mexico' => 'decimal:2',
        'control_mexico_completado_at' => 'datetime',
        'control_usa_completado_at' => 'datetime',
        'peso_cliente' => 'decimal:3',
        'largo_cliente' => 'decimal:2',
        'ancho_cliente' => 'decimal:2',
        'alto_cliente' => 'decimal:2',
        'volumen_cliente' => 'decimal:2',
    ];

    public function formatMeasurement($value)
    {
        if ($value === null || $value === '') {
            return '';
        }

        $formatted = rtrim(rtrim((string) $value, '0'), '.');

        return $formatted === '' || $formatted === '-0' ? '0' : $formatted;
    }

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

    public function bodega()
    {
        return $this->belongsTo(Bodega::class, 'bodega_id');
    }

    public function destinatarioConfirmadoPor()
    {
        return $this->belongsTo(User::class, 'destinatario_confirmado_por');
    }

    public function recibidoUsaPor() { return $this->belongsTo(User::class, 'recibido_usa_por'); }
    public function recibidoMexicoPor() { return $this->belongsTo(User::class, 'recibido_mexico_por'); }
    public function reempacadoPor() { return $this->belongsTo(User::class, 'reempacado_por'); }
    public function controlUsaCompletadoPor() { return $this->belongsTo(User::class, 'control_usa_completado_por'); }

    public function controlMexicoCompletadoPor() { return $this->belongsTo(User::class, 'control_mexico_completado_por'); }

    public function movimientos()
    {
        return $this->hasMany(EntradaMovimiento::class)->orderBy('ocurrido_at', 'desc');
    }
}
