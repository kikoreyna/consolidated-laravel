@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Mediciones</h2>
        <a href="{{ route('mediciones.create') }}" class="btn btn-primary">Nueva medición</a>
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
                    @forelse($mediciones as $medicion)
                        <tr>
                            <td>{{ $medicion->nombre }}</td>
                            <td>
                                <a href="{{ route('mediciones.show', $medicion) }}" class="btn btn-sm btn-info">Ver</a>
                                <a href="{{ route('mediciones.edit', $medicion) }}" class="btn btn-sm btn-warning">Editar</a>
                                <form action="{{ route('mediciones.destroy', $medicion) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar medición?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">No hay mediciones registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
