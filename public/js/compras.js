(() => {
    const form = document.querySelector('[data-compra-form]');
    if (!form) return;
    const lista = form.querySelector('[data-compra-lineas]');
    if (!lista) return;
    let siguiente = Math.max(-1, ...Array.from(lista.querySelectorAll('[name]'), e => Number(e.name.match(/detalles\[(\d+)\]/)?.[1] ?? -1))) + 1;
    const centavos = valor => {
        if (!/^\d{1,6}(?:\.\d{1,2})?$/.test(valor)) return 0n;
        const [entero, decimal = ''] = valor.split('.');
        return BigInt(entero) * 100n + BigInt(decimal.padEnd(2, '0'));
    };
    const dinero = valor => 'Q ' + (valor / 100n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + '.' + (valor % 100n).toString().padStart(2, '0');
    function actualizar() {
        let total = 0n;
        for (const fila of lista.querySelectorAll('[data-compra-linea]')) {
            const esMaterial = fila.querySelector('[data-linea-tipo]').value === 'Material';
            const material = fila.querySelector('[data-linea-material]');
            material.disabled = !esMaterial;
            material.required = esMaterial;
            const nuevo = esMaterial && material.value === 'nuevo';
            for (const contenedor of fila.querySelectorAll('[data-material-nuevo]')) {
                contenedor.hidden = !nuevo;
                for (const campo of contenedor.querySelectorAll('input')) {
                    campo.disabled = !nuevo;
                    campo.required = nuevo;
                }
            }
            const cantidad = centavos(fila.querySelector('[data-linea-cantidad]').value);
            const precio = centavos(fila.querySelector('[data-linea-precio]').value);
            const subtotal = (cantidad * precio + 50n) / 100n;
            fila.querySelector('[data-linea-subtotal]').textContent = dinero(subtotal);
            total += subtotal;
        }
        form.querySelector('[data-compra-total]').textContent = dinero(total);
        const referencia = form.querySelector('[name="referencia"]');
        referencia.required = ['Transferencia', 'Depósito', 'Cheque'].includes(form.querySelector('[name="metodo_pago"]').value);
    }
    form.addEventListener('input', actualizar);
    form.addEventListener('change', actualizar);
    form.querySelector('[data-compra-agregar]')?.addEventListener('click', () => {
        if (lista.children.length >= 50) return;
        lista.insertAdjacentHTML('beforeend', document.getElementById('compra-linea-nueva').innerHTML.replaceAll('__I__', String(siguiente++)));
        actualizar();
    });
    lista.addEventListener('click', e => {
        const boton = e.target.closest('[data-linea-quitar]');
        if (boton && !boton.disabled && lista.children.length > 1) { boton.closest('[data-compra-linea]').remove(); actualizar(); }
    });
    form.addEventListener('submit', e => {
        if (form.dataset.enviando) { e.preventDefault(); return; }
        form.dataset.enviando = '1';
        for (const boton of form.querySelectorAll('[type="submit"]')) { boton.disabled = true; boton.textContent = 'Guardando…'; }
    });
    window.addEventListener('pageshow', e => {
        if (e.persisted) window.location.reload();
    });
    actualizar();
})();
