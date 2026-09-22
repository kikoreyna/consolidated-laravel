@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Bodega USA</h2>
        <div>
            <a href="{{ route('entradas.index') }}" class="btn btn-outline-secondary">Ver entradas</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @include('entradas._control-usa')

    <div class="card">
        <div class="card-header">Guías asignadas a bodega USA</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Número</th><th>Cliente</th><th>Consolidado</th><th>Control USA</th><th>Acciones</th></tr></thead>
                    <tbody>
                    @forelse($entradas as $entrada)
                        <tr>
                            <td>{{ $entrada->numero }}</td>
                            <td>{{ optional($entrada->cliente)->nombre ?? 'N/A' }}</td>
                            <td>{{ optional($entrada->consolidado)->numero ?? 'Sin consolidar' }}</td>
                            <td>{{ $entrada->control_usa_tipo ? ucfirst(str_replace('_', ' ', $entrada->control_usa_tipo)) : 'Pendiente' }}</td>
                            <td><a href="{{ route('entradas.show', $entrada) }}" class="btn btn-sm btn-info">Ver</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">No hay guías asignadas.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
