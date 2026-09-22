@extends('layouts.app')
@section('content')<div class="container"><h2>Nueva cobertura</h2><form method="POST" action="{{ route('coberturas.store') }}">@csrf @include('coberturas._form') @include('layouts.validation')<button class="btn btn-primary">Guardar</button> <a href="{{ route('coberturas.index') }}" class="btn btn-secondary">Cancelar</a></form></div>@endsection
