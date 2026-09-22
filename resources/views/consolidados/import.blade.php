@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Agregar guías al consolidado {{ $consolidado->numero }}</h2>
    <div class="card">
        <div class="card-body">
            <p>El archivo debe contener una fila por guía. Todas las guías se agregarán al consolidado actual y heredarán su cliente.</p>
            <p class="mb-1">Columnas obligatorias:</p>
            <code>entrada_numero</code>
            <p class="mt-3 mb-1">Columnas opcionales:</p>
            <code>cliente_id,alias,observaciones,bodega_id,remitente_id,destinatario_id,transportadora_id,oficina_id,modalidad_entrega,vuelta</code>

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
