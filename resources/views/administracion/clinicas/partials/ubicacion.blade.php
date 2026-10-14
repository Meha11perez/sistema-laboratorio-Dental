@php
    $clinicaFormulario = $clinica ?? null;
    $catalogoUbicaciones = \App\Support\UbicacionGuatemala::catalogo();
    $valorDepartamento = old('departamento', $clinicaFormulario?->departamento ?? '');
    $valorMunicipio = old('municipio', $clinicaFormulario?->municipio ?? '');
    $departamentoInicial = is_string($valorDepartamento) ? $valorDepartamento : '';
    $municipioInicial = is_string($valorMunicipio) ? $valorMunicipio : '';
    $departamentoCatalogo = \App\Support\UbicacionGuatemala::departamento($departamentoInicial);
    $municipiosIniciales = $catalogoUbicaciones[$departamentoCatalogo] ?? [];
    $municipioCatalogo = null;
    foreach ($municipiosIniciales as $municipioNombre) {
        if (\App\Support\UbicacionGuatemala::normalizar($municipioNombre) === \App\Support\UbicacionGuatemala::normalizar($municipioInicial)) {
            $municipioCatalogo = $municipioNombre;
            break;
        }
    }
    $configuracionUbicacion = ['catalogo' => $catalogoUbicaciones, 'municipioInicial' => $municipioInicial];
@endphp

<div class="md:col-span-2" data-ubicacion-clinica data-config="{{ json_encode($configuracionUbicacion, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) }}">
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
            <label for="departamento" class="mb-2 block text-sm font-semibold text-slate-700">Departamento</label>
            <select name="departamento" id="departamento" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-200" aria-describedby="aviso_ubicacion_clinica">
                <option value="">Seleccione un departamento</option>
                @if($departamentoInicial !== '' && !$departamentoCatalogo)
                    <option value="{{ $departamentoInicial }}" data-catalogo="" selected>{{ $departamentoInicial }} (valor anterior)</option>
                @endif
                @foreach($catalogoUbicaciones as $departamentoNombre => $municipiosDepartamento)
                    <option value="{{ $departamentoCatalogo === $departamentoNombre ? $departamentoInicial : $departamentoNombre }}" data-catalogo="{{ $departamentoNombre }}" @selected($departamentoCatalogo === $departamentoNombre)>{{ $departamentoNombre }}</option>
                @endforeach
            </select>
            @error('departamento')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="municipio" class="mb-2 block text-sm font-semibold text-slate-700">Municipio</label>
            <select name="municipio" id="municipio" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 disabled:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-200" @disabled($departamentoInicial === '' && $municipioInicial === '') aria-describedby="aviso_ubicacion_clinica">
                <option value="">{{ $departamentoInicial === '' ? 'Seleccione primero un departamento' : 'Seleccione un municipio' }}</option>
                @if($municipioInicial !== '' && !$municipioCatalogo)
                    <option value="{{ $municipioInicial }}" selected>{{ $municipioInicial }} (valor anterior)</option>
                @endif
                @foreach($municipiosIniciales as $municipioNombre)
                    <option value="{{ $municipioCatalogo === $municipioNombre ? $municipioInicial : $municipioNombre }}" @selected($municipioCatalogo === $municipioNombre)>{{ $municipioNombre }}</option>
                @endforeach
            </select>
            @error('municipio')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <p id="aviso_ubicacion_clinica" class="text-xs text-amber-700 md:col-span-2" aria-live="polite" @if(!(($departamentoInicial !== '' && !$departamentoCatalogo) || ($municipioInicial !== '' && !$municipioCatalogo))) hidden @endif>
            La ubicación anterior no coincide con el catálogo. Puede conservarla al editar otros datos o seleccionar una ubicación del listado para corregirla.
        </p>

        <div class="md:col-span-2">
            <label for="direccion" class="mb-2 block text-sm font-semibold text-slate-700">Dirección exacta</label>
            <textarea name="direccion" id="direccion" rows="3" maxlength="255" placeholder="Avenida o calle, número, zona, colonia, aldea o referencia." class="w-full rounded-lg border border-slate-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-200">{{ old('direccion', $clinicaFormulario?->direccion) }}</textarea>
            @error('direccion')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

@once
    <script src="{{ asset('js/ubicacion-clinica.js') }}" defer></script>
@endonce
