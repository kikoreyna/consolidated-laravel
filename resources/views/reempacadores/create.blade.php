@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Crear reempacador</h2>

    <form action="{{ route('reempacadores.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Nombre</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Clave</label>
                <input type="text" name="clave" class="form-control" value="{{ old('clave') }}" required>
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
        <a href="{{ route('reempacadores.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
