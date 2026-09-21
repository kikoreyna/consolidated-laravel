<div class="row">
    <div class="col-md-6 mb-3">
        <label>Transportadora</label>
        <select name="transportadora_id" class="form-control" required>
            <option value="">Selecciona una transportadora</option>
            @foreach($transportadoras as $transportadora)
                <option value="{{ $transportadora->id }}" {{ old('transportadora_id', $oficina->transportadora_id ?? '') == $transportadora->id ? 'selected' : '' }}>{{ $transportadora->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label>Nombre de la oficina</label>
        <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $oficina->nombre ?? '') }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label>Persona de contacto</label>
        <input type="text" name="contacto" class="form-control" value="{{ old('contacto', $oficina->contacto ?? '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label>Teléfono</label>
        <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $oficina->telefono ?? '') }}">
    </div>
    <div class="col-md-12 mb-3">
        <label>Observaciones</label>
        <textarea name="observaciones" class="form-control" rows="4">{{ old('observaciones', $oficina->observaciones ?? '') }}</textarea>
    </div>
    <div class="col-md-4 mb-3">
        <label>Estado</label>
        <select name="activa" class="form-control" required>
            <option value="1" {{ old('activa', $oficina->activa ?? true) ? 'selected' : '' }}>Activa</option>
            <option value="0" {{ !old('activa', $oficina->activa ?? true) ? 'selected' : '' }}>Inactiva</option>
        </select>
    </div>
</div>
