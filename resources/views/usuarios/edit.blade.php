@extends('layouts.app')
@section('content')<div class="container"><h2>Editar usuario</h2><form action="{{ route('usuarios.update', $usuario) }}" method="POST">@csrf @method('PUT') @include('usuarios._form') @include('layouts.validation')<button class="btn btn-primary">Actualizar</button> <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a></form></div>@endsection
