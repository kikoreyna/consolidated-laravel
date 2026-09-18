@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Crear observación</h2>

    <form action="{{ route('observaciones.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-12 mb-3">
                <label>Contenido</label>
                <textarea name="contenido" class="form-control" rows="5" required>{{ old('contenido') }}</textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label>Usuario</label>
                <input type="number" name="user_id" class="form-control" value="{{ old('user_id') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Entrada</label>
                <input type="number" name="entrada_id" class="form-control" value="{{ old('entrada_id') }}" required>
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
        <a href="{{ route('observaciones.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
