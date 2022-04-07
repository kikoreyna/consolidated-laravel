<?php

$options_ambito = (object) [
    'cualquier' => '- Cualquier ámbito -',
    'con' => 'Consolidadas',
    'sin' => 'Sin consolidar',
];

?>

<div class="mb-3">
    <label for="selectFiltroAmbito" class="form-label d-block small">Ámbito</label>
    <select name="ambito" id="selectFiltroAmbito" class="form-select">
        @foreach ($options_ambito as $value => $label)
        <option value="{{ $value }}" {{ toggleSelected($value, request('ambito')) }}>{{ $label }}</option>
        @endforeach
    </select>
</div>
