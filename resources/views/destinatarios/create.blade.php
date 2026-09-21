@extends('layouts.app')
@section('content')<div class="container"><h2>Nuevo destinatario</h2><form action="{{ route('destinatarios.store') }}" method="POST">@csrf @include('destinatarios._form') @include('layouts.validation')<button class="btn btn-primary">Guardar</button> <a href="{{ route('destinatarios.index') }}" class="btn btn-secondary">Cancelar</a></form></div>@endsection
