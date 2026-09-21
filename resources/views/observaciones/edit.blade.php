@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar observación</h2>

    <form action="{{ route('observaciones.update', $observacion) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-12 mb-3">
                <label>Contenido</label>
                <textarea name="contenido" class="form-control" rows="5" required>{{ old('contenido', $observacion->contenido) }}</textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label>Usuario</label>
                <select name="user_id" class="form-control" required>
                    <option value="">Selecciona un usuario</option>
                    @foreach($usuarios as $usuario)
                        <option value="{{ $usuario->id }}" {{ old('user_id', $observacion->user_id) == $usuario->id ? 'selected' : '' }}>{{ $usuario->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>Entrada</label>
                <select name="entrada_id" class="form-control" required>
                    <option value="">Selecciona una entrada</option>
                    @foreach($entradas as $entrada)
                        <option value="{{ $entrada->id }}" {{ old('entrada_id', $observacion->entrada_id) == $entrada->id ? 'selected' : '' }}>{{ $entrada->numero }}</option>
                    @endforeach
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

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('observaciones.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
