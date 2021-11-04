@extends('app')
@section('content')
  
@component('@.bootstrap.card', [
    'title' => 'Editar destinatario',    
])
    <form action="{{ route('destinatarios.update', $destinatario) }}" method="post" autocomplete="off">
        @method('patch')
        @include('destinatarios._save')
        @component('@.bootstrap.grid-left-right')
            @slot('left')
            <button type="submit" class="btn btn-warning">Actualizar destinatario</button>
            <a href="{{ route('destinatarios.index') }}" class="btn btn-secondary">Regresar</a>
            @endslot

            @slot('right')
            @include('@.partials.modal-confirm-delete.trigger', ['only' => 'text'])
            @endslot
        @endcomponent
    </form>
@endcomponent
<br>

@include('@.partials.block-modifiers.content', ['model' => $destinatario])

@component('@.partials.modal-confirm-delete.modal', [
    'route' => route('destinatarios.destroy', $destinatario),
])
    <p>Al eliminar destinatario <em>{{ $destinatario->nombre }}</em> <br> no estará disponible para trayectorias de entradas</p>
    <p>
        <small>(Se conservará entradas existentes con el destinatario)</small>
    </p>
@endcomponent

@endsection
