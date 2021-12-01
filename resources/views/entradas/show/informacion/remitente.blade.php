<div class="tab-pane fade" id="content-remitente" role="tabpanel" aria-labelledby="remitente-tab">
@if( $entrada->hasRemitente() )
    @component('@.bootstrap.table')
        <tr>
            <td class="text-muted small" style="width:1%">Nombre</td>
            <td>{{ $entrada->remitente->nombre }}</td>
        </tr>
        <tr>
            <td class="text-muted small">Teléfono</td>
            <td>{{ $entrada->remitente->telefono }}</td>
        </tr>
        <tr>
            <td class="text-muted small">Dirección</td>
            <td>{{ $entrada->remitente->direccion }}</td>
        </tr>
        <tr>
            <td class="text-muted small">Postal</td>
            <td>{{ $entrada->remitente->postal }}</td>
        </tr>
        <tr>
            <td class="text-muted small">Localidad</td>
            <td>{{ $entrada->remitente->localidad }}</td>
        </tr>
        <tr>
            <td class="text-muted small border-0">Notas</td>
            <td class="border-0">{{ $entrada->remitente->notas }}</td>
        </tr>
    @endcomponent
    <br>
    <div class="text-end">
        @include('remitentes.modal-search.trigger', [
            'text' => 'Cambiar remitente',
            'classes' => 'btn btn-sm btn-primary',
        ])
        <a href="{{ route('remitentes.edit', ['remitente' => $entrada->remitente->id, 'entrada' => $entrada->id]) }}" class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="left" title="Editar remitente">
            <span>Editar remitente</span>
        </a>
    </div>

@else
    <br>
    <div class="text-center">
        @include('remitentes.modal-search.trigger', [
            'text' => 'Agregar remitente',
            'classes' => 'btn btn-primary',
        ])
    </div>

@endif

@component('remitentes.modal-search.modal', [
    'route' => route('entradas.edit', $entrada),    
])
    <input type="hidden" name="editor" value="remitente">
@endcomponent
</div>
