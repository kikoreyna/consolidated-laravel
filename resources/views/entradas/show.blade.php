@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detalle de la entrada</h2>

    <div class="card">
        <div class="card-body">
            <p><strong>Número:</strong> {{ $entrada->numero }}</p>
            <p><strong>Alias:</strong> {{ $entrada->alias ?? 'N/A' }}</p>
            <p><strong>Observaciones:</strong> {{ $entrada->observaciones ?? 'N/A' }}</p>
            <p><strong>Alias cliente número:</strong> {{ $entrada->alias_cliente_numero ? 'Sí' : 'No' }}</p>
            <p><strong>Cliente:</strong> {{ optional($entrada->cliente)->nombre ?? 'Cliente no disponible' }}</p>
            <p><strong>Remitente:</strong> {{ optional($entrada->remitente)->nombre ?? 'Sin remitente' }}</p>
            <p><strong>Destinatario:</strong> {{ optional($entrada->destinatario)->nombre ?? 'Sin destinatario' }}</p>
            <p><strong>Datos del destinatario:</strong> {{ $entrada->destinatario_confirmado ? 'Confirmados' : 'Pendientes de confirmación' }}</p>
            @if($entrada->destinatario_confirmado)
                <p><strong>Confirmado por:</strong> {{ optional($entrada->destinatarioConfirmadoPor)->name ?? 'Usuario no disponible' }}</p>
                <p><strong>Fecha de confirmación:</strong> {{ optional($entrada->destinatario_confirmado_at)->format('Y-m-d H:i') }}</p>
            @endif
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
            <p><strong>Recibido en USA:</strong> {{ $entrada->recibido_usa_at ? optional($entrada->recibidoUsaPor)->name . ' - ' . $entrada->recibido_usa_at->format('Y-m-d H:i') : 'Pendiente' }}</p>
            <p><strong>Recibido en México:</strong> {{ $entrada->recibido_mexico_at ? optional($entrada->recibidoMexicoPor)->name . ' - ' . $entrada->recibido_mexico_at->format('Y-m-d H:i') : 'Pendiente' }}</p>
            <p><strong>Control USA:</strong> {{ $entrada->control_usa_tipo ? ucfirst(str_replace('_', ' ', $entrada->control_usa_tipo)) : 'Pendiente' }}</p>
            <p><strong>Peso USA:</strong> {{ $entrada->peso_usa !== null ? $entrada->peso_usa . ' kg' : 'N/A' }}</p>
            <p><strong>Medidas USA:</strong> {{ $entrada->largo_usa !== null ? $entrada->largo_usa . ' x ' . $entrada->ancho_usa . ' x ' . $entrada->alto_usa . ' cm' : 'N/A' }}</p>
            <p><strong>Volumen USA:</strong> {{ $entrada->volumen_usa !== null ? $entrada->volumen_usa . ' cm³' : 'N/A' }}</p>
            <p><strong>Control USA completado por:</strong> {{ optional($entrada->controlUsaCompletadoPor)->name ?? 'Pendiente' }}</p>
            <p><strong>Conductor:</strong> {{ optional($entrada->conductor)->nombre ?? 'Sin conductor' }}</p>
            <p><strong>Vehículo:</strong> {{ optional($entrada->vehiculo)->alias ?? 'Sin vehículo' }}</p>
            <p><strong>Fecha cruce:</strong> {{ $entrada->cruce_at ? $entrada->cruce_at->format('Y-m-d H:i') : 'N/A' }}</p>
            <p><strong>Reempacador:</strong> {{ optional($entrada->reempacador)->nombre ?? 'Sin reempacador' }}</p>
            <p><strong>Código R:</strong> {{ optional($entrada->codigor)->nombre ?? 'Sin código' }}</p>
            <p><strong>Fecha reempacado:</strong> {{ $entrada->reempacado_at ? $entrada->reempacado_at->format('Y-m-d H:i') : 'N/A' }}</p>
            <p><strong>Reempacado por:</strong> {{ optional($entrada->reempacadoPor)->name ?? 'Pendiente' }}</p>
            <p><strong>Creado por:</strong> {{ optional($entrada->createdBy)->name ?? 'Usuario no disponible' }}</p>
            <p><strong>Actualizado por:</strong> {{ optional($entrada->updatedBy)->name ?? 'Usuario no disponible' }}</p>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">Historial de movimientos</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Movimiento</th><th>Usuario</th><th>Fecha y hora</th><th>Bodega</th><th>Detalle</th></tr></thead>
                    <tbody>
                    @forelse($entrada->movimientos as $movimiento)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $movimiento->tipo)) }}</td>
                            <td>{{ optional($movimiento->usuario)->name ?? 'N/A' }}</td>
                            <td>{{ $movimiento->ocurrido_at->format('Y-m-d H:i') }}</td>
                            <td>{{ optional($movimiento->bodega)->nombre ?? 'N/A' }}</td>
                            <td>{{ $movimiento->observacion ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">No hay movimientos registrados.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('entradas.index') }}" class="btn btn-secondary">Volver</a>
        <a href="{{ route('entradas.edit', $entrada) }}" class="btn btn-warning">Editar</a>
    </div>
</div>
@endsection
