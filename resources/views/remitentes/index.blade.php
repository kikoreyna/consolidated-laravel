@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Remitentes</h2>
        <a href="{{ route('remitentes.create') }}" class="btn btn-primary">Nuevo remitente</a>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card"><div class="card-body"><div class="table-responsive"><table class="table table-striped mb-0">
        <thead><tr><th>Nombre</th><th>Contacto</th><th>Teléfono</th><th>Estado</th><th>Acciones</th></tr></thead>
        <tbody>
        @forelse($remitentes as $remitente)
            <tr><td>{{ $remitente->nombre }}</td><td>{{ $remitente->contacto ?? 'N/A' }}</td><td>{{ $remitente->telefono ?? 'N/A' }}</td><td>{{ $remitente->activo ? 'Activo' : 'Inactivo' }}</td><td>
                <a href="{{ route('remitentes.show', $remitente) }}" class="btn btn-sm btn-info">Ver</a>
                <a href="{{ route('remitentes.edit', $remitente) }}" class="btn btn-sm btn-warning">Editar</a>
                @if($remitente->activo)<form action="{{ route('remitentes.destroy', $remitente) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" onclick="return confirm('¿Desactivar remitente?')">Desactivar</button></form>@endif
            </td></tr>
        @empty<tr><td colspan="5" class="text-center">No hay remitentes registrados.</td></tr>@endforelse
        </tbody>
    </table></div></div></div>
</div>
@endsection
