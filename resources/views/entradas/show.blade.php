@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detalle de la entrada</h2>

    <div class="card">
        <div class="card-body">
            <p><strong>Número:</strong> {{ $entrada->numero }}</p>
            <p><strong>Alias cliente número:</strong> {{ $entrada->alias_cliente_numero ? 'Sí' : 'No' }}</p>
            <p><strong>Cliente ID:</strong> {{ $entrada->cliente_id }}</p>
            <p><strong>Consolidado ID:</strong> {{ $entrada->consolidado_id ?? 'N/A' }}</p>
            <p><strong>Vuelta:</strong> {{ $entrada->vuelta ?? 'N/A' }}</p>
            <p><strong>Recibido:</strong> {{ $entrada->recibido_at ? $entrada->recibido_at->format('Y-m-d H:i') : 'N/A' }}</p>
            <p><strong>Conductor ID:</strong> {{ $entrada->conductor_id ?? 'N/A' }}</p>
            <p><strong>Vehículo ID:</strong> {{ $entrada->vehiculo_id ?? 'N/A' }}</p>
            <p><strong>Fecha cruce:</strong> {{ $entrada->cruce_at ? $entrada->cruce_at->format('Y-m-d H:i') : 'N/A' }}</p>
            <p><strong>Reempacador ID:</strong> {{ $entrada->reempacador_id ?? 'N/A' }}</p>
            <p><strong>Codigor ID:</strong> {{ $entrada->codigor_id ?? 'N/A' }}</p>
            <p><strong>Fecha reempacado:</strong> {{ $entrada->reempacado_at ? $entrada->reempacado_at->format('Y-m-d H:i') : 'N/A' }}</p>
            <p><strong>Created by:</strong> {{ $entrada->created_by }}</p>
            <p><strong>Updated by:</strong> {{ $entrada->updated_by }}</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('entradas.index') }}" class="btn btn-secondary">Volver</a>
        <a href="{{ route('entradas.edit', $entrada) }}" class="btn btn-warning">Editar</a>
    </div>
</div>
@endsection
