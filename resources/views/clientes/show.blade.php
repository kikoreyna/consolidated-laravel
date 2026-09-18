@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detalle del cliente</h2>

    <div class="card">
        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $cliente->nombre }}</p>
            <p><strong>Contacto:</strong> {{ $cliente->contacto }}</p>
            <p><strong>Alias:</strong> {{ $cliente->alias }}</p>
            <p><strong>Teléfono:</strong> {{ $cliente->telefono }}</p>
            <p><strong>Correo:</strong> {{ $cliente->correo_electronico }}</p>
            <p><strong>Dirección:</strong> {{ $cliente->direccion }}</p>
            <p><strong>Ciudad:</strong> {{ $cliente->ciudad }}</p>
            <p><strong>Estado:</strong> {{ $cliente->estado }}</p>
            <p><strong>País:</strong> {{ $cliente->pais }}</p>
            <p><strong>Notas:</strong> {{ $cliente->notas ?? 'N/A' }}</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Volver</a>
        <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-warning">Editar</a>
    </div>
</div>
@endsection
