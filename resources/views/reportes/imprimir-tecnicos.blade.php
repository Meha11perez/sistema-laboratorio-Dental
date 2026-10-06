<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producción por técnico | Diseño Dental</title>
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
        h2 { font-size:16px; margin:18px 0 10px; }
        .rl-note { font-size:10px; color:#627386; margin:4px 0; }
        @page { size:A4 landscape; margin:12mm; }
        @media print { body { margin:0; } .actions { display:none; } thead { display:table-header-group; } tr { break-inside:avoid; } }
    </style>
</head>
<body>
    <div class="actions">
        <a href="{{ route('reportes.tecnicos', $filtros) }}">Volver al reporte</a>
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
            <h1>Producción por técnico</h1>
            <p>Etapas iniciadas del {{ \Carbon\Carbon::parse($filtros['desde'])->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($filtros['hasta'])->format('d/m/Y') }}</p>
            <p class="muted">Generado: {{ now('America/Guatemala')->format('d/m/Y H:i') }} · {{ auth()->user()->name }}</p>
        </div>
    </header>
    <p class="summary">
        <strong>Órdenes distintas:</strong> {{ $resumen['ordenes'] }} ·
        <strong>Registros de etapas:</strong> {{ $resumen['registros'] }} ·
        <strong>Con fecha de cierre:</strong> {{ $resumen['cerradas'] }}
    </p>
    <div class="filters">
        <strong>Filtros:</strong> {{ count($descripcionFiltros) ? implode(' · ', $descripcionFiltros) : 'Todos los técnicos, etapas, áreas y tipos de prótesis.' }}
        <p class="muted">El período corresponde al inicio de cada etapa. Se incluyen todos los resultados filtrados.</p>
    </div>
    <h2>Participación por técnico</h2>
    @include('reportes.partials.resumen-tecnicos')
    <p class="muted">Una orden cuenta una vez por técnico participante. El total general de órdenes distintas no es la suma de las filas.</p>
    <h2>Detalle de etapas</h2>
    @include('reportes.partials.detalle-tecnicos', ['paraImprimir' => true])
    <p class="muted">La fecha de cierre puede ser posterior al período. Un registro sin cierre no demuestra actividad actual. Área y prótesis corresponden a los datos actuales de la orden.</p>
</body>
</html>
