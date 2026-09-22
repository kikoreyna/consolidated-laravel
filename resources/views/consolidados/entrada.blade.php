@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Agregar guía al consolidado {{ $consolidado->numero }}</h2>
    <p class="text-muted">El cliente se asignará automáticamente desde el consolidado. Los datos de remitente y destinatario son opcionales.</p>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('consolidados.entradas.store', $consolidado) }}" method="POST">
        @csrf
        <div class="card mb-4">
            <div class="card-header">Datos de la guía</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="numero">Número de guía *</label>
                        <input id="numero" name="numero" class="form-control" value="{{ old('numero') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="peso_cliente">Peso</label>
                        <input id="peso_cliente" name="peso_cliente" type="number" min="0" step="0.001" class="form-control" value="{{ old('peso_cliente') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="vuelta">Vuelta</label>
                        <input id="vuelta" name="vuelta" type="number" min="0" class="form-control" value="{{ old('vuelta') }}">
                    </div>
                    <div class="col-md-3 mb-3"><label for="largo_cliente">Largo</label><input id="largo_cliente" name="largo_cliente" type="number" min="0" step="0.01" class="form-control" value="{{ old('largo_cliente') }}"></div>
                    <div class="col-md-3 mb-3"><label for="ancho_cliente">Ancho</label><input id="ancho_cliente" name="ancho_cliente" type="number" min="0" step="0.01" class="form-control" value="{{ old('ancho_cliente') }}"></div>
                    <div class="col-md-3 mb-3"><label for="alto_cliente">Altura</label><input id="alto_cliente" name="alto_cliente" type="number" min="0" step="0.01" class="form-control" value="{{ old('alto_cliente') }}"></div>
                    <div class="col-md-3 mb-3"><label for="volumen_cliente">Volumen</label><input id="volumen_cliente" name="volumen_cliente" type="number" min="0" step="0.01" class="form-control" value="{{ old('volumen_cliente') }}"></div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Remitente <small class="text-muted">(opcional)</small></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="remitente_id">Usar remitente del catálogo</label>
                        <select id="remitente_id" name="remitente_id" class="form-control">
                            <option value="">Capturar uno nuevo o dejar vacío</option>
                            @foreach($remitentes as $remitente)
                                <option value="{{ $remitente->id }}" {{ old('remitente_id') == $remitente->id ? 'selected' : '' }}>{{ $remitente->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3"><label for="remitente_nombre">Nombre</label><input id="remitente_nombre" name="remitente_nombre" class="form-control" value="{{ old('remitente_nombre') }}"></div>
                    <div class="col-md-6 mb-3"><label for="remitente_telefono">Teléfono</label><input id="remitente_telefono" name="remitente_telefono" class="form-control" value="{{ old('remitente_telefono') }}"></div>
                    <div class="col-md-6 mb-3"><label for="remitente_direccion">Dirección</label><input id="remitente_direccion" name="remitente_direccion" class="form-control" value="{{ old('remitente_direccion') }}"></div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Destinatario <small class="text-muted">(opcional)</small></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="destinatario_id">Usar destinatario del catálogo</label>
                        <select id="destinatario_id" name="destinatario_id" class="form-control">
                            <option value="">Capturar uno nuevo o dejar vacío</option>
                            @foreach($destinatarios as $destinatario)
                                <option value="{{ $destinatario->id }}" {{ old('destinatario_id') == $destinatario->id ? 'selected' : '' }}>{{ $destinatario->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3"><label for="destinatario_nombre">Nombre</label><input id="destinatario_nombre" name="destinatario_nombre" class="form-control" value="{{ old('destinatario_nombre') }}"></div>
                    <div class="col-md-6 mb-3"><label for="destinatario_telefono">Teléfono</label><input id="destinatario_telefono" name="destinatario_telefono" class="form-control" value="{{ old('destinatario_telefono') }}"></div>
                    <div class="col-md-6 mb-3"><label for="destinatario_direccion">Dirección</label><input id="destinatario_direccion" name="destinatario_direccion" class="form-control" value="{{ old('destinatario_direccion') }}"></div>
                    <div class="col-md-3 mb-3"><label for="destinatario_codigo_postal">Código postal</label><input id="destinatario_codigo_postal" name="destinatario_codigo_postal" class="form-control" value="{{ old('destinatario_codigo_postal') }}"></div>
                    <div class="col-md-3 mb-3"><label for="destinatario_referencias">Referencias</label><input id="destinatario_referencias" name="destinatario_referencias" class="form-control" value="{{ old('destinatario_referencias') }}"></div>
                    <div class="col-md-2 mb-3"><label for="destinatario_ciudad">Ciudad</label><input id="destinatario_ciudad" name="destinatario_ciudad" class="form-control" value="{{ old('destinatario_ciudad') }}"></div>
                    <div class="col-md-2 mb-3"><label for="destinatario_estado">Estado</label><input id="destinatario_estado" name="destinatario_estado" class="form-control" value="{{ old('destinatario_estado') }}"></div>
                    <div class="col-md-2 mb-3"><label for="destinatario_pais">País</label><input id="destinatario_pais" name="destinatario_pais" class="form-control" value="{{ old('destinatario_pais') }}"></div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Guardar guía</button>
        <a href="{{ route('consolidados.show', $consolidado) }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
