<?php

$settings = (object) [
    'entradas' => $entradas,
    'has_entradas' => is_a($entradas, \Illuminate\Database\Eloquent\Collection::class) && $entradas->count(),
    'checkbox' => (object) [
        'form_id' => 'formEntradasAction',
        'render' => isset($checkbox) && is_bool($checkbox) ? $checkbox : true,
    ],
    'defaults' => (object) [
        'cliente'      => isset($cliente)      && is_a($cliente, \App\Cliente::class) ? $cliente : false,
        'consolidado'  => isset($consolidado)  && is_a($consolidado, \App\Consolidado::class) ? $consolidado : false,
        'destinatario' => isset($destinatario) && is_a($destinatario, \App\Destinatario::class) ? $destinatario : false,
    ],
    'thead' => [
        'checkbox'  => '',
        'entrada'   => 'Número <small class="d-block fw-normal">Consolidado</small>',
        'direccion' => 'Dirección <small class="d-block fw-normal">Localidad</small>',
        'cliente'   => 'Cliente <small class="d-block fw-normal">Alias</small>',
        'options'   => null,
    ],
];

/**
 * 
 * Si la propiedad render de checkbox no es válido
 * entonces ELIMINA el primer encabezado que es 'checkbox' => ''
 * en la propiedad thead de $settings
 * 
 */
if(! $settings->checkbox->render )
    array_shift($settings->thead);

?>

@if( $settings->has_entradas ) 
    @component('@.bootstrap.table', [
        'thead' => $settings->thead,
    ])
        @foreach($entradas as $entrada)
        <tr>
            <!-- Checkbox -->
            @if( $settings->checkbox->render )
            <?php $checkbox_id = "checkbox-entrada-{$entrada->id}" ?>
            <td class="align-top" style="width:1%">
                <input 
                    type="checkbox" 
                    class="form-check-input" 
                    form="{{ $settings->checkbox->form_id }}"
                    id="{{ $checkbox_id }}" 
                    name="entradas[]" 
                    value="{{ $entrada->id }}" 
                >
            </td>
            @endif

            <!-- Entrada & Consolidado -->
            <td>
                <label for="{{ $checkbox_id ?? '' }}">{{ $entrada->numero }}</label>
                <small class="d-block">
                    @if(! $entrada->hasConsolidado() )
                    <span class="small" style="color:#BBBBBB">SIN CONSOLIDAR</span>
                    
                    @else
                    <?php $route = route('consolidados.show', $settings->defaults->consolidado->id ?? $entrada->consolidado->id) ?>
                    <a href="{{ $route }}">{{ $settings->defaults->consolidado->numero ?? $entrada->consolidado->numero }}</a>
                    
                    @endif
                </small>
            </td>

            <!-- Destinatario & Localidad -->
            <td>
                @if( $entrada->hasDestinatario() )
                <span class="d-block">{{ $settings->defaults->destinatario->direccion ?? $entrada->destinatario->direccion ?? '-' }}</span>
                <small>{{ $settings->defaults->destinatario->localidad ?? $entrada->destinatario->localidad }}</small>

                @endif
            </td>

            <!-- Alias cliente -->
            <td>
                @if(! $entrada->hasCliente() )
                <span class="text-muted">-</span>

                @else
                <span class="d-block">{{ $settings->defaults->cliente->alias ?? $entrada->cliente->alias }}</span>

                @endif
            </td>

            <!-- Opciones -->
            <td class="text-end">
                <a href="{{ route('entradas.show', $entrada) }}" class="btn btn-sm btn-outline-primary">
                    {!! $graffiti->design('eye')->cache('svg') !!}
                </a>
            </td>
        </tr>
        @endforeach
    @endcomponent
@endif
