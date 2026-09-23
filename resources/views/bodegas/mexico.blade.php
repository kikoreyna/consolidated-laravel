@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Bodega México</h2>
        <a href="{{ route('entradas.index') }}" class="btn btn-outline-secondary">Ver entradas</a>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
    @if(session('warning'))<div class="alert alert-warning">{{ session('warning') }}</div>@endif
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    @php($contextoMexico = session('mexico_contexto', []))
    <div class="card mb-4">
        <div class="card-header">Configuración de recepción</div>
        <div class="card-body">
            <form action="{{ route('bodega-mexico.configurar') }}" method="POST" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-4"><label for="contexto_conductor_id">Conductor que recibe</label><select id="contexto_conductor_id" name="conductor_id" class="form-control" required><option value="">Seleccionar</option>@foreach($conductores as $conductor)<option value="{{ $conductor->id }}" {{ ($contextoMexico['conductor_id'] ?? '') == $conductor->id ? 'selected' : '' }}>{{ $conductor->nombre }}</option>@endforeach</select></div>
                <div class="col-md-4"><label for="contexto_vehiculo_id">Vehículo</label><select id="contexto_vehiculo_id" name="vehiculo_id" class="form-control" required><option value="">Seleccionar</option>@foreach($vehiculos as $vehiculo)<option value="{{ $vehiculo->id }}" {{ ($contextoMexico['vehiculo_id'] ?? '') == $vehiculo->id ? 'selected' : '' }}>{{ $vehiculo->alias }}</option>@endforeach</select></div>
                <div class="col-md-2"><label for="contexto_vuelta">Vuelta</label><input id="contexto_vuelta" name="vuelta" type="number" min="0" class="form-control" value="{{ $contextoMexico['vuelta'] ?? '' }}"></div>
                <div class="col-md-2"><button type="submit" class="btn btn-primary">Guardar configuración</button></div>
            </form>
        </div>
    </div>

    @if($pendiente = session('mexico_pendiente'))
        <div class="card mb-4">
            <div class="card-header">Entrada de guía {{ $pendiente['numero'] }}</div>
            <div class="card-body">
                <form action="{{ route('entradas.control-mexico.complete') }}" method="POST">
                    @csrf
                    <input type="hidden" name="entrada_id" value="{{ $pendiente['entrada_id'] }}">
                    <input type="hidden" name="accion" value="{{ $pendiente['accion'] }}">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label for="peso_mexico">Peso (lb)</label><input id="peso_mexico" name="peso_mexico" type="number" step="0.001" min="0" class="form-control" required autofocus></div>
                        <div class="col-md-6 mb-3"><label for="incidente">Tipo de incidente</label><select id="incidente" name="incidente" class="form-control"><option value="">Sin incidente</option><option value="dano_embalaje">Daño en embalaje</option><option value="empaque_danado">Empaque dañado</option><option value="faltante">Faltante</option><option value="excedente">Excedente</option><option value="diferencia_peso">Diferencia de peso</option><option value="guia_ilegible">Guía ilegible</option><option value="otro">Otro</option></select></div>
                        @if($pendiente['accion'] === 'pesar_medir')
                            <div class="col-md-4 mb-3"><label for="largo_mexico">Largo (in)</label><input id="largo_mexico" name="largo_mexico" type="number" step="0.01" min="0" class="form-control" required></div>
                            <div class="col-md-4 mb-3"><label for="ancho_mexico">Ancho (in)</label><input id="ancho_mexico" name="ancho_mexico" type="number" step="0.01" min="0" class="form-control" required></div>
                            <div class="col-md-4 mb-3"><label for="alto_mexico">Alto (in)</label><input id="alto_mexico" name="alto_mexico" type="number" step="0.01" min="0" class="form-control" required></div>
                        @endif
                        <div class="col-12 mb-3"><label for="observacion">Observación</label><textarea id="observacion" name="observacion" class="form-control" rows="2">{{ $pendiente['observacion'] }}</textarea></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Registrar entrada México</button>
                    <a href="{{ route('bodega-mexico.cambiar-modo') }}" class="btn btn-outline-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    @else
        <div class="card mb-4">
            <div class="card-header">Escanear guía para recibir en México</div>
            <div class="card-body">
                <form action="{{ route('entradas.control-mexico') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-6"><label for="numero">Número de guía</label><input id="numero" name="numero" class="form-control" autofocus required></div>
                    <div class="col-md-4"><label for="accion">Operación</label><select id="accion" name="accion" class="form-control"><option value="solo_recibido">Solo recibir</option><option value="solo_pesar">Recibir y pesar</option><option value="pesar_medir">Recibir, pesar y medir</option></select></div>
                    <div class="col-md-12"><label for="observacion">Observación</label><input id="observacion" name="observacion" class="form-control"></div>
                    <div class="col-md-12"><button type="submit" class="btn btn-primary">Continuar</button></div>
                </form>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">Guías asignadas a Bodega México</div>
        <div class="card-body table-responsive">
            <table class="table table-striped mb-0"><thead><tr><th>Guía</th><th>Cliente</th><th>Consolidado</th><th>Entrada México</th><th>Conductor</th><th>Vehículo</th></tr></thead><tbody>
            @forelse($entradas as $entrada)
                <tr><td><a href="{{ route('entradas.show', $entrada) }}">{{ $entrada->numero }}</a></td><td>{{ optional($entrada->cliente)->nombre }}</td><td>{{ optional($entrada->consolidado)->numero }}</td><td>{{ $entrada->recibido_mexico_at ? $entrada->recibido_mexico_at->format('Y-m-d H:i') : '' }}</td><td>{{ optional($entrada->conductor)->nombre }}</td><td>{{ optional($entrada->vehiculo)->alias }}</td></tr>
            @empty
                <tr><td colspan="6" class="text-center">No hay guías asignadas.</td></tr>
            @endforelse
            </tbody></table>
        </div>
    </div>
</div>
@endsection
