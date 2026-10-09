@php
    $camposClinica = [
        'nombre' => 'Nombre de la clínica',
        'direccion' => 'Dirección exacta',
        'asistente_secretaria' => 'Asistente o secretaria',
        'nit' => 'NIT',
        'telefono' => 'Teléfono de la clínica',
        'correo' => 'Correo de la clínica',
        'departamento' => 'Departamento',
        'municipio' => 'Municipio',
    ];
    $clinicaSeleccionada = old('clinica_id', isset($odontologo) ? $odontologo->clinica_id : null);
    $datosClinicas = $clinicas->mapWithKeys(function ($clinica) use ($camposClinica) {
        $datos = [];
        foreach ($camposClinica as $campo => $etiqueta) {
            $datos[$campo] = $clinica->{$campo};
        }
        return [(string) $clinica->id => $datos];
    })->all();
    $datosIniciales = $datosClinicas[(string) $clinicaSeleccionada] ?? null;
@endphp
<section class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:p-5"
         aria-labelledby="titulo_datos_clinica">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 id="titulo_datos_clinica" class="text-sm font-semibold text-slate-900">
                Datos de la clínica asociada
            </h2>
            <p id="ayuda_datos_clinica" class="mt-1 text-xs text-slate-500" aria-live="polite">
                {{ $datosIniciales
                    ? 'Estos datos corresponden a la clínica seleccionada.'
                    : 'Seleccione una clínica para consultar su información.' }}
            </p>
        </div>
        <a href="{{ route('administracion.clinicas.create') }}"
           class="text-sm font-semibold text-[#315875] hover:underline">
            Registrar una clínica
        </a>
    </div>
    <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($camposClinica as $campo => $etiqueta)
            <div class="min-w-0">
                <dt class="text-xs font-medium text-slate-500">{{ $etiqueta }}</dt>
                <dd id="dato_clinica_{{ $campo }}" class="mt-1 break-words text-sm text-slate-800">
                    {{ trim((string) ($datosIniciales[$campo] ?? '')) ?: '—' }}
                </dd>
            </div>
        @endforeach
    </dl>
    <p class="mt-4 text-xs text-slate-500">
        La información de la clínica se administra en el módulo Clínicas.
    </p>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selector = document.getElementById('clinica_id');
    const ayuda = document.getElementById('ayuda_datos_clinica');
    const clinicas = {{ \Illuminate\Support\Js::from($datosClinicas) }};
    const campos = {{ \Illuminate\Support\Js::from(array_keys($camposClinica)) }};
    if (!selector || !ayuda) return;

    function actualizarDatosClinica() {
        const datos = clinicas[selector.value] || null;
        for (const campo of campos) {
            const elemento = document.getElementById('dato_clinica_' + campo);
            if (elemento) {
                elemento.textContent = String(datos?.[campo] ?? '').trim() || '—';
            }
        }
        ayuda.textContent = datos
            ? 'Estos datos corresponden a la clínica seleccionada.'
            : 'Seleccione una clínica para consultar su información.';
    }

    selector.addEventListener('change', actualizarDatosClinica);
    actualizarDatosClinica();
});
</script>
