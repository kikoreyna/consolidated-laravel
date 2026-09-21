@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Crear entrada</h2>

    <form action="{{ route('entradas.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Número</label>
                <input type="text" name="numero" class="form-control" value="{{ old('numero') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Alias cliente número</label>
                <select name="alias_cliente_numero" class="form-control" required>
                    <option value="1" {{ old('alias_cliente_numero') == '1' ? 'selected' : '' }}>Sí</option>
                    <option value="0" {{ old('alias_cliente_numero') == '0' ? 'selected' : '' }}>No</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="cliente_id">Cliente</label>
                <select id="cliente_id" name="cliente_id" class="form-control" required>
                    <option value="">Selecciona un cliente</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>{{ $cliente->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="consolidado_id">Consolidado</label>
                <select id="consolidado_id" name="consolidado_id" class="form-control">
                    <option value="">Sin consolidado</option>
                    @foreach($consolidados as $consolidado)
                        <option value="{{ $consolidado->id }}" {{ old('consolidado_id') == $consolidado->id ? 'selected' : '' }}>
                            {{ $consolidado->numero }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="remitente_id">Remitente</label>
                <select id="remitente_id" name="remitente_id" class="form-control">
                    <option value="">Sin remitente</option>
                    @foreach($remitentes as $remitente)
                        <option value="{{ $remitente->id }}" {{ old('remitente_id') == $remitente->id ? 'selected' : '' }}>{{ $remitente->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="destinatario_id">Destinatario</label>
                <select id="destinatario_id" name="destinatario_id" class="form-control">
                    <option value="">Sin destinatario</option>
                    @foreach($destinatarios as $destinatario)
                        <option value="{{ $destinatario->id }}" {{ old('destinatario_id') == $destinatario->id ? 'selected' : '' }}>{{ $destinatario->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="modalidad_entrega">Modalidad de entrega</label>
                <select id="modalidad_entrega" name="modalidad_entrega" class="form-control">
                    <option value="">Sin definir</option>
                    <option value="domicilio" {{ old('modalidad_entrega') == 'domicilio' ? 'selected' : '' }}>A domicilio</option>
                    <option value="ocurre" {{ old('modalidad_entrega') == 'ocurre' ? 'selected' : '' }}>A ocurre</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="transportadora_id">Transportadora</label>
                <select id="transportadora_id" name="transportadora_id" class="form-control">
                    <option value="">Sin transportadora</option>
                    @foreach($transportadoras as $transportadora)
                        <option value="{{ $transportadora->id }}" {{ old('transportadora_id') == $transportadora->id ? 'selected' : '' }}>{{ $transportadora->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="oficina_id">Oficina ocurre</label>
                <select id="oficina_id" name="oficina_id" class="form-control">
                    <option value="">Sin oficina</option>
                    @foreach($oficinas as $oficina)
                        <option value="{{ $oficina->id }}" {{ old('oficina_id') == $oficina->id ? 'selected' : '' }}>{{ $oficina->transportadora->nombre }} - {{ $oficina->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label>Vuelta</label>
                <input type="number" name="vuelta" class="form-control" value="{{ old('vuelta') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Recibido</label>
                <input type="datetime-local" name="recibido_at" class="form-control" value="{{ old('recibido_at') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label for="conductor_id">Conductor</label>
                <select id="conductor_id" name="conductor_id" class="form-control">
                    <option value="">Sin conductor</option>
                    @foreach($conductores as $conductor)
                        <option value="{{ $conductor->id }}" {{ old('conductor_id') == $conductor->id ? 'selected' : '' }}>{{ $conductor->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="vehiculo_id">Vehículo</label>
                <select id="vehiculo_id" name="vehiculo_id" class="form-control">
                    <option value="">Sin vehículo</option>
                    @foreach($vehiculos as $vehiculo)
                        <option value="{{ $vehiculo->id }}" {{ old('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>{{ $vehiculo->alias }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label>Fecha cruce</label>
                <input type="datetime-local" name="cruce_at" class="form-control" value="{{ old('cruce_at') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label for="reempacador_id">Reempacador</label>
                <select id="reempacador_id" name="reempacador_id" class="form-control">
                    <option value="">Sin reempacador</option>
                    @foreach($reempacadores as $reempacador)
                        <option value="{{ $reempacador->id }}" {{ old('reempacador_id') == $reempacador->id ? 'selected' : '' }}>{{ $reempacador->nombre }} ({{ $reempacador->clave }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="codigor_id">Código R</label>
                <select id="codigor_id" name="codigor_id" class="form-control">
                    <option value="">Sin código</option>
                    @foreach($codigosr as $codigor)
                        <option value="{{ $codigor->id }}" {{ old('codigor_id') == $codigor->id ? 'selected' : '' }}>{{ $codigor->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label>Fecha reempacado</label>
                <input type="datetime-local" name="reempacado_at" class="form-control" value="{{ old('reempacado_at') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label for="created_by">Creado por</label>
                <select id="created_by" name="created_by" class="form-control" required>
                    @foreach($usuarios as $usuario)
                        <option value="{{ $usuario->id }}" {{ old('created_by', auth()->id()) == $usuario->id ? 'selected' : '' }}>{{ $usuario->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="updated_by">Actualizado por</label>
                <select id="updated_by" name="updated_by" class="form-control" required>
                    @foreach($usuarios as $usuario)
                        <option value="{{ $usuario->id }}" {{ old('updated_by', auth()->id()) == $usuario->id ? 'selected' : '' }}>{{ $usuario->name }}</option>
                    @endforeach
                </select>
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

        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="{{ route('entradas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
