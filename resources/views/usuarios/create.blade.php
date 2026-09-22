@extends('layouts.app')
@section('content')<div class="container"><h2>Nuevo usuario</h2><form action="{{ route('usuarios.store') }}" method="POST">@csrf @include('usuarios._form') @include('layouts.validation')<button class="btn btn-primary">Guardar</button> <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a></form></div>@endsection
