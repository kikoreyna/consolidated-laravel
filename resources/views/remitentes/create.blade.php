@extends('layouts.app')
@section('content')<div class="container"><h2>Nuevo remitente</h2><form action="{{ route('remitentes.store') }}" method="POST">@csrf @include('remitentes._form') @include('layouts.validation')<button class="btn btn-primary">Guardar</button> <a href="{{ route('remitentes.index') }}" class="btn btn-secondary">Cancelar</a></form></div>@endsection
