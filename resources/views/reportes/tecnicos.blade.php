@extends('layouts.app')
@section('title', 'Producción por técnico | Diseño Dental')
@section('content')
@include('reportes.partials.estilos')
<div class="reportes-lab">
    <header class="rl-header">
        <div>
            <p class="rl-eyebrow">Reportes del laboratorio</p>
            <h1>Producción por técnico</h1>
            <p class="rl-note">Participación registrada en las etapas iniciadas durante el período seleccionado.</p>
        </div>
        <a href="{{ route('reportes.index') }}" class="rl-button rl-secondary">Volver a reportes</a>
    </header>
    @if($errors->any())
        <div class="rl-errors" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <form method="GET" action="{{ route('reportes.tecnicos') }}" class="rl-panel rl-padding">
        <div class="rl-filters">
            <div class="rl-field">
                <label for="desde">Inicio de etapa desde</label>
                <input id="desde" name="desde" type="date" value="{{ $filtros['desde'] }}" required>
            </div>
            <div class="rl-field">
                <label for="hasta">Inicio de etapa hasta</label>
                <input id="hasta" name="hasta" type="date" value="{{ $filtros['hasta'] }}" required>
            </div>
            <div class="rl-field">
                <label for="tecnico_id">Técnico del historial</label>
                <select id="tecnico_id" name="tecnico_id">
                    <option value="">Todos los técnicos</option>
                    @foreach($tecnicos as $tecnico)
                        <option value="{{ $tecnico->id }}" @selected((string) ($filtros['tecnico_id'] ?? '') === (string) $tecnico->id)>{{ $tecnico->user?->name ?? 'Técnico #'.$tecnico->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="rl-field">
                <label for="etapa_produccion_id">Etapa realizada</label>
                <select id="etapa_produccion_id" name="etapa_produccion_id">
                    <option value="">Todas las etapas</option>
                    @foreach($etapas as $etapa)
                        <option value="{{ $etapa->id }}" @selected((string) ($filtros['etapa_produccion_id'] ?? '') === (string) $etapa->id)>{{ $etapa->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="rl-field">
                <label for="area_trabajo">Área de la orden</label>
                <select id="area_trabajo" name="area_trabajo">
                    <option value="">Todas las áreas</option>
                    @foreach($areas as $valor => $nombre)
                        <option value="{{ $valor }}" @selected(($filtros['area_trabajo'] ?? '') === $valor)>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="rl-field">
                <label for="tipo_protesis_id">Tipo de prótesis</label>
                <select id="tipo_protesis_id" name="tipo_protesis_id">
                    <option value="">Todos los tipos</option>
                    @foreach($tiposProtesis as $tipo)
                        <option value="{{ $tipo->id }}" @selected((string) ($filtros['tipo_protesis_id'] ?? '') === (string) $tipo->id)>{{ $tipo->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="rl-filter-footer">
            <p class="rl-note">Se conservan las participaciones de técnicos desactivados.</p>
            <div class="rl-actions">
                <a href="{{ route('reportes.tecnicos') }}" class="rl-button rl-secondary">Limpiar filtros</a>
                <button type="submit" class="rl-button">Consultar</button>
            </div>
        </div>
    </form>

    <div class="rl-metrics">
        @foreach(['Órdenes distintas' => 'ordenes', 'Registros de etapas iniciadas' => 'registros', 'Registros con fecha de cierre' => 'cerradas'] as $etiqueta => $clave)
            <article class="rl-metric"><span>{{ $etiqueta }}</span><strong>{{ $resumen[$clave] }}</strong></article>
        @endforeach
    </div>

    <section class="rl-panel">
        <div class="rl-section-header">
            <div><h2>Participación por técnico</h2><p class="rl-note">Resumen de todos los registros que coinciden con los filtros.</p></div>
            <div class="rl-actions">
                <a href="{{ route('reportes.tecnicos.exportar', $filtros) }}" class="rl-button rl-secondary">Exportar detalle CSV</a>
                <a href="{{ route('reportes.tecnicos.imprimir', $filtros) }}" class="rl-button" target="_blank" rel="noopener">Imprimir / guardar PDF</a>
            </div>
        </div>
        <div class="rl-table-scroll" role="region" aria-label="Resumen por técnico" tabindex="0">
            @include('reportes.partials.resumen-tecnicos')
        </div>
        <p class="rl-note rl-padding">Una orden cuenta una vez para cada técnico participante. El total general cuenta órdenes distintas; no es la suma de las órdenes por técnico.</p>
    </section>

    <section class="rl-panel">
        <div class="rl-section-header"><div><h2>Detalle de etapas</h2><p class="rl-note">{{ $historiales->total() }} registros encontrados · 15 por página.</p></div></div>
        <div class="rl-table-scroll" role="region" aria-label="Etapas registradas por técnico" tabindex="0">
            @include('reportes.partials.detalle-tecnicos', ['paraImprimir' => false])
        </div>
        @if($historiales->hasPages())
            <div class="rl-padding">{{ $historiales->onEachSide(1)->links() }}</div>
        @endif
    </section>
    <p class="rl-note">El período filtra la fecha de inicio de cada etapa. Su cierre puede ocurrir después del período. “Sin fecha de cierre” no implica que el técnico esté trabajando actualmente. Área y prótesis corresponden a los datos actuales de la orden.</p>
</div>
@endsection
