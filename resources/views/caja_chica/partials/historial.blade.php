@foreach($gasto->correcciones->sortByDesc('id') as $correccion)
    @php
        $antes = $correccion->datos_anteriores;
        $despues = $correccion->datos_resultantes ?? $correccion->datos_propuestos ?? ['estado' => 'Anulado'];
        $camposHistorial = ['fecha_gasto' => 'Fecha del gasto', 'concepto' => 'Concepto', 'beneficiario' => 'Proveedor o beneficiario', 'cantidad_milesimas' => 'Cantidad', 'precio_unitario_centavos' => 'Precio unitario', 'monto_centavos' => 'Total', 'responsable' => 'Responsable', 'numero_documento' => 'N.º documento', 'metodo_pago' => 'Método de pago', 'tipo_comprobante' => 'Tipo de documento', 'nit' => 'NIT', 'referencia' => 'Referencia', 'observaciones' => 'Observaciones', 'estado' => 'Estado del gasto'];
    @endphp
    <article class="cc-history" id="correccion-{{ $correccion->id }}">
        <div class="cc-header mb-3"><h3 class="font-semibold">Solicitud #{{ $correccion->id }}: {{ $correccion->tipo }}</h3>@include('caja_chica.partials.estado', ['estado' => $correccion->estado])</div>
        <p class="cc-muted">Solicitada por <strong>{{ $correccion->usuarioSolicitud?->name ?? '—' }}</strong> el {{ $correccion->created_at->format('d/m/Y H:i') }}.</p>
        <p class="mt-2 text-sm whitespace-pre-wrap"><strong>Motivo:</strong> {{ $correccion->motivo_solicitud }}</p>
        <div class="cc-scroll mt-4" tabindex="0" role="region" aria-label="Comparación de valores de la solicitud {{ $correccion->id }}">
            <table style="min-width:540px"><thead><tr><th>Dato</th><th>Valor anterior</th><th>{{ $correccion->estado === 'Aprobada' ? 'Valor aplicado' : 'Valor propuesto' }}</th></tr></thead><tbody>
                @foreach($camposHistorial as $campo => $etiqueta)
                    @php($nuevo = array_key_exists($campo, $despues) ? $despues[$campo] : ($antes[$campo] ?? null))
                    @if(($antes[$campo] ?? null) !== $nuevo)
                        <tr><td class="font-semibold">{{ $etiqueta }}</td><td>{{ in_array($campo, ['monto_centavos', 'precio_unitario_centavos'], true) ? 'Q '.$dinero($antes[$campo]) : ($campo === 'cantidad_milesimas' ? \App\Support\ImporteCajaChica::cantidadDecimal($antes[$campo]) : (($antes[$campo] ?? null) ?: '—')) }}</td><td>{{ in_array($campo, ['monto_centavos', 'precio_unitario_centavos'], true) ? 'Q '.$dinero($nuevo) : ($campo === 'cantidad_milesimas' ? \App\Support\ImporteCajaChica::cantidadDecimal($nuevo) : ($nuevo ?: '—')) }}</td></tr>
                    @endif
                @endforeach
                @if(($antes['comprobante_path'] ?? null) !== ($despues['comprobante_path'] ?? $antes['comprobante_path'] ?? null))
                    <tr><td class="font-semibold">Comprobante</td><td>@if($antes['comprobante_path'] ?? null)<a class="cc-link" href="{{ route('caja-chica.correcciones.comprobante', ['correccion' => $correccion, 'version' => 'anterior']) }}">Archivo anterior</a>@else — @endif</td><td><a class="cc-link" href="{{ route('caja-chica.correcciones.comprobante', $correccion) }}">Archivo propuesto</a></td></tr>
                @endif
            </tbody></table>
        </div>
        @if($correccion->fecha_resolucion)
            <p class="cc-muted mt-3">{{ $correccion->estado }} por <strong>{{ $correccion->usuarioResolucion?->name ?? '—' }}</strong> el {{ $correccion->fecha_resolucion->format('d/m/Y H:i') }}.</p>
            <p class="mt-2 text-sm whitespace-pre-wrap"><strong>Motivo de la decisión:</strong> {{ $correccion->motivo_resolucion }}</p>
            @if($correccion->estado === 'Rechazada')<p class="cc-help">La solicitud fue rechazada. Sus valores propuestos no se aplicaron al gasto.</p>@endif
        @elseif(auth()->user()->role?->nombre === 'Administrador')
            <form action="{{ route('caja-chica.correcciones.resolver', $correccion) }}" method="POST" class="mt-5" data-envio-caja>
                @csrf
                @method('PATCH')
                <label for="motivo-correccion-{{ $correccion->id }}">Motivo de la decisión *</label>
                <textarea id="motivo-correccion-{{ $correccion->id }}" name="motivo" required maxlength="1000" rows="2"></textarea>
                <p class="cc-help">Aprobar aplica estos cambios. Si el monto corregido supera el límite del mes, esta decisión también autoriza el excedente de este gasto.</p>
                <div class="cc-actions mt-3"><button class="cc-btn cc-btn-primary" type="submit" name="decision" value="aprobar">Aprobar {{ $correccion->tipo === 'Anular' ? 'anulación' : 'corrección' }}</button><button class="cc-btn cc-btn-danger" type="submit" name="decision" value="rechazar">Rechazar solicitud</button></div>
            </form>
        @else
            <p class="cc-help mt-3">Pendiente de decisión del Administrador. El gasto conserva sus valores actuales.</p>
        @endif
    </article>
@endforeach
