@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar datos de salida</h2>
    <p class="text-muted">Guía: {{ $entrada->numero }}</p>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('entradas.salida.update', $entrada) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="codigo_rastreo">Código de rastreo</label>
                        <input id="codigo_rastreo" name="codigo_rastreo" class="form-control" value="{{ old('codigo_rastreo', $entrada->codigo_rastreo) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="codigo_confirmacion">Código de confirmación</label>
                        <input id="codigo_confirmacion" name="codigo_confirmacion" class="form-control" value="{{ old('codigo_confirmacion', $entrada->codigo_confirmacion) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="transportadora_id">Transportadora</label>
                        <select id="transportadora_id" name="transportadora_id" class="form-control">
                            <option value="">Seleccionar</option>
                            @foreach($transportadoras as $transportadora)
                                <option value="{{ $transportadora->id }}" {{ old('transportadora_id', $entrada->transportadora_id) == $transportadora->id ? 'selected' : '' }}>{{ $transportadora->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="modalidad_entrega">Cobertura</label>
                        <select id="modalidad_entrega" name="modalidad_entrega" class="form-control">
                            <option value="domicilio" {{ old('modalidad_entrega', $entrada->modalidad_entrega ?: 'domicilio') === 'domicilio' ? 'selected' : '' }}>Domicilio</option>
                            <option value="ocurre" {{ old('modalidad_entrega', $entrada->modalidad_entrega) === 'ocurre' ? 'selected' : '' }}>Ocurre</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="oficina_id">Oficina de ocurre</label>
                        <select id="oficina_id" name="oficina_id" class="form-control">
                            <option value="">Seleccionar</option>
                            @foreach($oficinas as $oficina)
                                <option value="{{ $oficina->id }}" data-transportadora="{{ $oficina->transportadora_id }}" {{ old('oficina_id', $entrada->oficina_id) == $oficina->id ? 'selected' : '' }}>{{ $oficina->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="status_salida">Status</label>
                        <input id="status_salida" name="status_salida" class="form-control" value="{{ old('status_salida', $entrada->status_salida) }}">
                    </div>
                    <div class="col-md-8 mb-3">
                        <label for="incidente_salida">Incidente</label>
                        <input id="incidente_salida" name="incidente_salida" class="form-control" value="{{ old('incidente_salida', $entrada->incidente_salida) }}">
                    </div>
                    <div class="col-12 mb-3">
                        <label for="notas_salida">Notas</label>
                        <textarea id="notas_salida" name="notas_salida" rows="4" class="form-control">{{ old('notas_salida', $entrada->notas_salida) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Guardar salida</button>
        <a href="{{ route('entradas.show', $entrada) }}" class="btn btn-secondary mt-3">Cancelar</a>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var transportadora = document.getElementById('transportadora_id');
        var cobertura = document.getElementById('modalidad_entrega');
        var oficina = document.getElementById('oficina_id');
        var opciones = Array.prototype.slice.call(oficina.querySelectorAll('option[data-transportadora]'));

        function filtrarOficinas() {
            var ocurre = cobertura.value === 'ocurre';
            var transportadoraId = transportadora.value;
            var seleccionActual = oficina.value;
            oficina.disabled = !ocurre || !transportadoraId;
            opciones.forEach(function (opcion) {
                var visible = ocurre && transportadoraId && opcion.dataset.transportadora === transportadoraId;
                opcion.hidden = !visible;
                if (!visible && opcion.value === seleccionActual) {
                    oficina.value = '';
                }
            });
        }

        transportadora.addEventListener('change', filtrarOficinas);
        cobertura.addEventListener('change', filtrarOficinas);
        filtrarOficinas();
    });
</script>
@endsection
