<style>
    .reportes-lab.gm-report .rl-metrics { grid-template-columns:repeat(4,minmax(0,1fr)); }
    .reportes-lab.gm-report td { max-width:240px; overflow-wrap:anywhere; }
    @media(max-width:900px) { .reportes-lab.gm-report .rl-metrics { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media(max-width:520px) { .reportes-lab.gm-report .rl-metrics { grid-template-columns:1fr; } }
</style>
<div class="reportes-lab gm-report">
    <header class="rl-header">
        <div>
            <p class="rl-eyebrow">Diseño Dental · Reportes</p>
            <h1>Gastos mensuales</h1>
            <p class="rl-note">Caja chica y compras registradas en {{ $mesTexto }}.</p>
        </div>
        @unless($imprimir)
            <a href="{{ route('reportes.gastos.imprimir', ['mes' => $mes]) }}" class="rl-button" target="_blank" rel="noopener">Imprimir / guardar PDF</a>
        @endunless
    </header>

    @unless($imprimir)
        @if($errors->any())
            <div class="rl-errors" role="alert">{{ $errors->first() }}</div>
        @endif
        <form method="GET" action="{{ route('reportes.gastos') }}" class="rl-panel rl-padding">
            <div class="rl-filters">
                <div class="rl-field">
                    <label for="mes">Mes del reporte</label>
                    <input type="month" id="mes" name="mes" value="{{ $mes }}" required>
                </div>
            </div>
            <div class="rl-filter-footer">
                <p class="rl-note">Los totales incluyen todo el mes, aunque el detalle tenga varias páginas.</p>
                <div class="rl-actions">
                    <a href="{{ route('reportes.gastos') }}" class="rl-button rl-secondary">Mes actual</a>
                    <button type="submit" class="rl-button">Consultar</button>
                </div>
            </div>
        </form>
    @endunless

    <div class="rl-metrics">
        @foreach(['caja' => 'Caja chica vigente', 'materiales' => 'Compras de materiales', 'servicios' => 'Compras de servicios', 'total' => 'Total registrado del mes'] as $clave => $etiqueta)
            <article class="rl-metric">
                <span>{{ $etiqueta }}</span>
                <strong>Q {{ number_format($resumen[$clave] / 100, 2) }}</strong>
            </article>
        @endforeach
    </div>

    <section class="rl-panel">
        <div class="rl-section-header">
            <div>
                <h2>Detalle por origen</h2>
                <p class="rl-note">{{ $resumen['cantidad'] }} registros. Una factura con materiales y servicios aparece en dos subtotales.</p>
            </div>
        </div>
        <div class="rl-table-scroll" role="region" aria-label="Gastos mensuales por origen" tabindex="0">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th><th>Origen</th><th>Concepto</th><th>Beneficiario / proveedor</th>
                        <th>Documento / NIT</th><th>Método registrado</th><th>Importe</th><th>Registrado por</th>
                        @unless($imprimir)<th>Detalle</th>@endunless
                    </tr>
                </thead>
                <tbody>
                    @forelse($registros as $registro)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($registro->fecha)->format('d/m/Y') }}</td>
                            <td>{{ $registro->origen }}</td>
                            <td>{{ $registro->concepto }}</td>
                            <td>{{ $registro->beneficiario ?: '—' }}</td>
                            <td>{{ trim(($registro->serie ? $registro->serie.' / ' : '').($registro->documento ?? '')) ?: '—' }}<br>NIT: {{ $registro->nit ?: '—' }}</td>
                            <td>{{ $registro->metodo_pago }}</td>
                            <td style="white-space:nowrap">Q {{ number_format($registro->monto_centavos / 100, 2) }}</td>
                            <td>{{ $registro->usuario ?: 'Usuario no disponible' }}</td>
                            @unless($imprimir)
                                <td><a href="{{ $registro->origen === 'Caja chica' ? route('caja-chica.show', $registro->registro_id) : route('compras.show', $registro->registro_id) }}" class="rl-button rl-secondary">Ver</a></td>
                            @endunless
                        </tr>
                    @empty
                        <tr><td colspan="{{ $imprimir ? 8 : 9 }}">No hay gastos ni compras vigentes registrados para este mes.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @unless($imprimir)
            @if($registros->hasPages())<div class="rl-padding">{{ $registros->onEachSide(1)->links() }}</div>@endif
        @endunless
    </section>

    <div class="rl-panel rl-padding" style="margin-top:18px">
        <p class="rl-note"></p>
        @if($pendientesCaja > 0)<p class="rl-note">Caja chica: {{ $pendientesCaja }} pendientes de autorización no incluidos en el total.</p>@endif
        <p class="rl-note">El movimiento de inventario por recibir una compra no vuelve a sumarse como gasto. Las solicitudes de corrección pendientes conservan el importe vigente hasta su aprobación.</p>
    </div>
</div>
