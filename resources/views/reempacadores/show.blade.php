@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detalle del reempacador</h2>

    <div class="card">
        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $reempacador->nombre }}</p>
            <p><strong>Clave:</strong> {{ $reempacador->clave }}</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('reempacadores.index') }}" class="btn btn-secondary">Volver</a>
        <a href="{{ route('reempacadores.edit', $reempacador) }}" class="btn btn-warning">Editar</a>
    </div>
</div>
@endsection
