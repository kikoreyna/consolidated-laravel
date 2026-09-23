@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Detalle del consolidado</h2>
        <div class="d-flex gap-2">
            @if(!$consolidado->cerrado || in_array(auth()->user()->rol, ['supervisor', 'administrador', 'superadministrador'], true))
                <a href="{{ route('consolidados.edit', $consolidado) }}" class="btn btn-warning" title="Editar consolidado" aria-label="Editar consolidado">&#9998;</a>
            @endif
            <form action="{{ route('consolidados.destroy', $consolidado) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" title="Eliminar consolidado" aria-label="Eliminar consolidado" onclick="return confirm('¿Estás seguro de eliminar el consolidado {{ $consolidado->numero }}? Esta acción no se puede deshacer.')">&#128465;</button>
            </form>
        </div>
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
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Entradas <span class="badge bg-info">{{ $entradas->count() }}</span></span>
            <div>
                @if(!$consolidado->cerrado && auth()->user()->rol !== 'cliente')
                    <a href="{{ route('consolidados.entradas.create', $consolidado) }}" class="btn btn-primary btn-sm" title="Agregar guía" aria-label="Agregar guía">+</a>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Guía</th>
                            <th>Remitente</th>
                            <th>Destinatario</th>
                            <th>Salida</th>
                            <th>Status</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entradas as $entrada)
                            <tr>
                                <td>
                                    <a href="{{ route('entradas.show', $entrada) }}">{{ $entrada->numero }}</a>
                                    @if($entrada->consolidado)
                                        <small class="d-block text-muted">{{ $entrada->consolidado->numero }}</small>
                                    @endif
                                </td>
                                <td>
                                    {{ optional($entrada->remitente)->nombre ?? '' }}
                                    @if(optional($entrada->remitente)->telefono)
                                        <small class="d-block">{{ $entrada->remitente->telefono }}</small>
                                    @endif
                                </td>
                                <td>
                                    {{ optional($entrada->destinatario)->nombre ?? '' }}
                                    @if(optional($entrada->destinatario)->direccion)
                                        <small class="d-block">{{ $entrada->destinatario->direccion }}</small>
                                    @endif
                                    @if(optional($entrada->destinatario)->telefono)
                                        <small class="d-block">{{ $entrada->destinatario->telefono }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if(!$entrada->destinatario_confirmado)
                                        <span class="badge bg-secondary">VERIFICACION</span>
                                    @else
                                        {{ $entrada->modalidad_entrega === 'ocurre' ? 'Ocurre' : ($entrada->modalidad_entrega === 'domicilio' ? 'Domicilio' : '') }}
                                    @endif
                                </td>
                                <td>{{ $entrada->status_salida ?? '' }}</td>
                                <td>
                                    <a href="{{ route('entradas.show', $entrada) }}" class="btn btn-sm btn-info" title="Ver guía" aria-label="Ver guía">&#8634;</a>
                                    <a href="{{ route('entradas.edit', $entrada) }}" class="btn btn-sm btn-warning" title="Editar guía" aria-label="Editar guía">&#9998;</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay entradas asociadas a este consolidado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('consolidados.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</div>
@endsection
