@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detalle del consolidado</h2>

    <div class="card">
        <div class="card-body">
            <p><strong>Número:</strong> {{ $consolidado->numero }}</p>
            <p><strong>Palets:</strong> {{ $consolidado->palets }}</p>
            <p><strong>Cliente:</strong> {{ optional($consolidado->cliente)->nombre ?? 'Sin cliente' }}</p>
            <p><strong>Cliente alias número:</strong> {{ $consolidado->cliente_alias_numero ? 'Sí' : 'No' }}</p>
            <p><strong>Notificación:</strong> {{ $consolidado->notificacion ? $consolidado->notificacion->format('Y-m-d H:i') : 'N/A' }}</p>
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

    <div class="mt-3">
        <a href="{{ route('consolidados.index') }}" class="btn btn-secondary">Volver</a>
        <a href="{{ route('consolidados.edit', $consolidado) }}" class="btn btn-warning">Editar</a>
    </div>
</div>
@endsection
