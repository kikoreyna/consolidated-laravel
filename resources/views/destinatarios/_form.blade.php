<div class="row">
    <div class="col-md-6 mb-3">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $destinatario->nombre ?? '') }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label>Persona de contacto</label>
        <input type="text" name="contacto" class="form-control" value="{{ old('contacto', $destinatario->contacto ?? '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label>Teléfono</label>
        <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $destinatario->telefono ?? '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label>Correo electrónico</label>
        <input type="email" name="correo_electronico" class="form-control" value="{{ old('correo_electronico', $destinatario->correo_electronico ?? '') }}">
    </div>
    <div class="col-md-12 mb-3">
        <label>Dirección</label>
        <textarea name="direccion" class="form-control" rows="2">{{ old('direccion', $destinatario->direccion ?? '') }}</textarea>
    </div>
    <div class="col-md-4 mb-3">
        <label>Código postal</label>
        <input type="text" name="codigo_postal" class="form-control" value="{{ old('codigo_postal', $destinatario->codigo_postal ?? '') }}">
    </div>
    <div class="col-md-4 mb-3">
        <label>Ciudad</label>
        <input type="text" name="ciudad" class="form-control" value="{{ old('ciudad', $destinatario->ciudad ?? '') }}">
    </div>
    <div class="col-md-4 mb-3">
        <label>Estado</label>
        <input type="text" name="estado" class="form-control" value="{{ old('estado', $destinatario->estado ?? '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label>País</label>
        <input type="text" name="pais" class="form-control" value="{{ old('pais', $destinatario->pais ?? '') }}">
    </div>
    <div class="col-md-12 mb-3">
        <label>Referencias</label>
        <textarea name="referencias" class="form-control" rows="2">{{ old('referencias', $destinatario->referencias ?? '') }}</textarea>
    </div>
    <div class="col-md-12 mb-3">
        <label>Observaciones</label>
        <textarea name="observaciones" class="form-control" rows="3">{{ old('observaciones', $destinatario->observaciones ?? '') }}</textarea>
    </div>
    <div class="col-md-4 mb-3">
        <label>Estado</label>
        <select name="activo" class="form-control" required>
            <option value="1" {{ old('activo', $destinatario->activo ?? true) ? 'selected' : '' }}>Activo</option>
            <option value="0" {{ !old('activo', $destinatario->activo ?? true) ? 'selected' : '' }}>Inactivo</option>
        </select>
    </div>
</div>
