@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar entrada</h2>

    <form action="{{ route('entradas.update', $entrada) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Número</label>
                <input type="text" name="numero" class="form-control" value="{{ old('numero', $entrada->numero) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Alias cliente número</label>
                <select name="alias_cliente_numero" class="form-control" required>
                    <option value="1" {{ old('alias_cliente_numero', $entrada->alias_cliente_numero) == '1' ? 'selected' : '' }}>Sí</option>
                    <option value="0" {{ old('alias_cliente_numero', $entrada->alias_cliente_numero) == '0' ? 'selected' : '' }}>No</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>Alias de la guía</label>
                <input type="text" name="alias" class="form-control" value="{{ old('alias', $entrada->alias) }}">
            </div>
            <div class="col-md-6 mb-3">
                <label for="cliente_id">Cliente</label>
                @if($entrada->consolidado_id)
                    <input type="hidden" name="cliente_id" value="{{ $entrada->cliente_id }}">
                    <input type="text" id="cliente_id" class="form-control" value="{{ optional($entrada->cliente)->nombre ?? 'Cliente no disponible' }}" readonly>
                    <small class="text-muted">El cliente está determinado por el consolidado {{ optional($entrada->consolidado)->numero }}.</small>
                @else
                    <select id="cliente_id" name="cliente_id" class="form-control" required>
                        <option value="">Selecciona un cliente</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ old('cliente_id', $entrada->cliente_id) == $cliente->id ? 'selected' : '' }}>{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            <div class="col-md-12 mb-3">
                <label>Observaciones</label>
                <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones', $entrada->observaciones) }}</textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label for="consolidado_id">Consolidado</label>
                <select id="consolidado_id" name="consolidado_id" class="form-control">
                    <option value="">Sin consolidado</option>
                    @foreach($consolidados as $consolidado)
                        <option value="{{ $consolidado->id }}" {{ old('consolidado_id', $entrada->consolidado_id) == $consolidado->id ? 'selected' : '' }}>
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
                        <option value="{{ $remitente->id }}" {{ old('remitente_id', $entrada->remitente_id) == $remitente->id ? 'selected' : '' }}>{{ $remitente->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="destinatario_id">Destinatario</label>
                <select id="destinatario_id" name="destinatario_id" class="form-control">
                    <option value="">Sin destinatario</option>
                    @foreach($destinatarios as $destinatario)
                        <option value="{{ $destinatario->id }}" {{ old('destinatario_id', $entrada->destinatario_id) == $destinatario->id ? 'selected' : '' }}>{{ $destinatario->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="bodega_id">Bodega actual / trasladar a</label>
                <select id="bodega_id" name="bodega_id" class="form-control">
                    <option value="">Sin bodega</option>
                    @foreach($bodegas as $bodega)
                        <option value="{{ $bodega->id }}" {{ old('bodega_id', $entrada->bodega_id) == $bodega->id ? 'selected' : '' }}>{{ $bodega->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-12 mb-3">
                <div class="form-check">
                    <input type="hidden" name="destinatario_confirmado" value="0">
                    <input type="checkbox" id="destinatario_confirmado" name="destinatario_confirmado" value="1" class="form-check-input" {{ old('destinatario_confirmado', $entrada->destinatario_confirmado) ? 'checked' : '' }}>
                    <label class="form-check-label" for="destinatario_confirmado">
                        Confirmo que validé los datos del destinatario con el destinatario.
                    </label>
                </div>
                @if($entrada->destinatario_confirmado)
                    <small class="text-muted">Confirmado por {{ optional($entrada->destinatarioConfirmadoPor)->name ?? 'usuario no disponible' }} el {{ optional($entrada->destinatario_confirmado_at)->format('Y-m-d H:i') }}.</small>
                @endif
            </div>
            <div class="col-md-4 mb-3">
                <label for="modalidad_entrega">Modalidad de entrega</label>
                <select id="modalidad_entrega" name="modalidad_entrega" class="form-control">
                    <option value="">Sin definir</option>
                    <option value="domicilio" {{ old('modalidad_entrega', $entrada->modalidad_entrega) == 'domicilio' ? 'selected' : '' }}>A domicilio</option>
                    <option value="ocurre" {{ old('modalidad_entrega', $entrada->modalidad_entrega) == 'ocurre' ? 'selected' : '' }}>A ocurre</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="transportadora_id">Transportadora</label>
                <select id="transportadora_id" name="transportadora_id" class="form-control">
                    <option value="">Sin transportadora</option>
                    @foreach($transportadoras as $transportadora)
                        <option value="{{ $transportadora->id }}" {{ old('transportadora_id', $entrada->transportadora_id) == $transportadora->id ? 'selected' : '' }}>{{ $transportadora->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="oficina_id">Oficina ocurre</label>
                <select id="oficina_id" name="oficina_id" class="form-control">
                    <option value="">Sin oficina</option>
                    @foreach($oficinas as $oficina)
                        <option value="{{ $oficina->id }}" {{ old('oficina_id', $entrada->oficina_id) == $oficina->id ? 'selected' : '' }}>{{ $oficina->transportadora->nombre }} - {{ $oficina->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label>Vuelta</label>
                <input type="number" name="vuelta" class="form-control" value="{{ old('vuelta', $entrada->vuelta) }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Recibido</label>
                <input type="datetime-local" name="recibido_at" class="form-control" value="{{ old('recibido_at', $entrada->recibido_at ? $entrada->recibido_at->format('Y-m-d\TH:i') : '') }}">
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-check mt-4">
                    <input type="checkbox" name="recibido_usa_confirmar" value="1" class="form-check-input" id="recibido_usa_confirmar">
                    <label class="form-check-label" for="recibido_usa_confirmar">Marcar recibida en bodega USA ahora</label>
                </div>
                <small class="text-muted">{{ $entrada->recibido_usa_at ? 'Recibida por ' . optional($entrada->recibidoUsaPor)->name . ' el ' . $entrada->recibido_usa_at->format('Y-m-d H:i') : 'Pendiente' }}</small>
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-check mt-4">
                    <input type="checkbox" name="recibido_mexico_confirmar" value="1" class="form-check-input" id="recibido_mexico_confirmar">
                    <label class="form-check-label" for="recibido_mexico_confirmar">Marcar recibida en bodega México ahora</label>
                </div>
                <small class="text-muted">{{ $entrada->recibido_mexico_at ? 'Recibida por ' . optional($entrada->recibidoMexicoPor)->name . ' el ' . $entrada->recibido_mexico_at->format('Y-m-d H:i') : 'Pendiente' }}</small>
            </div>
            <div class="col-md-4 mb-3">
                <label for="conductor_id">Conductor</label>
                <select id="conductor_id" name="conductor_id" class="form-control">
                    <option value="">Sin conductor</option>
                    @foreach($conductores as $conductor)
                        <option value="{{ $conductor->id }}" {{ old('conductor_id', $entrada->conductor_id) == $conductor->id ? 'selected' : '' }}>{{ $conductor->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="vehiculo_id">Vehículo</label>
                <select id="vehiculo_id" name="vehiculo_id" class="form-control">
                    <option value="">Sin vehículo</option>
                    @foreach($vehiculos as $vehiculo)
                        <option value="{{ $vehiculo->id }}" {{ old('vehiculo_id', $entrada->vehiculo_id) == $vehiculo->id ? 'selected' : '' }}>{{ $vehiculo->alias }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label>Fecha cruce</label>
                <input type="datetime-local" name="cruce_at" class="form-control" value="{{ old('cruce_at', $entrada->cruce_at ? $entrada->cruce_at->format('Y-m-d\TH:i') : '') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label for="reempacador_id">Reempacador</label>
                <select id="reempacador_id" name="reempacador_id" class="form-control">
                    <option value="">Sin reempacador</option>
                    @foreach($reempacadores as $reempacador)
                        <option value="{{ $reempacador->id }}" {{ old('reempacador_id', $entrada->reempacador_id) == $reempacador->id ? 'selected' : '' }}>{{ $reempacador->nombre }} ({{ $reempacador->clave }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="codigor_id">Código R</label>
                <select id="codigor_id" name="codigor_id" class="form-control">
                    <option value="">Sin código</option>
                    @foreach($codigosr as $codigor)
                        <option value="{{ $codigor->id }}" {{ old('codigor_id', $entrada->codigor_id) == $codigor->id ? 'selected' : '' }}>{{ $codigor->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label>Fecha reempacado</label>
                <input type="datetime-local" name="reempacado_at" class="form-control" value="{{ old('reempacado_at', $entrada->reempacado_at ? $entrada->reempacado_at->format('Y-m-d\TH:i') : '') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label for="created_by">Creado por</label>
                <select id="created_by" name="created_by" class="form-control" required>
                    @foreach($usuarios as $usuario)
                        <option value="{{ $usuario->id }}" {{ old('created_by', $entrada->created_by) == $usuario->id ? 'selected' : '' }}>{{ $usuario->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="updated_by">Actualizado por</label>
                <select id="updated_by" name="updated_by" class="form-control" required>
                    @foreach($usuarios as $usuario)
                        <option value="{{ $usuario->id }}" {{ old('updated_by', $entrada->updated_by) == $usuario->id ? 'selected' : '' }}>{{ $usuario->name }}</option>
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

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('entradas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
