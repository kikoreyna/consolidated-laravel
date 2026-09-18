@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar conductor</h2>

    <form action="{{ route('conductores.update', $conductor) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-12 mb-3">
                <label>Nombre</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $conductor->nombre) }}" required>
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
        <a href="{{ route('conductores.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
