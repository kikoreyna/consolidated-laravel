@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Consolidados</h2>
        <div>
            <a href="{{ route('consolidados.create') }}" class="btn btn-primary">Nuevo consolidado</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Palets</th>
                        <th>Cliente</th>
                        <th>Notificación</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($consolidados as $consolidado)
                        <tr>
                            <td>
                                <a href="{{ route('consolidados.show', $consolidado) }}">
                                    {{ $consolidado->numero }}
                                </a>
                            </td>
                            <td>{{ $consolidado->palets }}</td>
                            <td>{{ optional($consolidado->cliente)->nombre ?? 'Sin cliente' }}</td>
                            <td>{{ $consolidado->notificacion ? $consolidado->notificacion->format('Y-m-d H:i') : 'N/A' }}</td>
                            <td>{{ $consolidado->cerrado ? 'Cerrado' : 'Abierto' }}</td>
                            <td>
                                <a href="{{ route('consolidados.show', $consolidado) }}" class="btn btn-sm btn-info">Ver</a>
                                <a href="{{ route('consolidados.edit', $consolidado) }}" class="btn btn-sm btn-warning">Editar</a>
                                <form action="{{ route('consolidados.destroy', $consolidado) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar consolidado?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No hay consolidados registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
