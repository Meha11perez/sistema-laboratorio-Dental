@php
    $ordenFormulario = $orden ?? null;
    $areaFormulario = old('area_trabajo', $ordenFormulario?->area_trabajo ?? '');
    $etapaFormulario = old('etapa_actual_id', $ordenFormulario?->etapa_actual_id);
    $nombreEtapa = static fn ($nombre) => match(
        \Illuminate\Support\Str::lower(trim((string) $nombre))
    ) {
        'rodetes y cubetas individuales' => 'Prueba de rodetes o cubetas individuales',
        'prueba de dientes' => 'Pruebas de dientes',
        'prueba de metal' => 'Pruebas de metal o estructura',
        'biscochos', 'bizcochos' => 'Pruebas de bizcochos',
        'terminados' => 'Terminados',
        'cromos' => 'Cromo cobalto',
        'ortodoncia' => 'Aparatos de ortodoncia',
        default => \Illuminate\Support\Str::ucfirst(
            \Illuminate\Support\Str::lower((string) $nombre)
        ),
    };
@endphp

@if($mostrarArea ?? true)
    <div>
        <label for="area_trabajo" class="block text-sm font-semibold text-slate-700 mb-2">
            Área de trabajo 
        </label>
        <select id="area_trabajo" name="area_trabajo" required
                class="w-full border border-slate-300 rounded-lg px-4 py-3 bg-white normal-case">
            <option value="">Seleccione un área</option>
            <option value="removible" @selected($areaFormulario === 'removible')>Prótesis removibles</option>
            <option value="fija" @selected($areaFormulario === 'fija')>Prótesis fijas</option>
            <option value="cromo_cobalto" @selected($areaFormulario === 'cromo_cobalto')>Cromo cobalto</option>
            <option value="ortodoncia" @selected($areaFormulario === 'ortodoncia')>Aparatos de ortodoncia</option>
        </select>
    </div>
@endif

<div>
    <label for="etapa_actual_id" class="block text-sm font-semibold text-slate-700 mb-2">
        Etapa del trabajo 
    </label>
    <select id="etapa_actual_id" name="etapa_actual_id" required
            class="w-full border border-slate-300 rounded-lg px-4 py-3 bg-white normal-case"
            aria-describedby="aviso_etapa">
        <option value="">Seleccione primero un área</option>
        @foreach ($etapas as $etapa)
            <option value="{{ $etapa->id }}"
                    data-areas="{{ json_encode($etapa->areas_trabajo ?? []) }}"
                    @selected($etapaFormulario == $etapa->id)>
                {{ $nombreEtapa($etapa->nombre) }}
            </option>
        @endforeach
    </select>
    <p id="aviso_etapa" aria-live="polite" class="text-xs text-slate-500 mt-2">
        Seleccione un área para mostrar sus etapas.
    </p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const area = document.getElementById('area_trabajo');
    const etapa = document.getElementById('etapa_actual_id');
    const aviso = document.getElementById('aviso_etapa');
    if (!area || !etapa || !aviso) return;

    function filtrarEtapas() {
        const disponibles = [];

        for (const opcion of etapa.options) {
            if (!opcion.value) continue;
            let areas = [];
            try { areas = JSON.parse(opcion.dataset.areas || '[]'); } catch {}
            const permitido = !!area.value && Array.isArray(areas) && areas.includes(area.value);
            opcion.hidden = !permitido;
            opcion.disabled = !permitido;
            if (permitido) disponibles.push(opcion.value);
        }

        const seleccionAnterior = etapa.options[etapa.selectedIndex];
        if (seleccionAnterior?.disabled) etapa.value = '';

        // Un único ingreso en Cromo cobalto y Ortodoncia: seleccionar su etapa.
        if (['cromo_cobalto', 'ortodoncia'].includes(area.value)
            && disponibles.length === 1) {
            etapa.value = disponibles[0];
        }

        etapa.disabled = !area.value || disponibles.length === 0;
        etapa.options[0].textContent = !area.value
            ? 'Seleccione primero un área'
            : disponibles.length === 0
                ? 'No hay etapas disponibles'
                : 'Seleccione una etapa';

        aviso.textContent = !area.value
            ? 'Seleccione un área para mostrar sus etapas.'
            : disponibles.length === 0
                ? 'No hay etapas activas configuradas para esta área.'
                : ['cromo_cobalto', 'ortodoncia'].includes(area.value)
                    ? 'Esta área tiene un solo ingreso y no utiliza pruebas intermedias.'
                    : 'Seleccione la etapa correspondiente al trabajo recibido.';
    }

    area.addEventListener('change', filtrarEtapas);
    filtrarEtapas();
});
</script>
