@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detalle de la bodega</h2>

    <div class="card">
        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $bodega->nombre }}</p>
            <p><strong>Descripción:</strong> {{ $bodega->descripcion }}</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('bodegas.index') }}" class="btn btn-secondary">Volver</a>
        <a href="{{ route('bodegas.edit', $bodega) }}" class="btn btn-warning">Editar</a>
    </div>
</div>
@endsection
