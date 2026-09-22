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
                        <th>Remitente</th>
                        <th>Destinatario</th>
                        <th>Datos verificados</th>
                        <th>Entrega</th>
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
                                @if($entrada->consolidado)
                                    <a href="{{ route('consolidados.show', $entrada->consolidado) }}">
                                        {{ $entrada->consolidado->numero }}
                                    </a>
                                @else
                                    Sin consolidar
                                @endif
                            </td>
                            <td>{{ optional($entrada->remitente)->nombre ?? 'N/A' }}</td>
                            <td>{{ optional($entrada->destinatario)->nombre ?? 'N/A' }}</td>
                            <td>{{ $entrada->destinatario_confirmado ? 'Sí' : 'Pendientes' }}</td>
                            <td>{{ $entrada->modalidad_entrega === 'ocurre' ? 'A ocurre' : ($entrada->modalidad_entrega === 'domicilio' ? 'A domicilio' : 'Sin definir') }}</td>
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
                            <td colspan="9" class="text-center">No hay entradas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
