<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Caja chica {{ $mes }}</title>
    <style>
        * { box-sizing:border-box; } body { margin:24px; color:#172b4d; font:12px Arial,sans-serif; } header { display:flex; align-items:center; justify-content:space-between; gap:24px; border-bottom:2px solid #315875; padding-bottom:16px; } img { max-width:150px; max-height:70px; } h1 { font-size:23px; margin:0 0 8px; } p { line-height:1.5; } .resumen { display:flex; gap:22px; flex-wrap:wrap; margin:22px 0; } .resumen div { min-width:180px; } table { width:100%; border-collapse:collapse; font-size:11px; } th,td { padding:9px; text-align:left; border-bottom:1px solid #dce5ee; vertical-align:top; overflow-wrap:anywhere; } th { background:#edf3f8; } .numero { text-align:right; white-space:nowrap; } .acciones { margin:20px 0; display:flex; gap:12px; } button,a { color:#315875; font:inherit; padding:8px 12px; } @page { size:A4 landscape; margin:12mm; } @media print { body { margin:0; } .acciones { display:none; } thead { display:table-header-group; } tr { break-inside:avoid; } }
    </style>
</head>
<body>
@php($dinero = fn ($centavos) => \App\Support\ImporteCajaChica::formato((int) $centavos))
<div class="acciones"><button type="button" onclick="window.print()">Imprimir</button><a href="{{ route('caja-chica.index', ['mes' => $mes]) }}">Volver a caja chica</a></div>
<header><div><h1>Caja chica</h1><p>Mes: {{ $mes }} · Emitido: {{ now('America/Guatemala')->format('d/m/Y H:i') }}</p></div>@if(is_file(public_path('images/logo-dis-dental.png')))<img src="{{ asset('images/logo-dis-dental.png') }}" alt="Diseño Dental">@endif</header>
<div class="resumen"><div>Límite mensual<br><strong>Q {{ $dinero($resumen['limite']) }}</strong></div><div>Gastos registrados y autorizados<br><strong>Q {{ $dinero($resumen['gastado']) }}</strong></div><div>Disponible del límite<br><strong>Q {{ $dinero($resumen['disponible']) }}</strong></div><div>Pendiente de autorización<br><strong>Q {{ $dinero($resumen['pendiente']) }}</strong></div></div>
@if($resumen['exceso'] > 0)<p>Exceso autorizado: <strong>Q {{ $dinero($resumen['exceso']) }}</strong>.</p>@endif
<table><thead><tr><th>Gasto</th><th>Fecha</th><th>N.º documento</th><th>Especificaciones / proveedor</th><th>Cantidad</th><th class="numero">Precio unitario Q</th><th class="numero">Total Q</th><th>Estado</th><th>Método / documento</th><th>Referencia</th><th>Responsable</th></tr></thead><tbody>
    @forelse($gastos as $gasto)<tr><td>#{{ $gasto->id }}</td><td>{{ $gasto->fecha_gasto->format('d/m/Y') }}</td><td>{{ $gasto->numero_documento ?? '—' }}</td><td>{{ $gasto->concepto }}<br>{{ $gasto->beneficiario }}</td><td>{{ $gasto->cantidad }}</td><td class="numero">{{ $dinero($gasto->precio_unitario_centavos) }}</td><td class="numero">{{ $dinero($gasto->monto_centavos) }}</td><td>{{ $gasto->estado }}</td><td>{{ $gasto->metodo_pago }}<br>{{ $gasto->tipo_comprobante }}</td><td>{{ $gasto->referencia ?? '—' }}</td><td>{{ $gasto->responsable }}</td></tr>@empty<tr><td colspan="11">No hay gastos en este mes.</td></tr>@endforelse
</tbody></table>
<p> Los pendientes, rechazados y anulados se conservan en el listado y no se suman a ese total. El disponible corresponde al límite mensual, no al efectivo en caja.</p>
</body>
</html>
