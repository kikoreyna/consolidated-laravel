@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Códigos</h2>
        <a href="{{ route('codigosr.create') }}" class="btn btn-primary">Nuevo código</a>
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
                    @forelse($codigosr as $codigor)
                        <tr>
                            <td>{{ $codigor->nombre }}</td>
                            <td>{{ $codigor->descripcion }}</td>
                            <td>
                                <a href="{{ route('codigosr.show', $codigor) }}" class="btn btn-sm btn-info">Ver</a>
                                <a href="{{ route('codigosr.edit', $codigor) }}" class="btn btn-sm btn-warning">Editar</a>
                                <form action="{{ route('codigosr.destroy', $codigor) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar código?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No hay códigos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
