@extends('app')
@section('content')

@component('@.bootstrap.page-header', [
    'title' => 'Editar guia de impresión',    
])
@endcomponent

@component('@.bootstrap.card')
    @slot('body')
    <form action="{{ route('guias_impresion.update', $guia) }}" method="post" autocomplete="off">
        @method('put')
        @include('guias_impresion._save')
        <br>

        <button class="btn btn-warning" type="submit">Actualizar <span class="d-none d-md-inline-block">guía de impresión</span></button>
        <a href="{{ route('guias_impresion.index') }}" class="btn btn-secondary">Regresar</a>
    </form>
    @endslot
    @slot('footer')
        <div class="text-center text-muted small">Actualizado {{ $guia->updated_at }}</div>
    @endslot
@endcomponent
<br>

<div class="text-end">
    @include('@.partials.confirm-delete.bundle', [
        'route' => route('guias_impresion.destroy', $guia),
        'text' => 'Eliminar guía de impresión',
        'content' => "<p class='lead'>¿Deseas eliminar guía de impresión <b>{$guia->nombre}</b>?</p>",
    ])
</div>
<br>

@endsection