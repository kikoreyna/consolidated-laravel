@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Conductores</h2>
        <a href="{{ route('conductores.create') }}" class="btn btn-primary">Nuevo conductor</a>
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
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($conductores as $conductor)
                        <tr>
                            <td>{{ $conductor->nombre }}</td>
                            <td>
                                <a href="{{ route('conductores.show', $conductor) }}" class="btn btn-sm btn-info">Ver</a>
                                <a href="{{ route('conductores.edit', $conductor) }}" class="btn btn-sm btn-warning">Editar</a>
                                <form action="{{ route('conductores.destroy', $conductor) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar conductor?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">No hay conductores registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
