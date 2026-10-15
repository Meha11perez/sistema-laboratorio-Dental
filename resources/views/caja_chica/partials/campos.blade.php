@php($gastoFormulario = $gasto ?? null)
<div class="cc-grid">
    <div><label for="fecha_gasto">Fecha del gasto *</label><input id="fecha_gasto" name="fecha_gasto" type="date" required max="{{ now('America/Guatemala')->toDateString() }}" value="{{ old('fecha_gasto', $gastoFormulario?->fecha_gasto?->toDateString() ?? now('America/Guatemala')->toDateString()) }}"></div>
    <div><label for="responsable">Responsable del gasto *</label><input id="responsable" name="responsable" required maxlength="150" value="{{ old('responsable', $gastoFormulario?->responsable) }}"><p class="cc-help"></p></div>
    <div><label for="cantidad">Cantidad *</label><input id="cantidad" name="cantidad" type="number" min="0.001" max="999999.999" step="0.001" inputmode="decimal" required value="{{ old('cantidad', $gastoFormulario?->cantidad ?? '1') }}"></div>
    <div><label for="precio_unitario">Precio unitario (Q) *</label><input id="precio_unitario" name="precio_unitario" type="number" min="0.01" max="9999999999.99" step="0.01" inputmode="decimal" required placeholder="0.00" value="{{ old('precio_unitario', $gastoFormulario?->precio_unitario) }}"></div>
    <div class="cc-wide"><label for="total_calculado">Total calculado (Q)</label><output id="total_calculado" for="cantidad precio_unitario" class="block text-xl font-bold text-slate-900" aria-live="polite">—</output><p class="cc-help">Cantidad × precio unitario. El sistema verifica el total al guardar, redondeado a dos decimales. Para un servicio o gasto global, utilice cantidad 1.</p></div>
    <div class="cc-wide"><label for="concepto">Especificaciones del gasto *</label><input id="concepto" name="concepto" required maxlength="255" placeholder="Ej. Compra de agua pura para el laboratorio" value="{{ old('concepto', $gastoFormulario?->concepto) }}"></div>
    <div><label for="beneficiario">Proveedor o persona a quien se pagó</label><input id="beneficiario" name="beneficiario" maxlength="150" value="{{ old('beneficiario', $gastoFormulario?->beneficiario) }}"></div>
    <div><label for="metodo_pago">Método de pago *</label><select id="metodo_pago" name="metodo_pago" required>@foreach(['Efectivo', 'Transferencia', 'Depósito', 'Cheque', 'Otro'] as $metodo)<option value="{{ $metodo }}" @selected(old('metodo_pago', $gastoFormulario?->metodo_pago ?? 'Efectivo') === $metodo)>{{ $metodo }}</option>@endforeach</select></div>
    <div><label for="tipo_comprobante">Documento de respaldo *</label><select id="tipo_comprobante" name="tipo_comprobante" required>@foreach(['Recibo', 'Factura', 'Sin comprobante'] as $tipo)<option value="{{ $tipo }}" @selected(old('tipo_comprobante', $gastoFormulario?->tipo_comprobante ?? 'Recibo') === $tipo)>{{ $tipo }}</option>@endforeach</select></div>
    <div><label for="nit">NIT del proveedor</label><input id="nit" name="nit" maxlength="30" value="{{ old('nit', $gastoFormulario?->nit) }}"><p class="cc-help">Opcional para gastos de caja chica.</p></div>
    <div><label for="numero_documento">Número de documento</label><input id="numero_documento" name="numero_documento" maxlength="150" value="{{ old('numero_documento', $gastoFormulario?->numero_documento) }}"><p class="cc-help">Número de factura, recibo u otro respaldo, si dispone de él.</p></div>
    <div><label for="referencia">Número de transferencia, depósito o cheque</label><input id="referencia" name="referencia" maxlength="150" value="{{ old('referencia', $gastoFormulario?->referencia) }}"><p class="cc-help">Obligatorio para transferencia, depósito o cheque.</p></div>
    <div><label for="comprobante">{{ $gastoFormulario ? 'Nuevo archivo de respaldo' : 'Adjuntar comprobante' }}</label><input id="comprobante" name="comprobante" type="file" accept=".pdf,.jpg,.jpeg,.png"><p class="cc-help">Opcional. PDF, JPG o PNG, hasta 5 MB. {{ $gastoFormulario ? 'Si no adjunta otro archivo, se conservará el actual.' : '' }}</p></div>
    <div class="cc-wide"><label for="observaciones">Observaciones</label><textarea id="observaciones" name="observaciones" rows="3" maxlength="1000">{{ old('observaciones', $gastoFormulario?->observaciones) }}</textarea></div>
</div>
<script>
(() => {
    const cantidad = document.getElementById('cantidad');
    const precio = document.getElementById('precio_unitario');
    const total = document.getElementById('total_calculado');
    const metodo = document.getElementById('metodo_pago');
    const referencia = document.getElementById('referencia');
    if (!cantidad || !precio || !total) return;
    function actualizar() {
        const valido = cantidad.checkValidity() && precio.checkValidity() && cantidad.value && precio.value;
        if (!valido || !/^\d{1,6}(?:\.\d{1,3})?$/.test(cantidad.value) || !/^\d{1,10}(?:\.\d{1,2})?$/.test(precio.value)) { total.textContent = '—'; return; }
        const convertir = (valor, escala) => { const [entero, decimal = ''] = valor.split('.'); return BigInt(entero) * (10n ** BigInt(escala)) + BigInt(decimal.padEnd(escala, '0')); };
        const centavos = (convertir(cantidad.value, 3) * convertir(precio.value, 2) + 500n) / 1000n;
        total.textContent = centavos > 0n && centavos <= 999999999999n ? (Number(centavos) / 100).toLocaleString('es-GT', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : 'Total fuera del rango permitido';
    }
    function exigirReferencia() { referencia.required = ['Transferencia', 'Depósito', 'Cheque'].includes(metodo.value); }
    cantidad.addEventListener('input', actualizar);
    precio.addEventListener('input', actualizar);
    metodo.addEventListener('change', exigirReferencia);
    actualizar();
    exigirReferencia();
})();
</script>
