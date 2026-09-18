@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Crear transportadora</h2>

    <form action="{{ route('transportadoras.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Nombre</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Web</label>
                <input type="url" name="web" class="form-control" value="{{ old('web') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label>Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}">
            </div>
            <div class="col-md-12 mb-3">
                <label>Notas</label>
                <textarea name="notas" class="form-control" rows="4">{{ old('notas') }}</textarea>
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
        <a href="{{ route('transportadoras.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
