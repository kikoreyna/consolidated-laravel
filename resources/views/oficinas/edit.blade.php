@extends('layouts.app')
@section('content')<div class="container"><h2>Editar oficina</h2><form action="{{ route('oficinas.update', $oficina) }}" method="POST">@csrf @method('PUT') @include('oficinas._form') @include('layouts.validation')<button class="btn btn-primary">Actualizar</button> <a href="{{ route('oficinas.index') }}" class="btn btn-secondary">Cancelar</a></form></div>@endsection
