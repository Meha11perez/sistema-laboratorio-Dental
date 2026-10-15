<div id="confirmar-etapa-{{ $orden->id }}">
    <style>
        .confirmacion-etapa { position:fixed; inset:0; width:calc(100% - 2rem); max-width:30rem; max-height:calc(100dvh - 2rem); margin:auto; padding:0; overflow:auto; border:1px solid #dce5ed; border-radius:1.25rem; background:#fff; color:#0f172a; box-shadow:0 28px 80px #0f172a40; }
        .confirmacion-etapa::backdrop { background:#0f172a99; backdrop-filter:blur(4px); }
        .confirmacion-etapa[open] { animation:mostrar-confirmacion-etapa .16s ease-out; }
        @keyframes mostrar-confirmacion-etapa { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
        @media (prefers-reduced-motion:reduce) { .confirmacion-etapa[open] { animation:none; } }
    </style>
    <form method="POST" action="{{ route('ordenes.produccion.completar', $orden) }}" data-form-etapa>
        @csrf
        @method('PUT')
        <button type="submit" data-abrir-etapa disabled class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#315875] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#182d47] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315875] disabled:cursor-wait disabled:opacity-60" aria-haspopup="dialog" aria-controls="dialogo-etapa-{{ $orden->id }}">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4 4L19 6" /></svg>
            Completar mi etapa
        </button>
    </form>
    <p data-error-etapa hidden class="mt-3 text-sm text-red-700">No se pudo abrir la confirmación. Recargue la página o utilice otro navegador.</p>
    <noscript><p class="mt-3 text-sm text-amber-800">Active JavaScript para confirmar que completó su etapa.</p></noscript>
    <dialog id="dialogo-etapa-{{ $orden->id }}" class="confirmacion-etapa" aria-labelledby="titulo-etapa-{{ $orden->id }}" aria-describedby="descripcion-etapa-{{ $orden->id }}">
        <div class="p-5 sm:p-7">
            <div class="flex items-start justify-between gap-4">
                <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#e7eef8] text-[#315875]">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 4H6a2 2 0 0 0-2 2v14h16V6a2 2 0 0 0-2-2h-3M9 3h6v4H9zM8 14l3 3 5-6" /></svg>
                </span>
                <button type="button" data-cerrar-etapa aria-label="Cerrar confirmación" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315875]">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m6 6 12 12M6 18 18 6" /></svg>
                </button>
            </div>
            <p class="mt-5 text-xs font-semibold uppercase tracking-widest text-[#315875]">Confirmación de producción</p>
            <h2 id="titulo-etapa-{{ $orden->id }}" class="mt-2 text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">¿Completó su etapa?</h2>
            <p id="descripcion-etapa-{{ $orden->id }}" class="mt-3 text-sm leading-6 text-slate-600">Confirme que terminó el trabajo asignado. Recepción o Administración revisará el siguiente paso.</p>
            <dl class="mt-5 space-y-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="min-w-0"><dt class="text-xs text-slate-500">Orden de trabajo</dt><dd class="mt-1 break-words text-sm font-semibold text-slate-900">{{ $orden->codigo }}</dd></div>
                <div class="min-w-0"><dt class="text-xs text-slate-500">Etapa que completó</dt><dd class="mt-1 break-words text-sm font-semibold text-slate-900">{{ $orden->etapaActual?->nombre ?? 'Sin asignar' }}</dd></div>
            </dl>
            <p class="mt-4 text-xs leading-5 text-slate-500">La etapa quedará pendiente de revisión. El siguiente paso se confirmará desde Revisiones de producción.</p>
        </div>
        <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50 px-5 py-4 sm:flex-row sm:justify-end sm:px-7">
            <button type="button" data-cerrar-etapa autofocus class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315875]">Volver</button>
            <button type="button" data-confirmar-etapa class="inline-flex items-center justify-center rounded-lg bg-[#315875] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#182d47] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315875] disabled:cursor-wait disabled:opacity-60">Sí, completar etapa</button>
        </div>
        <p class="sr-only" data-estado-etapa aria-live="polite"></p>
    </dialog>
</div>
<script>
(() => {
    const contenedor = document.currentScript.previousElementSibling;
    const formulario = contenedor.querySelector('[data-form-etapa]');
    const dialogo = contenedor.querySelector('dialog');
    const abrir = contenedor.querySelector('[data-abrir-etapa]');
    const confirmar = contenedor.querySelector('[data-confirmar-etapa]');
    const cerrar = contenedor.querySelectorAll('[data-cerrar-etapa]');
    const estado = contenedor.querySelector('[data-estado-etapa]');
    let autorizado = false;
    let enviando = false;
    if (typeof dialogo.showModal !== 'function' || typeof formulario.requestSubmit !== 'function') {
        contenedor.querySelector('[data-error-etapa]').hidden = false;
        return;
    }
    abrir.disabled = false;
    formulario.addEventListener('submit', (evento) => {
        if (enviando) {
            evento.preventDefault();
            return;
        }
        if (!autorizado) {
            evento.preventDefault();
            if (!dialogo.open) dialogo.showModal();
            return;
        }
        autorizado = false;
        enviando = true;
        abrir.disabled = true;
        confirmar.disabled = true;
        cerrar.forEach(boton => boton.disabled = true);
        formulario.setAttribute('aria-busy', 'true');
        confirmar.textContent = 'Guardando…';
        estado.textContent = 'Registrando la etapa completada.';
    });
    cerrar.forEach(boton => boton.addEventListener('click', () => {
        if (!enviando) dialogo.close();
    }));
    dialogo.addEventListener('cancel', (evento) => {
        if (enviando) evento.preventDefault();
    });
    dialogo.addEventListener('close', () => abrir.focus());
    confirmar.addEventListener('click', () => {
        if (enviando) return;
        autorizado = true;
        formulario.requestSubmit();
        autorizado = false;
    });
    window.addEventListener('pageshow', (evento) => {
        if (!evento.persisted) return;
        autorizado = false;
        enviando = false;
        abrir.disabled = false;
        confirmar.disabled = false;
        cerrar.forEach(boton => boton.disabled = false);
        formulario.removeAttribute('aria-busy');
        confirmar.textContent = 'Sí, completar etapa';
        estado.textContent = '';
        if (dialogo.open) dialogo.close();
    });
})();
</script>
