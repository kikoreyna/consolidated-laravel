@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detalle de la observación</h2>

    <div class="card">
        <div class="card-body">
            <p><strong>Contenido:</strong> {{ $observacion->contenido }}</p>
            <p><strong>Usuario:</strong> {{ $observacion->user_id }}</p>
            <p><strong>Entrada:</strong> {{ $observacion->entrada_id }}</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('observaciones.index') }}" class="btn btn-secondary">Volver</a>
        <a href="{{ route('observaciones.edit', $observacion) }}" class="btn btn-warning">Editar</a>
    </div>
</div>
@endsection
