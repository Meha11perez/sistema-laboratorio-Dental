@php
    $ordenFormulario = $orden ?? null;
    $areaFormulario = old('area_trabajo', $ordenFormulario?->area_trabajo ?? '');
    $tipoFormulario = old('tipo_protesis_id', $ordenFormulario?->tipo_protesis_id);
    $etapaFormulario = old('etapa_actual_id', $ordenFormulario?->etapa_actual_id);
@endphp

<div>
    <label for="area_trabajo" class="block text-sm font-semibold text-slate-700 mb-2">
        Área de trabajo *
    </label>
    <select id="area_trabajo" name="area_trabajo" required
            class="w-full border border-slate-300 rounded-lg px-4 py-3 bg-white">
        <option value="">Seleccione un área</option>
        <option value="removible" @selected($areaFormulario === 'removible')>Prótesis Removibles</option>
        <option value="fija" @selected($areaFormulario === 'fija')>Prótesis Fijas</option>
        <option value="cromo_cobalto" @selected($areaFormulario === 'cromo_cobalto')>Cromo Cobalto</option>
        <option value="ortodoncia" @selected($areaFormulario === 'ortodoncia')>Aparatos de Ortodoncia</option>
    </select>
</div>

<div>
    <label for="tipo_protesis_id" class="block text-sm font-semibold text-slate-700 mb-2">
        Tipo de trabajo *
    </label>
    <select id="tipo_protesis_id" name="tipo_protesis_id" required
            class="w-full border border-slate-300 rounded-lg px-4 py-3 bg-white">
        <option value="">Seleccione un tipo de trabajo</option>
        @foreach ($tiposProtesis as $tipo)
            <option value="{{ $tipo->id }}" data-area="{{ $tipo->area_trabajo }}"
                    @selected($tipoFormulario == $tipo->id)>
                {{ $tipo->nombre }}
            </option>
        @endforeach
    </select>
    <p id="aviso_tipo_protesis" aria-live="polite" class="text-xs text-amber-700 mt-2" hidden></p>
</div>

<div>
    <label for="etapa_actual_id" class="block text-sm font-semibold text-slate-700 mb-2">
        Etapa actual
    </label>
    <select id="etapa_actual_id" name="etapa_actual_id"
            class="w-full border border-slate-300 rounded-lg px-4 py-3 bg-white">
        <option value="">Sin asignar</option>
        @foreach ($etapas as $etapa)
            <option value="{{ $etapa->id }}"
                    data-areas="{{ json_encode($etapa->areas_trabajo ?? []) }}"
                    @selected($etapaFormulario == $etapa->id)>
                {{ $etapa->nombre }}
            </option>
        @endforeach
    </select>
    <p id="aviso_etapa" aria-live="polite" class="text-xs text-amber-700 mt-2" hidden></p>
</div>

<script>
(() => {
    const area = document.getElementById('area_trabajo');
    const tipo = document.getElementById('tipo_protesis_id');
    const etapa = document.getElementById('etapa_actual_id');
    const avisoTipo = document.getElementById('aviso_tipo_protesis');
    const avisoEtapa = document.getElementById('aviso_etapa');
    if (!area || !tipo || !etapa) return;

    function mostrarAviso(elemento, texto) {
        if (!elemento) return;
        elemento.textContent = texto;
        elemento.hidden = !texto;
    }

    function filtrarProduccion(seCambioArea = false) {
        let tiposDisponibles = 0;
        for (const opcion of tipo.options) {
            if (!opcion.value) continue;
            const permitido = !!area.value && opcion.dataset.area === area.value;
            opcion.hidden = !permitido;
            opcion.disabled = !permitido;
            if (permitido) tiposDisponibles++;
        }

        const tipoAnterior = tipo.options[tipo.selectedIndex];
        const tipoIncompatible = tipoAnterior?.disabled;
        if (tipoIncompatible) tipo.value = '';

        mostrarAviso(avisoTipo,
            area.value && !tiposDisponibles
                ? 'No hay tipos activos configurados para esta área.'
                : tipoIncompatible
                    ? 'Seleccione un tipo de trabajo del área elegida.'
                    : ''
        );

        const etapasDisponibles = [];
        for (const opcion of etapa.options) {
            if (!opcion.value) continue;
            let areas = [];
            try { areas = JSON.parse(opcion.dataset.areas || '[]'); } catch {}
            const permitido = !!area.value && Array.isArray(areas) && areas.includes(area.value);
            opcion.hidden = !permitido;
            opcion.disabled = !permitido;
            if (permitido) etapasDisponibles.push(opcion.value);
        }

        const etapaAnterior = etapa.options[etapa.selectedIndex];
        const etapaIncompatible = etapaAnterior?.disabled;
        if (etapaIncompatible) etapa.value = '';

        // Al cambiar a un área con una sola etapa, seleccionarla explícitamente en el formulario.
        if (seCambioArea && etapasDisponibles.length === 1) {
            etapa.value = etapasDisponibles[0];
        }

        mostrarAviso(avisoEtapa,
            area.value && !etapasDisponibles.length
                ? 'No hay etapas activas configuradas para esta área.'
                : etapaIncompatible && !etapa.value
                    ? 'La etapa anterior no corresponde al área. Seleccione una etapa compatible.'
                    : ''
        );
    }

    area.addEventListener('change', () => filtrarProduccion(true));
    tipo.addEventListener('change', () => mostrarAviso(avisoTipo, ''));
    etapa.addEventListener('change', () => mostrarAviso(avisoEtapa, ''));
    filtrarProduccion();
})();
</script>
