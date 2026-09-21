@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Entradas</h2>
        <a href="{{ route('entradas.create') }}" class="btn btn-primary">Nueva entrada</a>
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
                        <th>Consolidado</th>
                        <th>Vuelta</th>
                        <th>Recibido</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entradas as $entrada)
                        <tr>
                            <td>
                                <a href="{{ route('entradas.show', $entrada) }}">
                                    {{ $entrada->numero }}
                                </a>
                            </td>
                            <td>
                                @if($entrada->consolidado_id)
                                    <a href="{{ route('consolidados.show', $entrada->consolidado_id) }}">
                                        {{ $entrada->consolidado_id }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $entrada->vuelta ?? 'N/A' }}</td>
                            <td>{{ $entrada->recibido_at ? $entrada->recibido_at->format('Y-m-d H:i') : 'N/A' }}</td>
                            <td>
                                <a href="{{ route('entradas.show', $entrada) }}" class="btn btn-sm btn-info">Ver</a>
                                <a href="{{ route('entradas.edit', $entrada) }}" class="btn btn-sm btn-warning">Editar</a>
                                <form action="{{ route('entradas.destroy', $entrada) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar entrada?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No hay entradas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
