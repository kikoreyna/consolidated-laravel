@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detalle de la entrada</h2>

    <div class="card">
        <div class="card-body">
            <p><strong>Número:</strong> {{ $entrada->numero }}</p>
            <p><strong>Alias cliente número:</strong> {{ $entrada->alias_cliente_numero ? 'Sí' : 'No' }}</p>
            <p><strong>Cliente:</strong> {{ optional($entrada->cliente)->nombre ?? 'Cliente no disponible' }}</p>
            <p><strong>Remitente:</strong> {{ optional($entrada->remitente)->nombre ?? 'Sin remitente' }}</p>
            <p><strong>Destinatario:</strong> {{ optional($entrada->destinatario)->nombre ?? 'Sin destinatario' }}</p>
            <p><strong>Consolidado:</strong>
                @if($entrada->consolidado)
                    <a href="{{ route('consolidados.show', $entrada->consolidado) }}">
                        {{ $entrada->consolidado->numero }}
                    </a>
                @else
                    Sin consolidar
                @endif
            </p>
            <p><strong>Modalidad de entrega:</strong> {{ $entrada->modalidad_entrega === 'ocurre' ? 'A ocurre' : ($entrada->modalidad_entrega === 'domicilio' ? 'A domicilio' : 'Sin definir') }}</p>
            <p><strong>Transportadora:</strong> {{ optional($entrada->transportadora)->nombre ?? 'Sin transportadora' }}</p>
            <p><strong>Oficina:</strong> {{ optional($entrada->oficina)->nombre ?? 'Sin oficina' }}</p>
            <p><strong>Vuelta:</strong> {{ $entrada->vuelta ?? 'N/A' }}</p>
            <p><strong>Recibido:</strong> {{ $entrada->recibido_at ? $entrada->recibido_at->format('Y-m-d H:i') : 'N/A' }}</p>
            <p><strong>Conductor:</strong> {{ optional($entrada->conductor)->nombre ?? 'Sin conductor' }}</p>
            <p><strong>Vehículo:</strong> {{ optional($entrada->vehiculo)->alias ?? 'Sin vehículo' }}</p>
            <p><strong>Fecha cruce:</strong> {{ $entrada->cruce_at ? $entrada->cruce_at->format('Y-m-d H:i') : 'N/A' }}</p>
            <p><strong>Reempacador:</strong> {{ optional($entrada->reempacador)->nombre ?? 'Sin reempacador' }}</p>
            <p><strong>Código R:</strong> {{ optional($entrada->codigor)->nombre ?? 'Sin código' }}</p>
            <p><strong>Fecha reempacado:</strong> {{ $entrada->reempacado_at ? $entrada->reempacado_at->format('Y-m-d H:i') : 'N/A' }}</p>
            <p><strong>Creado por:</strong> {{ optional($entrada->createdBy)->name ?? 'Usuario no disponible' }}</p>
            <p><strong>Actualizado por:</strong> {{ optional($entrada->updatedBy)->name ?? 'Usuario no disponible' }}</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('entradas.index') }}" class="btn btn-secondary">Volver</a>
        <a href="{{ route('entradas.edit', $entrada) }}" class="btn btn-warning">Editar</a>
    </div>
</div>
@endsection
