@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Reempacadores</h2>
        <a href="{{ route('reempacadores.create') }}" class="btn btn-primary">Nuevo reempacador</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Clave</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reempacadores as $reempacador)
                        <tr>
                            <td>{{ $reempacador->nombre }}</td>
                            <td>{{ $reempacador->clave }}</td>
                            <td>
                                <a href="{{ route('reempacadores.show', $reempacador) }}" class="btn btn-sm btn-info">Ver</a>
                                <a href="{{ route('reempacadores.edit', $reempacador) }}" class="btn btn-sm btn-warning">Editar</a>
                                <form action="{{ route('reempacadores.destroy', $reempacador) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar reempacador?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No hay reempacadores registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
