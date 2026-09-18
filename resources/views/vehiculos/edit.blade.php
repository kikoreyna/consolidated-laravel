@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar vehículo</h2>

    <form action="{{ route('vehiculos.update', $vehiculo) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Alias</label>
                <input type="text" name="alias" class="form-control" value="{{ old('alias', $vehiculo->alias) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Descripción</label>
                <input type="text" name="descripcion" class="form-control" value="{{ old('descripcion', $vehiculo->descripcion) }}">
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
        <a href="{{ route('vehiculos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
