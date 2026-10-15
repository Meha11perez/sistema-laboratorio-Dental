<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gastos mensuales | Diseño Dental</title>
    <style>
        * { box-sizing:border-box; }
        body { margin:24px; color:#253649; font-family:'Segoe UI',Arial,sans-serif; font-size:12px; }
        header { display:flex; align-items:center; justify-content:space-between; gap:20px; padding-bottom:15px; border-bottom:2px solid #365b7c; }
        .logo { display:block; width:180px; max-width:100%; height:auto; }
        h1 { font-size:22px; margin:0 0 5px; font-weight:600; }
        p { margin:5px 0; }
        .muted { color:#627386; }
        .actions { display:flex; justify-content:flex-end; gap:10px; margin-bottom:18px; }
        .actions a,.actions button { padding:10px 14px; border:1px solid #365b7c; border-radius:7px; background:#365b7c; color:#fff; font:inherit; text-decoration:none; cursor:pointer; }
        .summary { padding:12px 0; line-height:1.7; }
        .filters { padding:10px; background:#f1f6f8; margin-bottom:15px; line-height:1.7; }
        table { width:100%; border-collapse:collapse; table-layout:fixed; font-size:11px; }
        th { background:#edf5f8; color:#365b7c; text-align:left; }
        th,td { border:1px solid #dce5ec; padding:8px; vertical-align:top; overflow-wrap:anywhere; word-break:normal; }
        td small { display:block; color:#627386; margin-top:3px; }
        .importe { text-align:right; white-space:nowrap; }
        .table-scroll { overflow-x:auto; }
        .notas { margin-top:14px; line-height:1.6; }
        @page { size:A4 landscape; margin:12mm; }
        @media screen and (max-width:900px) { .table-scroll table { min-width:950px; } }
        @media screen and (max-width:600px) { body { margin:16px; } header { align-items:flex-start; flex-direction:column; } .actions { flex-wrap:wrap; } }
        @media print {
            body { margin:0; }
            .actions { display:none; }
            .table-scroll { overflow:visible; }
            thead { display:table-header-group; }
            tr { break-inside:avoid; }
            th,.filters { print-color-adjust:exact; -webkit-print-color-adjust:exact; }
        }
    </style>
</head>
<body>
    <div class="actions">
        <a href="{{ route('reportes.gastos', ['mes' => $mes]) }}">Volver al reporte</a>
        <button type="button" onclick="window.print()">Imprimir / guardar PDF</button>
    </div>
    <header>
        <div>
            @if(is_file(public_path('images/logo-dis-dental.png')))
                <img class="logo" src="{{ asset('images/logo-dis-dental.png') }}" alt="Diseño Dental">
            @else
                <strong>Laboratorio Diseño Dental</strong>
            @endif
        </div>
        <div>
            <h1>Gastos mensuales</h1>
            <p>Caja chica y compras registradas en {{ $mesTexto }}.</p>
            <p class="muted">Generado: {{ now('America/Guatemala')->format('d/m/Y H:i') }} · {{ auth()->user()->name }}</p>
        </div>
    </header>
    <p class="summary">
        @foreach(['caja' => 'Caja chica vigente', 'materiales' => 'Compras de materiales', 'servicios' => 'Compras de servicios', 'total' => 'Total registrado del mes'] as $clave => $etiqueta)
            <span><strong>{{ $etiqueta }}:</strong> Q {{ number_format($resumen[$clave] / 100, 2) }}</span>
            @if(!$loop->last) · @endif
        @endforeach
    </p>
    <div class="filters">
        <strong>Mes:</strong> {{ $mesTexto }} · <strong>Registros:</strong> {{ $resumen['cantidad'] }}.
        Una factura con materiales y servicios aparece en dos subtotales.
    </div>
    <div class="table-scroll" role="region" aria-label="Detalle de gastos mensuales" tabindex="0">
        <table>
            <colgroup><col style="width:8%"><col style="width:10%"><col style="width:17%"><col style="width:16%"><col style="width:17%"><col style="width:10%"><col style="width:10%"><col style="width:12%"></colgroup>
            <thead>
                <tr>
                    <th scope="col">Fecha</th><th scope="col">Origen</th><th scope="col">Concepto</th><th scope="col">Beneficiario / proveedor</th>
                    <th scope="col">Documento / NIT</th><th scope="col">Método registrado</th><th scope="col" class="importe">Importe</th><th scope="col">Registrado por</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registros as $registro)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($registro->fecha)->format('d/m/Y') }}</td>
                        <td>{{ $registro->origen }}</td>
                        <td>{{ $registro->concepto }}</td>
                        <td>{{ $registro->beneficiario ?: '—' }}</td>
                        <td>{{ trim(($registro->serie ? $registro->serie.' / ' : '').($registro->documento ?? '')) ?: '—' }}<small>NIT: {{ $registro->nit ?: '—' }}</small></td>
                        <td>{{ $registro->metodo_pago }}</td>
                        <td class="importe">Q {{ number_format($registro->monto_centavos / 100, 2) }}</td>
                        <td>{{ $registro->usuario ?: 'Usuario no disponible' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8">No hay gastos ni compras vigentes registrados para este mes.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="notas muted">
        <p> </p>
        @if($pendientesCaja > 0)<p>Caja chica: {{ $pendientesCaja }} pendientes de autorización no incluidos en el total.</p>@endif
        <p></p>
        <p>  </p>
        <p></p>
        <p>Se imprimen todos los resultados del mes, incluidos los de otras páginas.</p>
    </div>
</body>
</html>
