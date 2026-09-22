@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row align-items-start">
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center"><span>Entrada</span><a href="{{ route('entradas.edit', $entrada) }}" class="btn btn-warning btn-sm" title="Editar entrada">&#9998;</a></div>
                <div class="card-body p-2">
                    <table class="table table-sm table-bordered mb-0">
                        <tbody>
                            <tr><th colspan="2">Información</th></tr>
                            <tr><td>Número</td><td>{{ $entrada->numero }}</td></tr>
                            <tr><td>Cliente</td><td>{{ optional($entrada->cliente)->nombre ?? 'Sin cliente' }}</td></tr>
                            <tr><td>Consolidado</td><td>@if($entrada->consolidado)<a href="{{ route('consolidados.show', $entrada->consolidado) }}">{{ $entrada->consolidado->numero }}</a>@else Sin consolidar @endif</td></tr>
                            <tr><td>Creado por</td><td>{{ optional($entrada->createdBy)->name ?? 'N/A' }}</td></tr>
                            <tr><td>Fecha de creación</td><td>{{ optional($entrada->created_at)->format('Y-m-d H:i') }}</td></tr>
                            <tr><td>Actualizado por</td><td>{{ optional($entrada->updatedBy)->name ?? 'N/A' }}</td></tr>
                            <tr><td>Fecha de actualización</td><td>{{ optional($entrada->updated_at)->format('Y-m-d H:i') }}</td></tr>
                            <tr><th colspan="2">Proceso</th></tr>
                            <tr><td>Recibido</td><td>{{ $entrada->recibido_at ? $entrada->recibido_at->format('Y-m-d H:i') : '' }}</td></tr>
                            <tr><td>En bodega USA por</td><td>{{ optional($entrada->recibidoUsaPor)->name ?? '' }}</td></tr>
                            <tr><td>En bodega México por</td><td>{{ optional($entrada->recibidoMexicoPor)->name ?? '' }}</td></tr>
                            <tr><td>Conductor</td><td>{{ optional($entrada->conductor)->nombre ?? 'Sin conductor' }}</td></tr>
                            <tr><td>Vehículo</td><td>{{ optional($entrada->vehiculo)->alias ?? 'Sin vehículo' }}</td></tr>
                            <tr><td>Número de vuelta</td><td>{{ $entrada->vuelta ?? 'N/A' }}</td></tr>
                            <tr><td>Fecha de cruce</td><td>{{ $entrada->cruce_at ? $entrada->cruce_at->format('Y-m-d H:i') : '' }}</td></tr>
                            <tr><td>Reempacador</td><td>{{ optional($entrada->reempacador)->nombre ?? '' }}</td></tr>
                            <tr><td>Código de reempacado</td><td>{{ optional($entrada->codigor)->nombre ?? '' }}</td></tr>
                            <tr><td>Reempacado por</td><td>{{ optional($entrada->reempacadoPor)->name ?? '' }}</td></tr>
                            <tr><td>Fecha de reempacado</td><td>{{ $entrada->reempacado_at ? $entrada->reempacado_at->format('Y-m-d H:i') : '' }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header">Trayectoria</div>
                <div class="card-body p-2">
                    <table class="table table-sm table-bordered mb-0">
                        <tbody>
                            <tr><th colspan="2">Remitente</th></tr>
                            <tr><td>Nombre</td><td>{{ optional($entrada->remitente)->nombre ?? '' }}</td></tr>
                            <tr><td>Dirección</td><td>{{ optional($entrada->remitente)->direccion ?? '' }}</td></tr>
                            <tr><td>Postal</td><td>{{ optional($entrada->remitente)->codigo_postal ?? '' }}</td></tr>
                            <tr><td>Localidad</td><td>{{ collect([optional($entrada->remitente)->ciudad, optional($entrada->remitente)->estado, optional($entrada->remitente)->pais])->filter()->implode(', ') }}</td></tr>
                            <tr><td>Teléfono</td><td>{{ optional($entrada->remitente)->telefono ?? '' }}</td></tr>
                            <tr><th colspan="2">Destinatario</th></tr>
                            <tr><td>Nombre</td><td>{{ optional($entrada->destinatario)->nombre ?? '' }}</td></tr>
                            <tr><td>Dirección</td><td>{{ optional($entrada->destinatario)->direccion ?? '' }}</td></tr>
                            <tr><td>Postal</td><td>{{ optional($entrada->destinatario)->codigo_postal ?? '' }}</td></tr>
                            <tr><td>Referencias</td><td>{{ optional($entrada->destinatario)->referencias ?? '' }}</td></tr>
                            <tr><td>Localidad</td><td>{{ collect([optional($entrada->destinatario)->ciudad, optional($entrada->destinatario)->estado, optional($entrada->destinatario)->pais])->filter()->implode(', ') }}</td></tr>
                            <tr><td>Teléfono</td><td>{{ optional($entrada->destinatario)->telefono ?? '' }}</td></tr>
                            <tr><td>Verificación</td><td>{{ $entrada->destinatario_confirmado ? 'Confirmado' : '' }}</td></tr>
                            <tr><td>Fecha de verificado</td><td>{{ $entrada->destinatario_confirmado_at ? $entrada->destinatario_confirmado_at->format('Y-m-d H:i') : '' }}</td></tr>
                            <tr><th colspan="2">Medidas declaradas</th></tr>
                            <tr><td>Peso</td><td>{{ $entrada->peso_cliente !== null ? $entrada->peso_cliente : '' }}</td></tr>
                            <tr><td>Medidas</td><td>{{ $entrada->largo_cliente !== null ? $entrada->largo_cliente . ' x ' . $entrada->ancho_cliente . ' x ' . $entrada->alto_cliente : '' }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center"><span>Salida</span><a href="{{ route('entradas.salida.edit', $entrada) }}" class="btn btn-warning btn-sm" title="Editar datos de salida">&#9998;</a></div>
                <div class="card-body p-2">
                    @if(!$entrada->destinatario_confirmado)
                        <div class="d-flex flex-column justify-content-center text-center px-4" style="min-height: 270px;">
                            <p class="mb-2">Para crear la guía de salida, se requiere</p>
                            <strong>La verificación del destinatario</strong>
                            <a href="{{ route('entradas.edit', $entrada) }}#destinatario_confirmado" class="btn btn-outline-primary btn-sm align-self-center mt-4">Verificar destinatario</a>
                        </div>
                    @else
                        <table class="table table-sm table-bordered mb-0">
                            <tbody>
                                <tr><th colspan="2">Información</th></tr>
                                <tr><td>Rastreo</td><td>{{ $entrada->codigo_rastreo ?? '' }}</td></tr>
                                <tr><td>Confirmación</td><td>{{ $entrada->codigo_confirmacion ?? '' }}</td></tr>
                                <tr><td>Transportadora</td><td>{{ optional($entrada->transportadora)->nombre ?? '' }}</td></tr>
                                <tr><td>Cobertura</td><td>{{ $entrada->modalidad_entrega === 'ocurre' ? 'Ocurre' : ($entrada->modalidad_entrega === 'domicilio' ? 'Domicilio' : '') }}</td></tr>
                                <tr><td>Dirección de ocurre</td><td>{{ optional($entrada->oficina)->nombre ?? '' }}</td></tr>
                                <tr><td>Status</td><td>{{ $entrada->status_salida ?? '' }}</td></tr>
                                <tr><td>Incidente</td><td>{{ $entrada->incidente_salida ?? '' }}</td></tr>
                                <tr><td>Notas</td><td>{{ $entrada->notas_salida ?? '' }}</td></tr>
                                <tr><td>Actualizado por</td><td>{{ optional($entrada->updatedBy)->name ?? 'N/A' }}</td></tr>
                                <tr><td>Fecha de actualizado</td><td>{{ optional($entrada->updated_at)->format('Y-m-d H:i') }}</td></tr>
                                <tr><th colspan="2">Control USA</th></tr>
                                <tr><td>Tipo</td><td>{{ $entrada->control_usa_tipo ? ucfirst(str_replace('_', ' ', $entrada->control_usa_tipo)) : '' }}</td></tr>
                                <tr><td>Peso</td><td>{{ $entrada->peso_usa !== null ? $entrada->peso_usa . ' lb' : '' }}</td></tr>
                                <tr><td>Medidas</td><td>{{ $entrada->largo_usa !== null ? $entrada->largo_usa . ' x ' . $entrada->ancho_usa . ' x ' . $entrada->alto_usa . ' in' : '' }}</td></tr>
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
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
