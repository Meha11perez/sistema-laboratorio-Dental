@extends('layouts.app')
@section('title', $datos['titulo'].' | Diseño Dental')
@section('content')
@include('reportes.partials.estilos')
<style>
    .reportes-lab .ra-table td { min-width:100px; max-width:260px; overflow-wrap:anywhere; }
    .reportes-lab .ra-table .ra-importe { white-space:nowrap; }
    .reportes-lab .ra-filtros-activos { color:#627386; font-size:12px; line-height:1.8; }
    .reportes-lab .ra-filtros-activos span { display:inline-block; margin-right:15px; }
    .reportes-lab .ra-notas { padding:17px 20px; border:1px solid #d6e5ec; border-radius:12px; background:#f2f8fa; }
    .reportes-lab .ra-notas p + p { margin-top:7px; }
</style>
<div class="reportes-lab">
    <header class="rl-header">
        <div>
            <p class="rl-eyebrow">Reportes del laboratorio</p>
            <h1>{{ $datos['titulo'] }}</h1>
            <p class="rl-note">{{ $datos['subtitulo'] }}</p>
        </div>
        <a href="{{ route('reportes.index') }}" class="rl-button rl-secondary">Volver a reportes</a>
    </header>
    @if($errors->any())
        <div class="rl-errors" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form method="GET" action="{{ route('reportes.'.$reporte) }}" class="rl-panel rl-padding">
        <div class="rl-filters">
            @foreach($datos['campos'] as $campo)
                <div class="rl-field">
                    <label for="{{ $campo['nombre'] }}">{{ $campo['etiqueta'] }}</label>
                    @if($campo['tipo'] === 'select')
                        <select id="{{ $campo['nombre'] }}" name="{{ $campo['nombre'] }}"
                            @if($campo['nombre'] === 'vista') data-consulta="{{ route('reportes.'.$reporte) }}" @endif>
                            @if($campo['todos'])<option value="">Todos</option>@endif
                            @foreach($campo['opciones'] as $valor => $etiqueta)
                                <option value="{{ $valor }}" @selected((string) ($datos['filtros'][$campo['nombre']] ?? '') === (string) $valor)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    @else
                        <input id="{{ $campo['nombre'] }}" name="{{ $campo['nombre'] }}" type="{{ $campo['tipo'] }}" value="{{ $datos['filtros'][$campo['nombre']] ?? '' }}" required>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="rl-filter-footer">
            <p class="rl-note">Los indicadores incluyen todos los resultados de la consulta.</p>
            <div class="rl-actions">
                <a href="{{ route('reportes.'.$reporte, isset($datos['filtros']['vista']) ? ['vista' => $datos['filtros']['vista']] : []) }}" class="rl-button rl-secondary">Limpiar filtros</a>
                <button type="submit" class="rl-button">Consultar</button>
            </div>
        </div>
    </form>

    <div class="rl-metrics">
        @foreach($datos['metricas'] as $metrica)
            <article class="rl-metric">
                <span>{{ $metrica['etiqueta'] }}</span>
                <strong>@include('reportes.partials.valor-adicional', ['valor' => $metrica['valor'], 'formato' => $metrica['formato']])</strong>
            </article>
        @endforeach
    </div>

    @if(!empty($datos['enlaces']))
        <div class="rl-actions" style="margin-bottom:18px">
            @foreach($datos['enlaces'] as $enlace)
                <a class="rl-button rl-secondary" href="{{ route($enlace['ruta'], $enlace['parametros']) }}">{{ $enlace['texto'] }}</a>
            @endforeach
        </div>
    @endif

    <section class="rl-panel">
        <div class="rl-section-header">
            <div><h2>Resultados de la consulta</h2><p class="rl-note">{{ $filas->total() }} resultados · 15 por página.</p></div>
            <div class="rl-actions">
                <a href="{{ route('reportes.'.$reporte.'.exportar', $datos['filtros']) }}" class="rl-button rl-secondary">Exportar CSV</a>
                <a href="{{ route('reportes.'.$reporte.'.imprimir', $datos['filtros']) }}" class="rl-button" target="_blank" rel="noopener">Imprimir / guardar PDF</a>
            </div>
        </div>
        <div class="rl-padding ra-filtros-activos">@include('reportes.partials.filtros-adicional')</div>
        <div class="rl-table-scroll" role="region" aria-label="{{ $datos['titulo'] }}" tabindex="0">
            @include('reportes.partials.tabla-adicional')
        </div>
        @if($filas->hasPages())<div class="rl-padding">{{ $filas->onEachSide(1)->links() }}</div>@endif
    </section>
    <div class="ra-notas">
        @foreach($datos['notas'] as $nota)<p class="rl-note">{{ $nota }}</p>@endforeach
    </div>
</div>
<script>
    document.querySelectorAll('select[data-consulta]').forEach(function (select) {
        select.addEventListener('change', function () {
            const destino = new URL(select.dataset.consulta, window.location.origin);
            destino.searchParams.set('vista', select.value);
            window.location.assign(destino.toString());
        });
    });
</script>
@endsection
