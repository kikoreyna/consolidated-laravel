<div class="tab-pane fade show active" id="informacion" role="tabpanel" aria-labelledby="informacion-tab">
    <!-- Informacion -->
    @component('@.bootstrap.table')
        @slot('tbody')
        <tr>
            <td class="text-muted small">Consolidado</td>
            <td>
                @if( is_object($entrada->consolidado) )
                <a href="{{ route('consolidados.show', $entrada->consolidado) }}" class="link-primary">{{ $entrada->consolidado->numero }}</a>
                
                @else
                <span class="d-none">Sin consolidar</span>
                
                @endif
            </td>
        </tr>
        <tr>
            <td class="text-muted small">Cliente</td>
            <td>{{ $entrada->cliente->nombre }} ({{ $entrada->cliente->alias }})</td>
        </tr>
        <tr>
            <td class="text-muted small align-top">Contenido</td>
            <td>{{ $entrada->contenido ?? 'Desconocido' }}</td>
        </tr>
        <tr>
            <td class="text-muted small border-0">Actualizado</td>
            <td class="border-0">{{ $entrada->updated_at }}, {{ $entrada->updater->name }}</td>
        </tr>
        @endslot
    @endcomponent
    <br>
    
    <div class="text-end">
        <a href="{{ route('entradas.edit', [$entrada, 'editor' => 'informacion']) }}" class="btn btn-warning btn-sm">
            <span>Editar información</span>
        </a>
    </div>
</div>