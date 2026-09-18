@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detalle del código</h2>

    <div class="card">
        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $codigor->nombre }}</p>
            <p><strong>Descripción:</strong> {{ $codigor->descripcion ?? 'Sin descripción' }}</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('codigosr.index') }}" class="btn btn-secondary">Volver</a>
        <a href="{{ route('codigosr.edit', $codigor) }}" class="btn btn-warning">Editar</a>
    </div>
</div>
@endsection
