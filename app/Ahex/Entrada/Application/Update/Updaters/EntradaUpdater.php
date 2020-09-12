<?php

namespace App\Ahex\Entrada\Application\Update\Updaters;

use App\Consolidado;
use App\Ahex\Fake\Domain\Fakeuser;

Class EntradaUpdater extends Updater
{
    private $filled;

    public function validate()
    {
        $this->request->validate(
            [
                'consolidado_numero' => ['nullable','exists:consolidados,numero'],
                'cliente' => ['required_if:consolidado_numero,null','exists:clientes,id'],
                'numero' => ['required'],
                'cliente_alias_numero' => ['sometimes','accepted'],
                'recibido' => ['sometimes','accepted'],
            ],
            [
                'consolidado_numero.exists' => __('Escribe un número de consolidado válido'),
                'cliente.required_without' => __('Selecciona un cliente'),
                'cliente.exists' => __('Selecciona un cliente válido.'),
                'numero.required' => __('Escribe el número de entrada'),
                'cliente_alias_numero.accepted' => __('Activa o desactiva la opción del alias del cliente en el número de entrada'),
                'recibido.accepted' => __('Activa o desactiva la opción de recibido'),
            ]
        );
        
        return $this;
    }

    public function values()
    {
        if( ! $this->request->filled('consolidado_numero') )
            return $this->valuesWithoutConsolidado();

        $consolidado = $this->getIfConsolidadoAbierto( $this->request->consolidado_numero );
        return $this->valuesWithConsolidado( $consolidado );
    }

    private function valuesWithoutConsolidado()
    {
        return [
            'numero'               => $this->request->numero,
            'consolidado_id'       => null,
            'cliente_id'           => $this->request->cliente ?? $this->entrada->cliente_id,
            'cliente_alias_numero' => $this->request->input('cliente_alias_numero', 0),
            'recibido_at'          => $this->recibidoAt( $this->request->input('recibido', false) ),
            'recibido_by_user'     => $this->recibidoByUser( $this->request->input('recibido', false) ),
            'updated_by_user'      => Fakeuser::live(),
        ];
    }

    private function valuesWithConsolidado( $consolidado )
    {
        return [
            'numero'               => $this->request->numero,
            'consolidado_id'       => $consolidado->id,
            'cliente_id'           => $consolidado->cliente_id,
            'cliente_alias_numero' => $this->request->input('cliente_alias_numero', 0),
            'recibido_at'          => $this->recibidoAt( $this->request->input('recibido', false) ),
            'recibido_by_user'     => $this->recibidoByUser( $this->request->input('recibido', false) ),
            'updated_by_user'      => Fakeuser::live(),
        ];
    }

    private function recibidoAt( $has_request_recibido )
    {
        if( ! $has_request_recibido )
            return null;

        if( ! $this->entrada->recibido_at )
            return now();

        return $this->entrada->recibido_at;
    }

    private function recibidoByUser( $has_request_recibido )
    {
        if( ! $has_request_recibido )
            return null;

        if( ! $this->entrada->recibido_by_user )
            return Fakeuser::live();

        return $this->entrada->recibido_by_user;
    }


    public function redirect($saved)
    {
        if( ! $saved )
            return back()->with('failure', 'Error al actualizar entrada');

        return back()->with('success', 'Entrada actualizada');
    }

    private function getIfConsolidadoAbierto( $numero )
    {
        if( is_object( $this->entrada->consolidado ) )
        {
            if( $this->entrada->consolidado->numero == $numero )
                return $this->entrada->consolidado;
        }

        if( ! $consolidado = Consolidado::where('numero', $numero)->where('cerrado', 0)->first() )
            return back()->with('failure', 'Consolidado cerrado, no es posible agregar entradas.')->send();

        return $consolidado;
    }
}