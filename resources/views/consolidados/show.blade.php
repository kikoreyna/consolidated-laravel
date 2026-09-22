@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Detalle del consolidado</h2>
        @if(!$consolidado->cerrado || in_array(auth()->user()->rol, ['supervisor', 'administrador', 'superadministrador'], true))
            <a href="{{ route('consolidados.edit', $consolidado) }}" class="btn btn-warning">Editar consolidado</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <p><strong>Número:</strong> {{ $consolidado->numero }}</p>
            <p><strong>Palets:</strong> {{ $consolidado->palets }}</p>
            <p><strong>Cliente:</strong> {{ optional($consolidado->cliente)->nombre ?? 'Sin cliente' }}</p>
            <p><strong>Cliente alias número:</strong> {{ $consolidado->cliente_alias_numero ? 'Sí' : 'No' }}</p>
            <p><strong>Notificación:</strong> {{ $consolidado->notificacion ? $consolidado->notificacion->format('Y-m-d H:i') : 'N/A' }}</p>
            <p><strong>Estado:</strong> {{ $consolidado->cerrado ? 'Cerrado' : 'Abierto' }}</p>
            @if($consolidado->cerrado)
                <p><strong>Cerrado por:</strong> {{ optional($consolidado->cerradoPor)->name ?? 'N/A' }}</p>
                <p><strong>Fecha de cierre:</strong> {{ optional($consolidado->cerrado_at)->format('Y-m-d H:i') }}</p>
            @endif
        </div>
    </div>

    <div class="mt-4">
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#consolidadoHistorial" aria-expanded="false" aria-controls="consolidadoHistorial">
            Ver historial de cambios
        </button>
        <div class="collapse mt-2" id="consolidadoHistorial">
            <div class="card">
                <div class="card-header">Historial de cambios</div>
                <div class="card-body">
            <table class="table table-striped mb-0">
                <thead><tr><th>Movimiento</th><th>Usuario</th><th>Fecha y hora</th><th>Detalle</th></tr></thead>
                <tbody>
                @forelse($consolidado->movimientos as $movimiento)
                    <tr><td>{{ ucfirst(str_replace('_', ' ', $movimiento->tipo)) }}</td><td>{{ optional($movimiento->usuario)->name ?? 'N/A' }}</td><td>{{ $movimiento->ocurrido_at->format('Y-m-d H:i') }}</td><td>{{ $movimiento->observacion }}</td></tr>
                @empty
                    <tr><td colspan="4" class="text-center">No hay cambios registrados.</td></tr>
                @endforelse
                </tbody>
            </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            Entradas del consolidado
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Cliente</th>
                            <th>Vuelta</th>
                            <th>Recibido</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entradas as $entrada)
                            <tr>
                                <td>{{ $entrada->numero }}</td>
                                <td>{{ optional($entrada->cliente)->nombre ?? 'Sin cliente' }}</td>
                                <td>{{ $entrada->vuelta ?? 'N/A' }}</td>
                                <td>{{ $entrada->recibido_at ? $entrada->recibido_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('entradas.show', $entrada) }}" class="btn btn-sm btn-info">Ver</a>
                                    <a href="{{ route('entradas.edit', $entrada) }}" class="btn btn-sm btn-warning">Editar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay entradas asociadas a este consolidado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(!$consolidado->cerrado && auth()->user()->rol !== 'cliente')
        <a href="{{ route('consolidados.entradas.create', $consolidado) }}" class="btn btn-primary mt-4">Agregar guía</a>
        <a href="{{ route('consolidados.importar', $consolidado) }}" class="btn btn-outline-primary mt-3">Agregar guías desde CSV</a>
    @endif

    <div class="mt-3">
        <a href="{{ route('consolidados.index') }}" class="btn btn-secondary">Volver</a>
        @if(!$consolidado->cerrado || in_array(auth()->user()->rol, ['supervisor', 'administrador', 'superadministrador'], true))
            <a href="{{ route('consolidados.edit', $consolidado) }}" class="btn btn-warning">Editar</a>
        @endif
    </div>
</div>
@endsection
