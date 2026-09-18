@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detalle de la medición</h2>

    <div class="card">
        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $medicion->nombre }}</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('mediciones.index') }}" class="btn btn-secondary">Volver</a>
        <a href="{{ route('mediciones.edit', $medicion) }}" class="btn btn-warning">Editar</a>
    </div>
</div>
@endsection
