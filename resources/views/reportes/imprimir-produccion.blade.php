<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de producción | Diseño Dental</title>
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
        @page { size:A4 landscape; margin:12mm; }
        @media print { body { margin:0; } .actions { display:none; } thead { display:table-header-group; } tr { break-inside:avoid; } }
    </style>
</head>
<body>
    <div class="actions">
        <a href="{{ route('reportes.produccion', $filtros) }}">Volver al reporte</a>
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
            <h1>Reporte de producción</h1>
            <p>Órdenes ingresadas del {{ \Carbon\Carbon::parse($filtros['desde'])->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($filtros['hasta'])->format('d/m/Y') }}</p>
            <p class="muted">Generado: {{ now('America/Guatemala')->format('d/m/Y H:i') }} · {{ auth()->user()->name }}</p>
        </div>
    </header>
    <p class="summary">
        <strong>Órdenes:</strong> {{ $resumen['ordenes'] }} ·
        <strong>Cantidad solicitada:</strong> {{ $resumen['cantidad'] }} ·
        <strong>Con historial:</strong> {{ $resumen['con_historial'] }} ·
        <strong>En proceso:</strong> {{ $resumen['en_proceso'] }} ·
        <strong>Terminadas:</strong> {{ $resumen['terminadas'] }} ·
        <strong>Entregadas:</strong> {{ $resumen['entregadas'] }}
    </p>
    <div class="filters">
        <strong>Filtros:</strong> {{ count($descripcionFiltros) > 0 ? implode(' · ', $descripcionFiltros) : 'Todas las áreas, tipos, estados, etapas y técnicos.' }}
        <p class="muted">Los estados corresponden a la situación actual. Técnico y etapa filtran participación en el historial. Se imprimen todos los resultados del período.</p>
    </div>
    <table>
        <thead><tr><th>Orden / ingreso</th><th>Paciente / odontólogo</th><th>Área / prótesis</th><th>Cantidad</th><th>Estado actual</th><th>Etapa / técnico actuales</th><th>Historial</th></tr></thead>
        <tbody>
            @forelse($ordenes as $orden)
                <tr>
                    <td>{{ $orden->codigo_area ?: $orden->codigo }}<small>{{ $orden->fecha_ingreso?->format('d/m/Y') }}</small></td>
                    <td>{{ trim(($orden->paciente?->nombre ?? '').' '.($orden->paciente?->apellido ?? '')) ?: 'Sin paciente' }}<small>{{ $orden->odontologo?->nombre ?? 'Sin odontólogo' }}</small></td>
                    <td>{{ $areas[$orden->area_trabajo] ?? 'Área por revisar' }}<small>{{ $orden->tipoProtesis?->nombre ?? 'Sin tipo' }}</small></td>
                    <td>{{ $orden->cantidad }}</td>
                    <td>{{ $orden->estadoOrden?->nombre ?? 'Sin estado' }}</td>
                    <td>{{ $orden->etapaActual?->nombre ?? 'Sin etapa asignada' }}<small>{{ $orden->tecnicoActual?->user?->name ?? 'Sin técnico asignado actualmente' }}</small></td>
                    <td>{{ $orden->historial_produccion_count }} registros</td>
                </tr>
            @empty
                <tr><td colspan="7">No hay órdenes con los filtros seleccionados.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
