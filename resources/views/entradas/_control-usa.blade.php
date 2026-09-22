<div class="card mb-4">
    <div class="card-header">Recepción y control en bodega USA</div>
    <div class="card-body">
        @if($datosCliente = session('usa_datos_cliente'))
            <div class="alert alert-info">
                <strong>Datos proporcionados por el cliente para {{ $datosCliente['numero'] }}:</strong>
                Peso: {{ $datosCliente['peso'] !== null ? $datosCliente['peso'] . ' lb' : 'N/A' }};
                Medidas: {{ $datosCliente['largo'] !== null ? $datosCliente['largo'] . ' x ' . $datosCliente['ancho'] . ' x ' . $datosCliente['alto'] . ' in' : 'N/A' }};
                Volumen: {{ $datosCliente['volumen'] !== null ? $datosCliente['volumen'] . ' in³' : 'N/A' }}.
            </div>
        @endif
        @if($pendiente = session('usa_pendiente'))
            <p class="mb-3">Guía escaneada: <strong>{{ $pendiente['numero'] }}</strong></p>
            @if($pendiente['accion'] === 'pesar_medir')
                <h5>Capturar peso y medidas</h5>
            @else
                <h5>Capturar peso</h5>
            @endif
            <form action="{{ route('entradas.control-usa.complete') }}" method="POST" class="row g-3">
                @csrf
                <input type="hidden" name="entrada_id" value="{{ $pendiente['entrada_id'] }}">
                <input type="hidden" name="accion" value="{{ $pendiente['accion'] }}">
                <div class="col-md-4"><label>Incidente</label><select name="incidente" class="form-control"><option value="sin_incidente" {{ $pendiente['incidente'] === 'sin_incidente' ? 'selected' : '' }}>Sin incidente</option><option value="dano_embalaje">Daño en embalaje</option><option value="empaque_danado">Empaque dañado</option><option value="faltante">Faltante</option><option value="excedente">Excedente</option><option value="diferencia_peso">Diferencia de peso</option><option value="guia_ilegible">Guía ilegible</option><option value="otro">Otro</option></select></div>
                <div class="col-md-3"><label>Peso (lb)</label><input type="number" step="0.001" min="0" name="peso_usa" class="form-control" required autofocus></div>
                @if($pendiente['accion'] === 'pesar_medir')
                    <div class="col-md-2"><label>Largo (in)</label><input type="number" step="0.01" min="0" name="largo_usa" class="form-control" required></div>
                    <div class="col-md-2"><label>Ancho (in)</label><input type="number" step="0.01" min="0" name="ancho_usa" class="form-control" required></div>
                    <div class="col-md-2"><label>Alto (in)</label><input type="number" step="0.01" min="0" name="alto_usa" class="form-control" required></div>
                @endif
                <div class="col-md-12"><label>Observación</label><input type="text" name="observacion" class="form-control"></div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">Guardar control y volver al escáner</button>
                    <a href="{{ route('bodega-usa.cambiar-modo') }}" class="btn btn-outline-secondary">Cambiar modo</a>
                </div>
            </form>
        @else
            @php($scanAction = session('usa_scan_action'))
            @if($scanAction)
                <form id="usa-escaneo" action="{{ route('entradas.control-usa') }}" method="POST" class="row g-3">
                    @csrf
                    <input type="hidden" name="accion" value="{{ $scanAction }}">
                    <div class="col-md-6">
                        <label for="control_numero">Número de guía</label>
                        <input id="control_numero" type="text" name="numero" class="form-control" placeholder="Escanea la siguiente guía" required autofocus>
                    </div>
                    <div class="col-md-6"><label>Incidente</label><select name="incidente" class="form-control"><option value="sin_incidente">Sin incidente</option><option value="dano_embalaje">Daño en embalaje</option><option value="empaque_danado">Empaque dañado</option><option value="faltante">Faltante</option><option value="excedente">Excedente</option><option value="diferencia_peso">Diferencia de peso</option><option value="guia_ilegible">Guía ilegible</option><option value="otro">Otro</option></select></div>
                    <div class="col-md-12">
                        <label for="control_observacion">Observación</label>
                        <input id="control_observacion" type="text" name="observacion" class="form-control">
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Continuar</button>
                        <a href="{{ route('bodega-usa.cambiar-modo') }}" class="btn btn-outline-secondary">Cambiar modo</a>
                    </div>
                </form>
            @else
            <div id="usa-operaciones" class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary usa-operacion" data-accion="solo_recibido">Recepción</button>
                <button type="button" class="btn btn-outline-primary usa-operacion" data-accion="solo_pesar">Recepción y peso</button>
                <button type="button" class="btn btn-primary usa-operacion" data-accion="pesar_medir">Recepción, peso y medidas</button>
            </div>

            <form id="usa-escaneo" action="{{ route('entradas.control-usa') }}" method="POST" class="row g-3 d-none mt-2">
                @csrf
                <input type="hidden" name="accion" id="usa-accion">
                <div class="col-md-6">
                    <label for="control_numero">Número de guía</label>
                    <input id="control_numero" type="text" name="numero" class="form-control" placeholder="Escanea o escribe el número" required>
                </div>
                <div class="col-md-6"><label>Incidente</label><select name="incidente" class="form-control"><option value="sin_incidente">Sin incidente</option><option value="dano_embalaje">Daño en embalaje</option><option value="empaque_danado">Empaque dañado</option><option value="faltante">Faltante</option><option value="excedente">Excedente</option><option value="diferencia_peso">Diferencia de peso</option><option value="guia_ilegible">Guía ilegible</option><option value="otro">Otro</option></select></div>
                <div class="col-md-12">
                    <label for="control_observacion">Observación</label>
                    <input id="control_observacion" type="text" name="observacion" class="form-control">
                </div>
                <div class="col-md-12"><button type="submit" class="btn btn-primary">Continuar</button></div>
            </form>

            <script>
                document.querySelectorAll('.usa-operacion').forEach(function (button) {
                    button.addEventListener('click', function () {
                        document.getElementById('usa-operaciones').classList.add('d-none');
                        document.getElementById('usa-escaneo').classList.remove('d-none');
                        document.getElementById('usa-accion').value = this.dataset.accion;
                        document.getElementById('control_numero').focus();
                    });
                });
            </script>
            @endif
        @endif
    </div>
</div>
