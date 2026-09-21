@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Observaciones</h2>
        <a href="{{ route('observaciones.create') }}" class="btn btn-primary">Nueva observación</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Contenido</th>
                        <th>Usuario</th>
                        <th>Entrada</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($observaciones as $observacion)
                        <tr>
                            <td>{{ Str::limit($observacion->contenido, 80) }}</td>
                            <td>{{ optional($observacion->user)->name ?? 'Usuario no disponible' }}</td>
                            <td>{{ optional($observacion->entrada)->numero ?? 'Entrada no disponible' }}</td>
                            <td>
                                <a href="{{ route('observaciones.show', $observacion) }}" class="btn btn-sm btn-info">Ver</a>
                                <a href="{{ route('observaciones.edit', $observacion) }}" class="btn btn-sm btn-warning">Editar</a>
                                <form action="{{ route('observaciones.destroy', $observacion) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar observación?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No hay observaciones registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
