@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detalle de la transportadora</h2>

    <div class="card">
        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $transportadora->nombre }}</p>
            <p><strong>Web:</strong> {{ $transportadora->web ?? 'Sin web' }}</p>
            <p><strong>Teléfono:</strong> {{ $transportadora->telefono ?? 'Sin teléfono' }}</p>
            <p><strong>Notas:</strong> {{ $transportadora->notas ?? 'Sin notas' }}</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('transportadoras.index') }}" class="btn btn-secondary">Volver</a>
        <a href="{{ route('transportadoras.edit', $transportadora) }}" class="btn btn-warning">Editar</a>
    </div>
</div>
@endsection
