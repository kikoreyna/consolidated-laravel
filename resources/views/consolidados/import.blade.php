@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Agregar guías al consolidado {{ $consolidado->numero }}</h2>
    <div class="card">
        <div class="card-body">
            <p>El archivo debe contener una fila por guía. Todas las guías se agregarán al consolidado actual y heredarán su cliente.</p>
            <p class="mb-1">Plantilla aceptada:</p>
            <code>Numero de entrada, Peso, Medida de peso, Ancho, Altura, Profundidad, Medida de volumen, (R)Nombre, (R)Telefono, (R)Direccion, (R)Codigo Postal, (R)Ciudad, (R)Estado, (R)Pais, (D)Nombre, (D)Telefono, (D)Direccion, (D)Codigo Postal, (D)Referencias, (D)Ciudad, (D)Estado, (D)Pais</code>
            <p class="mt-3 mb-1">También se acepta el formato interno con columnas como:</p>
            <code>entrada_numero,peso_cliente,largo_cliente,ancho_cliente,alto_cliente,remitente_nombre,destinatario_nombre</code>

            <form action="{{ route('consolidados.importar.store', $consolidado) }}" method="POST" enctype="multipart/form-data" class="mt-4">
                @csrf
                <div class="mb-3">
                    <label for="archivo">Archivo CSV</label>
                    <input id="archivo" type="file" name="archivo" accept=".csv,text/csv" class="form-control" required>
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

                <button type="submit" class="btn btn-primary">Agregar guías</button>
                <a href="{{ route('consolidados.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
