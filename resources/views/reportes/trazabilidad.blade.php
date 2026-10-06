@extends('layouts.app')
@section('title', 'Trazabilidad de producción | Laboratorio Dental')
@section('content')
@include('reportes.partials.estilos')
<div class="reportes-lab">
    <header class="rl-header">
        <div>
            <p class="rl-eyebrow">Historial de producción</p>
            <h1>Trazabilidad · {{ $orden->codigo_area ?: $orden->codigo }}</h1>
            <p class="rl-note">Todos los registros de esta orden, incluidos los de técnicos que ya no están activos.</p>
        </div>
        <a href="{{ route('reportes.produccion', $filtros) }}" class="rl-button rl-secondary">Volver al reporte</a>
    </header>
    <section class="rl-panel rl-padding">
        <dl class="rl-details">
            <div><dt>Paciente</dt><dd>{{ trim(($orden->paciente?->nombre ?? '').' '.($orden->paciente?->apellido ?? '')) ?: 'Sin paciente' }}</dd></div>
            <div><dt>Odontólogo</dt><dd>{{ $orden->odontologo?->nombre ?? 'Sin odontólogo' }}</dd></div>
            <div><dt>Área / prótesis</dt><dd>{{ $areas[$orden->area_trabajo] ?? 'Área por revisar' }} · {{ $orden->tipoProtesis?->nombre ?? 'Sin tipo' }}</dd></div>
            <div><dt>Estado actual</dt><dd>{{ $orden->estadoOrden?->nombre ?? 'Sin estado' }}</dd></div>
            <div><dt>Etapa actual</dt><dd>{{ $orden->etapaActual?->nombre ?? 'Sin etapa asignada' }}</dd></div>
            <div><dt>Técnico actual</dt><dd>{{ $orden->tecnicoActual?->user?->name ?? 'Sin técnico asignado actualmente' }}</dd></div>
        </dl>
    </section>
    <section class="rl-panel">
        <div class="rl-section-header">
            <div><h2>Recorrido de la orden</h2><p class="rl-note">{{ $historiales->count() }} registros de producción · horas de Guatemala</p></div>
            <a href="{{ route('ordenes.show', $orden) }}" class="rl-link">Ver orden completa</a>
        </div>
        @if($historiales->isNotEmpty())
            <div class="rl-table-scroll" role="region" aria-label="Historial de producción de la orden" tabindex="0">
                <table>
                    <thead><tr><th scope="col">Etapa</th><th scope="col">Técnico</th><th scope="col">Inicio</th><th scope="col">Fin</th><th scope="col">Tiempo transcurrido</th><th scope="col">Estado</th><th scope="col">Observaciones / registro</th></tr></thead>
                    <tbody>
                        @foreach($historiales as $historial)
                            <tr>
                                <td>{{ $historial->etapaProduccion?->nombre ?? 'Etapa no disponible' }}</td>
                                <td>{{ $historial->tecnico?->user?->name ?? 'Técnico no disponible' }}</td>
                                <td>{{ $historial->fecha_inicio?->copy()->timezone('America/Guatemala')->format('d/m/Y H:i') ?? 'Sin inicio registrado' }}</td>
                                <td>{{ $historial->fecha_fin?->copy()->timezone('America/Guatemala')->format('d/m/Y H:i') ?? 'Sin cierre registrado' }}</td>
                                <td>{{ $historial->minutos_transcurridos !== null ? number_format($historial->minutos_transcurridos, 2).' min' : 'No calculable' }}</td>
                                <td><span class="rl-badge">{{ $historial->estado ?? 'Sin estado' }}</span></td>
                                <td>{{ $historial->observaciones ?: 'Sin observaciones' }}<p class="rl-note">Registrado por: {{ $historial->usuarioRegistro?->name ?? 'Usuario no disponible' }}</p></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="rl-empty">Esta orden todavía no tiene registros en el historial de producción.</p>
        @endif
    </section>
    <p class="rl-note">El tiempo transcurrido se calcula entre las fechas de inicio y fin. Incluye descansos y períodos fuera de la jornada laboral. Las etapas sin cierre no tienen una duración calculada.</p>
</div>
@endsection
