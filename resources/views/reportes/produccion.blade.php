@extends('layouts.app')
@section('title', 'Reporte de producción | Laboratorio Dental')
@section('content')
@include('reportes.partials.estilos')
<div class="reportes-lab">
    <header class="rl-header">
        <div>
            <p class="rl-eyebrow">Reportes del laboratorio</p>
            <h1>Producción y trazabilidad</h1>
            <p class="rl-note">Órdenes ingresadas en el período seleccionado y su situación actual.</p>
        </div>
        <a href="{{ route('reportes.index') }}" class="rl-button rl-secondary">Volver a reportes</a>
    </header>

    @if($errors->any())
        <div class="rl-errors" role="alert">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="GET" action="{{ route('reportes.produccion') }}" class="rl-panel rl-padding">
        <div class="rl-filters">
            <div class="rl-field">
                <label for="desde">Ingreso desde</label>
                <input id="desde" name="desde" type="date" value="{{ $filtros['desde'] }}" required>
            </div>
            <div class="rl-field">
                <label for="hasta">Ingreso hasta</label>
                <input id="hasta" name="hasta" type="date" value="{{ $filtros['hasta'] }}" required>
            </div>
            <div class="rl-field">
                <label for="area_trabajo">Área de trabajo</label>
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
            <div class="rl-field">
                <label for="odontologo_id">Odontólogo</label>
                <select id="odontologo_id" name="odontologo_id">
                    <option value="">Todos los odontólogos</option>
                    @foreach($odontologos as $doctor)
                        <option value="{{ $doctor->id }}" @selected((string) ($filtros['odontologo_id'] ?? '') === (string) $doctor->id)>{{ $doctor->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="rl-field">
                <label for="clinica_id">Clínica</label>
                <select id="clinica_id" name="clinica_id">
                    <option value="">Todas las clínicas</option>
                    @foreach($clinicas as $clinica)
                        <option value="{{ $clinica->id }}" @selected((string) ($filtros['clinica_id'] ?? '') === (string) $clinica->id)>{{ $clinica->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="rl-field">
                <label for="estado_orden_id">Estado actual</label>
                <select id="estado_orden_id" name="estado_orden_id">
                    <option value="">Todos los estados</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado->id }}" @selected((string) ($filtros['estado_orden_id'] ?? '') === (string) $estado->id)>{{ $estado->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="rl-field">
                <label for="etapa_produccion_id">Etapa en el historial</label>
                <select id="etapa_produccion_id" name="etapa_produccion_id">
                    <option value="">Todas las etapas</option>
                    @foreach($etapas as $etapa)
                        <option value="{{ $etapa->id }}" @selected((string) ($filtros['etapa_produccion_id'] ?? '') === (string) $etapa->id)>{{ $etapa->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="rl-field">
                <label for="tecnico_id">Técnico en el historial</label>
                <select id="tecnico_id" name="tecnico_id">
                    <option value="">Todos los técnicos</option>
                    @foreach($tecnicos as $tecnico)
                        <option value="{{ $tecnico->id }}" @selected((string) ($filtros['tecnico_id'] ?? '') === (string) $tecnico->id)>{{ $tecnico->user?->name ?? 'Técnico #'.$tecnico->id }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="rl-filter-footer">
            <p class="rl-note">Técnico y etapa buscan participación en un mismo registro del historial.</p>
            <div class="rl-actions">
                <a href="{{ route('reportes.produccion') }}" class="rl-button rl-secondary">Limpiar filtros</a>
                <button type="submit" class="rl-button">Consultar</button>
            </div>
        </div>
    </form>

    <div class="rl-metrics">
        @foreach([
            'Órdenes del período' => 'ordenes',
            'Cantidad solicitada' => 'cantidad',
            'Órdenes con historial' => 'con_historial',
            'Actualmente en proceso' => 'en_proceso',
            'Actualmente terminadas' => 'terminadas',
            'Actualmente entregadas' => 'entregadas',
        ] as $etiqueta => $clave)
            <article class="rl-metric"><span>{{ $etiqueta }}</span><strong>{{ $resumen[$clave] }}</strong></article>
        @endforeach
    </div>

    <section class="rl-panel">
        <div class="rl-section-header">
            <div>
                <h2>Órdenes encontradas</h2>
                <p class="rl-note">El resumen, la exportación y la impresión usan los mismos filtros.</p>
            </div>
            <div class="rl-actions">
                <a href="{{ route('reportes.produccion.exportar', $filtros) }}" class="rl-button rl-secondary">Exportar CSV</a>
                <a href="{{ route('reportes.produccion.imprimir', $filtros) }}" class="rl-button" target="_blank" rel="noopener">Imprimir / guardar PDF</a>
            </div>
        </div>
        @if($ordenes->count() > 0)
            <div class="rl-table-scroll" role="region" aria-label="Órdenes del reporte" tabindex="0">
                <table>
                    <thead><tr>
                        <th scope="col">Orden / ingreso</th><th scope="col">Paciente / odontólogo</th>
                        <th scope="col">Área / prótesis</th><th scope="col">Cantidad</th>
                        <th scope="col">Estado actual</th><th scope="col">Etapa / técnico actuales</th>
                        <th scope="col">Historial</th>
                    </tr></thead>
                    <tbody>
                        @foreach($ordenes as $orden)
                            <tr>
                                <td><a href="{{ route('ordenes.show', $orden) }}" class="rl-link">{{ $orden->codigo_area ?: $orden->codigo }}</a><p class="rl-note">{{ $orden->fecha_ingreso?->format('d/m/Y') ?? 'Sin fecha' }}</p></td>
                                <td>{{ trim(($orden->paciente?->nombre ?? '').' '.($orden->paciente?->apellido ?? '')) ?: 'Sin paciente' }}<p class="rl-note">{{ $orden->odontologo?->nombre ?? 'Sin odontólogo' }}</p></td>
                                <td>{{ $areas[$orden->area_trabajo] ?? 'Área por revisar' }}<p class="rl-note">{{ $orden->tipoProtesis?->nombre ?? 'Sin tipo de prótesis' }}</p></td>
                                <td>{{ $orden->cantidad }}</td>
                                <td><span class="rl-badge">{{ $orden->estadoOrden?->nombre ?? 'Sin estado' }}</span></td>
                                <td>{{ $orden->etapaActual?->nombre ?? 'Sin etapa asignada' }}<p class="rl-note">{{ $orden->tecnicoActual?->user?->name ?? 'Sin técnico asignado actualmente' }}</p></td>
                                <td><a href="{{ route('reportes.trazabilidad', array_merge(['orden' => $orden->id], $filtros)) }}" class="rl-link">Ver trazabilidad</a><p class="rl-note">{{ $orden->historial_produccion_count }} registros</p></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="rl-padding">{{ $ordenes->onEachSide(1)->links() }}</div>
        @else
            <p class="rl-empty">No hay órdenes en esta página con los filtros seleccionados. Puede ampliar el período o limpiar los filtros.</p>
        @endif
    </section>
    <p class="rl-note">“Con historial” indica que existe al menos un registro de producción. Los estados mostrados corresponden a la situación actual de cada orden.</p>
</div>
@endsection
