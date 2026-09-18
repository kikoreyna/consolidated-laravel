@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detalle del vehículo</h2>

    <div class="card">
        <div class="card-body">
            <p><strong>Alias:</strong> {{ $vehiculo->alias }}</p>
            <p><strong>Descripción:</strong> {{ $vehiculo->descripcion ?? 'Sin descripción' }}</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('vehiculos.index') }}" class="btn btn-secondary">Volver</a>
        <a href="{{ route('vehiculos.edit', $vehiculo) }}" class="btn btn-warning">Editar</a>
    </div>
</div>
@endsection
