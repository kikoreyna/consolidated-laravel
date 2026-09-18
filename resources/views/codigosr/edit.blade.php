@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar código</h2>

    <form action="{{ route('codigosr.update', $codigor) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Nombre</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $codigor->nombre) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Descripción</label>
                <input type="text" name="descripcion" class="form-control" value="{{ old('descripcion', $codigor->descripcion) }}">
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
        <a href="{{ route('codigosr.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
