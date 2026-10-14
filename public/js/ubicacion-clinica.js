(() => {
    function iniciarUbicaciones() {
        for (const contenedor of document.querySelectorAll('[data-ubicacion-clinica]')) {
            if (contenedor.dataset.inicializado === '1') continue;
            const departamento = contenedor.querySelector('[name="departamento"]');
            const municipio = contenedor.querySelector('[name="municipio"]');
            const aviso = contenedor.querySelector('[aria-live="polite"]');
            if (!departamento || !municipio || !aviso) continue;

            let configuracion;
            try {
                configuracion = JSON.parse(contenedor.dataset.config);
            } catch {
                aviso.hidden = false;
                aviso.textContent = 'No se pudo cargar el listado de municipios. Recargue la página.';
                continue;
            }
            contenedor.dataset.inicializado = '1';

            const normalizar = valor => String(valor ?? '').trim().replace(/\s+/g, ' ')
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();

            function actualizarMunicipios(valorAnterior = '') {
                const clave = departamento.selectedOptions[0]?.dataset.catalogo ?? '';
                const opciones = configuracion.catalogo[clave] ?? [];
                const seleccionado = opciones.find(nombre => normalizar(nombre) === normalizar(valorAnterior));
                municipio.replaceChildren(new Option(
                    departamento.value ? 'Seleccione un municipio' : 'Seleccione primero un departamento', ''
                ));

                for (const nombre of opciones) {
                    const conservar = nombre === seleccionado;
                    municipio.add(new Option(nombre, conservar ? valorAnterior : nombre, false, conservar));
                }
                if (valorAnterior && !seleccionado) {
                    municipio.add(new Option(`${valorAnterior} (valor anterior)`, valorAnterior, false, true));
                }

                // Un valor histórico se conserva al cargar. Al cambiar departamento se descarta.
                const historico = Boolean((departamento.value && !clave) || (valorAnterior && !seleccionado));
                aviso.hidden = !historico;
                aviso.textContent = historico
                    ? 'La ubicación anterior no coincide con el catálogo. Puede conservarla al editar otros datos o seleccionar una ubicación del listado para corregirla.'
                    : '';
                municipio.disabled = !departamento.value && !valorAnterior;
                if (!valorAnterior) municipio.value = '';
            }

            departamento.addEventListener('change', () => actualizarMunicipios());
            municipio.addEventListener('change', () => { aviso.hidden = true; });
            actualizarMunicipios(configuracion.municipioInicial);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', iniciarUbicaciones, { once: true });
    } else {
        iniciarUbicaciones();
    }
})();
