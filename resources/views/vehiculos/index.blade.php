@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Vehículos</h2>
        <a href="{{ route('vehiculos.create') }}" class="btn btn-primary">Nuevo vehículo</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Alias</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehiculos as $vehiculo)
                        <tr>
                            <td>{{ $vehiculo->alias }}</td>
                            <td>{{ $vehiculo->descripcion }}</td>
                            <td>
                                <a href="{{ route('vehiculos.show', $vehiculo) }}" class="btn btn-sm btn-info">Ver</a>
                                <a href="{{ route('vehiculos.edit', $vehiculo) }}" class="btn btn-sm btn-warning">Editar</a>
                                <form action="{{ route('vehiculos.destroy', $vehiculo) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar vehículo?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No hay vehículos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
