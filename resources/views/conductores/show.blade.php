@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detalle del conductor</h2>

    <div class="card">
        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $conductor->nombre }}</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('conductores.index') }}" class="btn btn-secondary">Volver</a>
        <a href="{{ route('conductores.edit', $conductor) }}" class="btn btn-warning">Editar</a>
    </div>
</div>
@endsection
