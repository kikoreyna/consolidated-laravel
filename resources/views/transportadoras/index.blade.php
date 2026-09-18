@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Transportadoras</h2>
        <a href="{{ route('transportadoras.create') }}" class="btn btn-primary">Nueva transportadora</a>
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
                        <th>Web</th>
                        <th>Teléfono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transportadoras as $transportadora)
                        <tr>
                            <td>{{ $transportadora->nombre }}</td>
                            <td>{{ $transportadora->web }}</td>
                            <td>{{ $transportadora->telefono }}</td>
                            <td>
                                <a href="{{ route('transportadoras.show', $transportadora) }}" class="btn btn-sm btn-info">Ver</a>
                                <a href="{{ route('transportadoras.edit', $transportadora) }}" class="btn btn-sm btn-warning">Editar</a>
                                <form action="{{ route('transportadoras.destroy', $transportadora) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar transportadora?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No hay transportadoras registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
