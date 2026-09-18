@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Bodegas</h2>
        <a href="{{ route('bodegas.create') }}" class="btn btn-primary">Nueva bodega</a>
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
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bodegas as $bodega)
                        <tr>
                            <td>{{ $bodega->nombre }}</td>
                            <td>{{ $bodega->descripcion }}</td>
                            <td>
                                <a href="{{ route('bodegas.show', $bodega) }}" class="btn btn-sm btn-info">Ver</a>
                                <a href="{{ route('bodegas.edit', $bodega) }}" class="btn btn-sm btn-warning">Editar</a>
                                <form action="{{ route('bodegas.destroy', $bodega) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar bodega?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No hay bodegas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
