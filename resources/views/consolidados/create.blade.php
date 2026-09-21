@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Crear consolidado</h2>

    <form action="{{ route('consolidados.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Número</label>
                <input type="text" name="numero" class="form-control" value="{{ old('numero') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Palets</label>
                <input type="number" name="palets" class="form-control" value="{{ old('palets') }}" min="0">
            </div>
            <div class="col-md-6 mb-3">
                <label for="cliente_id">Cliente</label>
                <select id="cliente_id" name="cliente_id" class="form-control" required>
                    <option value="">Selecciona un cliente</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>{{ $cliente->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>Notificación</label>
                <input type="datetime-local" name="notificacion" class="form-control" value="{{ old('notificacion') }}">
            </div>
            <div class="col-md-12 mb-3">
                <label>Cliente alias número</label>
                <select name="cliente_alias_numero" class="form-control">
                    <option value="0" {{ old('cliente_alias_numero') == '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('cliente_alias_numero') == '1' ? 'selected' : '' }}>Sí</option>
                </select>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="{{ route('consolidados.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
