<script>
(() => {
    document.querySelectorAll('[data-envio-caja]').forEach(formulario => {
        if (formulario.dataset.iniciado) return;
        formulario.dataset.iniciado = '1';
        formulario.addEventListener('submit', evento => {
            if (formulario.dataset.enviando === '1') {
                evento.preventDefault();
                return;
            }
            formulario.dataset.enviando = '1';
            formulario.setAttribute('aria-busy', 'true');
            formulario.querySelectorAll('button[type="submit"]').forEach(boton => {
                boton.setAttribute('aria-disabled', 'true');
                boton.style.pointerEvents = 'none';
            });
        });
        window.addEventListener('pageshow', evento => {
            if (!evento.persisted) return;
            delete formulario.dataset.enviando;
            formulario.removeAttribute('aria-busy');
            formulario.querySelectorAll('button[type="submit"]').forEach(boton => {
                boton.removeAttribute('aria-disabled');
                boton.style.pointerEvents = '';
            });
        });
    });
})();
</script>
