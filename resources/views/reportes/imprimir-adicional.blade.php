<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $datos['titulo'] }} | Diseño Dental</title>
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
        table { width:100%; border-collapse:collapse; font-size:11px; }
        th { background:#edf5f8; color:#365b7c; text-align:left; }
        th,td { border:1px solid #dce5ec; padding:8px; vertical-align:top; overflow-wrap:anywhere; }
        td small { display:block; color:#627386; margin-top:3px; }
        .filters span { display:inline-block; margin-right:14px; }
        .ra-importe { white-space:nowrap; }
        @page { size:A4 landscape; margin:12mm; }
        @media print { body { margin:0; } .actions { display:none; } thead { display:table-header-group; } tr { break-inside:avoid; } }
    </style>
</head>
<body>
    <div class="actions">
        <a href="{{ route('reportes.'.$reporte, $datos['filtros']) }}">Volver al reporte</a>
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
            <h1>{{ $datos['titulo'] }}</h1>
            <p>{{ $datos['subtitulo'] }}</p>
            <p class="muted">Generado: {{ now('America/Guatemala')->format('d/m/Y H:i') }} · {{ auth()->user()->name }}</p>
        </div>
    </header>
    <p class="summary">
        @foreach($datos['metricas'] as $metrica)
            <span><strong>{{ $metrica['etiqueta'] }}:</strong> @include('reportes.partials.valor-adicional', ['valor' => $metrica['valor'], 'formato' => $metrica['formato']])</span>
            @if(!$loop->last) · @endif
        @endforeach
    </p>
    <div class="filters">@include('reportes.partials.filtros-adicional')</div>
    @include('reportes.partials.tabla-adicional')
    @foreach($datos['notas'] as $nota)<p class="muted">{{ $nota }}</p>@endforeach
    <p class="muted">Se imprimen todos los resultados filtrados, incluidos los de otras páginas.</p>
</body>
</html>
