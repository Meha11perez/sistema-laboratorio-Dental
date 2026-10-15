@php($importe = fn ($valor) => \App\Support\ImporteCajaChica::formato((int) $valor))
<dl class="mb-5">
    @foreach(['fecha'=>'Fecha', 'proveedor'=>'Proveedor', 'nit'=>'NIT', 'serie'=>'Serie', 'numero_factura'=>'Factura', 'metodo_pago'=>'Método de pago informado', 'referencia'=>'Referencia', 'observaciones'=>'Observaciones'] as $campo=>$etiqueta)
    <div><dt>{{ $etiqueta }}</dt><dd>{{ ($datos[$campo] ?? '') !== '' ? ($datos[$campo] ?? '—') : '—' }}</dd></div>
    @endforeach
    <div><dt>Total</dt><dd>Q {{ $importe($datos['total_centavos'] ?? 0) }}</dd></div>
</dl>
<div class="cc-scroll" tabindex="0" role="region" aria-label="Detalle de materiales y servicios"><table class="cc-table"><thead><tr><th>Tipo</th><th>Descripción</th><th>Unidad</th><th>Cantidad</th><th>Precio unitario</th><th>Total</th></tr></thead><tbody>
    @foreach($datos['detalles'] ?? [] as $linea)<tr><td>{{ $linea['tipo'] }}</td><td>{{ $linea['descripcion'] }}@if(!empty($linea['material_solicitado']))<p class="cc-help">Material solicitado: {{ $linea['material_solicitado'] }}{{ empty($linea['material_id']) ? ' · pendiente de alta al recibir' : '' }}</p>@endif</td><td>{{ $linea['unidad'] }}</td><td>{{ \App\Support\ImporteCajaChica::decimal($linea['cantidad_centesimas']) }}</td><td>Q {{ $importe($linea['precio_centavos']) }}</td><td>Q {{ $importe($linea['total_centavos']) }}</td></tr>@endforeach
</tbody></table></div>
