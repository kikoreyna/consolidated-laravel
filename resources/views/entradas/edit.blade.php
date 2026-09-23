@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar entrada</h2>

    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item"><button type="button" class="nav-link active" data-edit-tab="guia">Guía</button></li>
        <li class="nav-item"><button type="button" class="nav-link" data-edit-tab="medidas">Medidas</button></li>
        <li class="nav-item"><button type="button" class="nav-link" data-edit-tab="remitente">Remitente</button></li>
        <li class="nav-item"><button type="button" class="nav-link" data-edit-tab="destinatario">Destinatario</button></li>
        <li class="nav-item"><button type="button" class="nav-link" data-edit-tab="proceso">Proceso</button></li>
    </ul>

    <form action="{{ route('entradas.update', $entrada) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-3 edit-field guia">
                <label>Número</label>
                <input type="text" name="numero" class="form-control" value="{{ old('numero', $entrada->numero) }}" required>
            </div>
            <div class="col-md-6 mb-3 edit-field guia">
                <label>Alias cliente número</label>
                <select name="alias_cliente_numero" class="form-control" required>
                    <option value="1" {{ old('alias_cliente_numero', $entrada->alias_cliente_numero) == '1' ? 'selected' : '' }}>Sí</option>
                    <option value="0" {{ old('alias_cliente_numero', $entrada->alias_cliente_numero) == '0' ? 'selected' : '' }}>No</option>
                </select>
            </div>
            <div class="col-md-6 mb-3 edit-field guia">
                <label>Alias de la guía</label>
                <input type="text" name="alias" class="form-control" value="{{ old('alias', $entrada->alias) }}">
            </div>
            <div class="col-md-6 mb-3 edit-field guia">
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
            <div class="col-md-12 mb-3 edit-field guia">
                <label>Observaciones</label>
                <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones', $entrada->observaciones) }}</textarea>
            </div>
            <div class="col-md-6 mb-3 edit-field guia">
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
            <div class="col-md-4 mb-3 edit-field medidas">
                <label for="peso_cliente">Peso declarado</label>
                <input id="peso_cliente" name="peso_cliente" type="number" min="0" step="0.001" class="form-control" value="{{ old('peso_cliente', $entrada->peso_cliente) }}">
            </div>
            <div class="col-md-4 mb-3 edit-field medidas">
                <label for="largo_cliente">Largo declarado</label>
                <input id="largo_cliente" name="largo_cliente" type="number" min="0" step="0.01" class="form-control" value="{{ old('largo_cliente', $entrada->largo_cliente) }}">
            </div>
            <div class="col-md-4 mb-3 edit-field medidas">
                <label for="ancho_cliente">Ancho declarado</label>
                <input id="ancho_cliente" name="ancho_cliente" type="number" min="0" step="0.01" class="form-control" value="{{ old('ancho_cliente', $entrada->ancho_cliente) }}">
            </div>
            <div class="col-md-4 mb-3 edit-field medidas">
                <label for="alto_cliente">Altura declarada</label>
                <input id="alto_cliente" name="alto_cliente" type="number" min="0" step="0.01" class="form-control" value="{{ old('alto_cliente', $entrada->alto_cliente) }}">
            </div>
            <div class="col-md-4 mb-3 edit-field medidas">
                <label for="volumen_cliente">Volumen declarado</label>
                <input id="volumen_cliente" name="volumen_cliente" type="number" min="0" step="0.01" class="form-control" value="{{ old('volumen_cliente', $entrada->volumen_cliente) }}">
            </div>
            <div class="col-md-6 mb-3 edit-field remitente">
                <label for="remitente_id">Remitente</label>
                <select id="remitente_id" name="remitente_id" class="form-control">
                    <option value="">Sin remitente</option>
                    @foreach($remitentes as $remitente)
                        <option value="{{ $remitente->id }}" {{ old('remitente_id', $entrada->remitente_id) == $remitente->id ? 'selected' : '' }}>{{ $remitente->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3 edit-field destinatario">
                <label for="destinatario_id">Destinatario</label>
                <select id="destinatario_id" name="destinatario_id" class="form-control">
                    <option value="">Sin destinatario</option>
                    @foreach($destinatarios as $destinatario)
                        <option value="{{ $destinatario->id }}" {{ old('destinatario_id', $entrada->destinatario_id) == $destinatario->id ? 'selected' : '' }}>{{ $destinatario->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 edit-field proceso"><h5 class="border-bottom pb-2">Recepción</h5></div>
            <div class="col-md-6 mb-3 edit-field proceso">
                <label for="bodega_id">Bodega actual / trasladar a</label>
                <select id="bodega_id" name="bodega_id" class="form-control">
                    <option value="">Sin bodega</option>
                    @foreach($bodegas as $bodega)
                        <option value="{{ $bodega->id }}" {{ old('bodega_id', $entrada->bodega_id) == $bodega->id ? 'selected' : '' }}>{{ $bodega->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-12 mb-3 edit-field destinatario">
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
            <div class="col-md-4 mb-3 edit-field proceso">
                <label>Vuelta</label>
                <input type="number" name="vuelta" class="form-control" value="{{ old('vuelta', $entrada->vuelta) }}">
            </div>
            <div class="col-md-4 mb-3 edit-field proceso">
                <label>Recibido</label>
                <input type="datetime-local" name="recibido_at" class="form-control" value="{{ old('recibido_at', $entrada->recibido_at ? $entrada->recibido_at->format('Y-m-d\TH:i') : '') }}">
            </div>
            <div class="col-12 edit-field proceso"><h5 class="border-bottom pb-2">Bodega USA</h5></div>
            <div class="col-md-6 mb-3 edit-field proceso">
                <div class="form-check mt-4">
                    <input type="checkbox" name="recibido_usa_confirmar" value="1" class="form-check-input" id="recibido_usa_confirmar">
                    <label class="form-check-label" for="recibido_usa_confirmar">Marcar recibida en bodega USA ahora</label>
                </div>
                <small class="text-muted">{{ $entrada->recibido_usa_at ? 'Recibida por ' . optional($entrada->recibidoUsaPor)->name . ' el ' . $entrada->recibido_usa_at->format('Y-m-d H:i') : 'Pendiente' }}</small>
            </div>
            <div class="col-12 edit-field proceso"><h5 class="border-bottom pb-2">Bodega México</h5></div>
            <div class="col-md-6 mb-3 edit-field proceso">
                <div class="form-check mt-4">
                    <input type="checkbox" name="recibido_mexico_confirmar" value="1" class="form-check-input" id="recibido_mexico_confirmar">
                    <label class="form-check-label" for="recibido_mexico_confirmar">Marcar recibida en bodega México ahora</label>
                </div>
                <small class="text-muted">{{ $entrada->recibido_mexico_at ? 'Entrada registrada por ' . optional($entrada->recibidoMexicoPor)->name . ' el ' . $entrada->recibido_mexico_at->format('Y-m-d H:i') : '' }}</small>
            </div>
            <div class="col-12 edit-field proceso"><h5 class="border-bottom pb-2">Cruce</h5></div>
            <div class="col-md-4 mb-3 edit-field proceso">
                <label for="conductor_id">Conductor</label>
                <select id="conductor_id" name="conductor_id" class="form-control">
                    <option value="">Sin conductor</option>
                    @foreach($conductores as $conductor)
                        <option value="{{ $conductor->id }}" {{ old('conductor_id', $entrada->conductor_id) == $conductor->id ? 'selected' : '' }}>{{ $conductor->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3 edit-field proceso">
                <label for="vehiculo_id">Vehículo</label>
                <select id="vehiculo_id" name="vehiculo_id" class="form-control">
                    <option value="">Sin vehículo</option>
                    @foreach($vehiculos as $vehiculo)
                        <option value="{{ $vehiculo->id }}" {{ old('vehiculo_id', $entrada->vehiculo_id) == $vehiculo->id ? 'selected' : '' }}>{{ $vehiculo->alias }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3 edit-field proceso">
                <label>Fecha cruce</label>
                <input type="datetime-local" name="cruce_at" class="form-control" value="{{ old('cruce_at', $entrada->cruce_at ? $entrada->cruce_at->format('Y-m-d\TH:i') : '') }}">
            </div>
            <div class="col-12 edit-field proceso"><h5 class="border-bottom pb-2">Reempaque</h5></div>
            <div class="col-md-4 mb-3 edit-field proceso">
                <label for="reempacador_id">Reempacador</label>
                <select id="reempacador_id" name="reempacador_id" class="form-control">
                    <option value="">Sin reempacador</option>
                    @foreach($reempacadores as $reempacador)
                        <option value="{{ $reempacador->id }}" {{ old('reempacador_id', $entrada->reempacador_id) == $reempacador->id ? 'selected' : '' }}>{{ $reempacador->nombre }} ({{ $reempacador->clave }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3 edit-field proceso">
                <label for="codigor_id">Código R</label>
                <select id="codigor_id" name="codigor_id" class="form-control">
                    <option value="">Sin código</option>
                    @foreach($codigosr as $codigor)
                        <option value="{{ $codigor->id }}" {{ old('codigor_id', $entrada->codigor_id) == $codigor->id ? 'selected' : '' }}>{{ $codigor->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3 edit-field proceso">
                <label>Fecha reempacado</label>
                <input type="datetime-local" name="reempacado_at" class="form-control" value="{{ old('reempacado_at', $entrada->reempacado_at ? $entrada->reempacado_at->format('Y-m-d\TH:i') : '') }}">
            </div>
            <div class="col-md-4 mb-3 edit-field proceso">
                <label>Creado por</label>
                <input type="text" class="form-control" value="{{ optional($entrada->createdBy)->name ?? '' }}" readonly>
            </div>
            <div class="col-md-4 mb-3 edit-field proceso">
                <label>Actualizado por</label>
                <input type="text" class="form-control" value="{{ optional($entrada->updatedBy)->name ?? '' }}" readonly>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tabs = Array.prototype.slice.call(document.querySelectorAll('[data-edit-tab]'));
        var fields = Array.prototype.slice.call(document.querySelectorAll('.edit-field'));

        function showTab(tab) {
            tabs.forEach(function (button) {
                button.classList.toggle('active', button.dataset.editTab === tab);
            });
            fields.forEach(function (field) {
                field.classList.toggle('d-none', !field.classList.contains(tab));
            });
        }

        tabs.forEach(function (button) {
            button.addEventListener('click', function () {
                showTab(button.dataset.editTab);
            });
        });

        showTab('guia');
    });
</script>
@endsection
