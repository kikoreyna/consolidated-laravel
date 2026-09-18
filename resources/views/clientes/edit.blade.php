@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar cliente</h2>

    <form action="{{ route('clientes.update', $cliente) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Nombre</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $cliente->nombre) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Contacto</label>
                <input type="text" name="contacto" class="form-control" value="{{ old('contacto', $cliente->contacto) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Alias</label>
                <input type="text" name="alias" class="form-control" value="{{ old('alias', $cliente->alias) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $cliente->telefono) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Correo electrónico</label>
                <input type="email" name="correo_electronico" class="form-control" value="{{ old('correo_electronico', $cliente->correo_electronico) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Dirección</label>
                <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $cliente->direccion) }}" required>
            </div>
            <div class="col-md-4 mb-3">
                <label>Ciudad</label>
                <input type="text" name="ciudad" class="form-control" value="{{ old('ciudad', $cliente->ciudad) }}" required>
            </div>
            <div class="col-md-4 mb-3">
                <label>Estado</label>
                <input type="text" name="estado" class="form-control" value="{{ old('estado', $cliente->estado) }}" required>
            </div>
            <div class="col-md-4 mb-3">
                <label>País</label>
                <input type="text" name="pais" class="form-control" value="{{ old('pais', $cliente->pais) }}" required>
            </div>
            <div class="col-12 mb-3">
                <label>Notas</label>
                <textarea name="notas" class="form-control" rows="3">{{ old('notas', $cliente->notas) }}</textarea>
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

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
