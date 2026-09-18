@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detalle del consolidado</h2>

    <div class="card">
        <div class="card-body">
            <p><strong>Número:</strong> {{ $consolidado->numero }}</p>
            <p><strong>Palets:</strong> {{ $consolidado->palets }}</p>
            <p><strong>Cliente ID:</strong> {{ $consolidado->cliente_id }}</p>
            <p><strong>Cliente alias número:</strong> {{ $consolidado->cliente_alias_numero ? 'Sí' : 'No' }}</p>
            <p><strong>Notificación:</strong> {{ $consolidado->notificacion ? $consolidado->notificacion->format('Y-m-d H:i') : 'N/A' }}</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('consolidados.index') }}" class="btn btn-secondary">Volver</a>
        <a href="{{ route('consolidados.edit', $consolidado) }}" class="btn btn-warning">Editar</a>
    </div>
</div>
@endsection
