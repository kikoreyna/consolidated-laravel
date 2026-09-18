@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Crear entrada</h2>

    <form action="{{ route('entradas.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Número</label>
                <input type="text" name="numero" class="form-control" value="{{ old('numero') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Alias cliente número</label>
                <select name="alias_cliente_numero" class="form-control" required>
                    <option value="1" {{ old('alias_cliente_numero') == '1' ? 'selected' : '' }}>Sí</option>
                    <option value="0" {{ old('alias_cliente_numero') == '0' ? 'selected' : '' }}>No</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>Cliente ID</label>
                <input type="number" name="cliente_id" class="form-control" value="{{ old('cliente_id') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Consolidado ID</label>
                <input type="number" name="consolidado_id" class="form-control" value="{{ old('consolidado_id') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Vuelta</label>
                <input type="number" name="vuelta" class="form-control" value="{{ old('vuelta') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Recibido</label>
                <input type="datetime-local" name="recibido_at" class="form-control" value="{{ old('recibido_at') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Conductor ID</label>
                <input type="number" name="conductor_id" class="form-control" value="{{ old('conductor_id') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Vehículo ID</label>
                <input type="number" name="vehiculo_id" class="form-control" value="{{ old('vehiculo_id') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Fecha cruce</label>
                <input type="datetime-local" name="cruce_at" class="form-control" value="{{ old('cruce_at') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Reempacador ID</label>
                <input type="number" name="reempacador_id" class="form-control" value="{{ old('reempacador_id') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Codigor ID</label>
                <input type="number" name="codigor_id" class="form-control" value="{{ old('codigor_id') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Fecha reempacado</label>
                <input type="datetime-local" name="reempacado_at" class="form-control" value="{{ old('reempacado_at') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Created by</label>
                <input type="number" name="created_by" class="form-control" value="{{ old('created_by', 1) }}" required>
            </div>
            <div class="col-md-4 mb-3">
                <label>Updated by</label>
                <input type="number" name="updated_by" class="form-control" value="{{ old('updated_by', 1) }}" required>
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
        <a href="{{ route('entradas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
