<div class="cc-stats">
    @foreach(['limite' => 'Límite mensual', 'gastado' => 'Gastos registrados y autorizados', 'disponible' => 'Disponible del límite', 'pendiente' => 'Pendiente de autorización'] as $campo => $etiqueta)
        <div class="cc-card cc-padding">
            <p class="cc-stat-label">{{ $etiqueta }}</p>
            <p class="cc-stat-value">Q {{ $dinero($resumen[$campo]) }}</p>
            @if($campo === 'pendiente')<p class="cc-help">{{ $resumen['cantidad_pendiente'] }} {{ $resumen['cantidad_pendiente'] === 1 ? 'solicitud' : 'solicitudes' }}</p>@endif
        </div>
    @endforeach
</div>
@if($resumen['exceso'] > 0)<p class="cc-notice cc-notice-warning">Exceso autorizado del mes: <strong>Q {{ $dinero($resumen['exceso']) }}</strong>. Cada gasto nuevo que supere el límite requiere su propia autorización.</p>@endif
