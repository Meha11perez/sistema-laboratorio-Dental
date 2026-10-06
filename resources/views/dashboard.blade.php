@extends('layouts.app')

@section('title', 'Panel de control | Laboratorio Dental')

@section('content')
<style>
    .dental-dashboard {
        --dd-blue: #365b7c;
        --dd-blue-dark: #294b69;
        --dd-cyan: #278596;
        --dd-text: #253649;
        --dd-muted: #627386;
        --dd-border: #dce5ec;
        max-width: 1280px;
        margin: 0 auto;
        color: var(--dd-text);
        font-family: 'Segoe UI', system-ui, -apple-system, Arial, sans-serif;
    }

    .dental-dashboard * { box-sizing: border-box; }

    .dental-dashboard h1,
    .dental-dashboard h2,
    .dental-dashboard h3,
    .dental-dashboard p { margin: 0; }

    .dental-dashboard a { text-decoration: none; }

    .dental-dashboard a:focus-visible {
        outline: 3px solid #71c5d2;
        outline-offset: 4px;
    }

    .dental-dashboard .dd-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 22px;
    }

    .dental-dashboard .dd-eyebrow {
        color: var(--dd-cyan);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .dental-dashboard h1 {
        font-size: 28px;
        font-weight: 600;
        line-height: 1.3;
        margin: 5px 0;
    }

    .dental-dashboard .dd-subtitle {
        color: var(--dd-muted);
        font-size: 14px;
        line-height: 1.6;
    }

    .dental-dashboard .dd-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .dental-dashboard .dd-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 10px 16px;
        border: 1px solid var(--dd-blue);
        border-radius: 9px;
        background: var(--dd-blue);
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        transition: background .15s;
    }

    .dental-dashboard .dd-button:hover {
        background: var(--dd-blue-dark);
    }

    .dental-dashboard .dd-button-secondary {
        background: #fff;
        color: var(--dd-blue);
        border-color: var(--dd-border);
    }

    .dental-dashboard .dd-button-secondary:hover {
        background: #eef5f9;
    }

    .dental-dashboard .dd-metrics {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .dental-dashboard .dd-metric {
        padding: 18px 20px;
        background: #fff;
        border: 1px solid var(--dd-border);
        border-top: 3px solid #82a7bf;
        border-radius: 12px;
    }

    .dental-dashboard .dd-metric-cyan {
        border-top-color: #75b8c4;
    }

    .dental-dashboard .dd-metric-alert {
        border-top-color: #c79044;
    }

    .dental-dashboard .dd-label {
        color: var(--dd-muted);
        font-size: 12px;
        font-weight: 600;
    }

    .dental-dashboard .dd-number {
        display: block;
        margin: 6px 0;
        color: var(--dd-blue);
        font-size: 32px;
        font-weight: 600;
        line-height: 1.2;
    }

    .dental-dashboard .dd-metric-alert .dd-number {
        color: #946020;
    }

    .dental-dashboard .dd-note {
        color: var(--dd-muted);
        font-size: 12px;
        line-height: 1.5;
    }

    .dental-dashboard .dd-shortcuts {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }

    .dental-dashboard .dd-shortcut {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 15px 17px;
        background: #eef5f9;
        border: 1px solid #d9e6ee;
        border-radius: 10px;
        color: var(--dd-blue);
        font-size: 13px;
        font-weight: 600;
    }

    .dental-dashboard .dd-shortcut:hover {
        background: #e3eff5;
    }

    .dental-dashboard .dd-columns {
        display: grid;
        grid-template-columns: minmax(0, 1.25fr) minmax(0, 1fr);
        align-items: start;
        gap: 18px;
        margin-bottom: 20px;
    }

    .dental-dashboard .dd-panel {
        background: #fff;
        border: 1px solid var(--dd-border);
        border-radius: 12px;
        overflow: hidden;
    }

    .dental-dashboard .dd-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--dd-border);
    }

    .dental-dashboard h2 {
        font-size: 16px;
        font-weight: 600;
        line-height: 1.4;
    }

    .dental-dashboard .dd-panel-header .dd-note {
        margin-top: 4px;
    }

    .dental-dashboard .dd-count {
        flex-shrink: 0;
        padding: 5px 10px;
        border-radius: 7px;
        background: #edf5f8;
        color: var(--dd-blue);
        font-size: 12px;
        font-weight: 600;
    }

    .dental-dashboard .dd-areas {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        padding: 18px 20px;
    }

    .dental-dashboard .dd-area {
        border: 1px solid var(--dd-border);
        border-radius: 10px;
        padding: 14px;
    }

    .dental-dashboard .dd-area-heading {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 10px;
    }

    .dental-dashboard .dd-area-code {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 33px;
        height: 30px;
        flex-shrink: 0;
        border-radius: 6px;
        background: #eaf4f7;
        color: var(--dd-cyan);
        font-size: 11px;
        font-weight: 700;
    }

    .dental-dashboard h3 {
        font-size: 13px;
        font-weight: 600;
    }

    .dental-dashboard .dd-area-total {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 8px;
    }

    .dental-dashboard .dd-area-total strong {
        color: var(--dd-blue);
        font-size: 26px;
        font-weight: 600;
    }

    .dental-dashboard progress {
        display: block;
        width: 100%;
        height: 6px;
        border: 0;
        border-radius: 4px;
        overflow: hidden;
        background: #edf2f6;
        accent-color: #5b9eaf;
    }

    .dental-dashboard progress::-webkit-progress-bar {
        background: #edf2f6;
        border-radius: 4px;
    }

    .dental-dashboard progress::-webkit-progress-value {
        background: #5b9eaf;
        border-radius: 4px;
    }

    .dental-dashboard progress::-moz-progress-bar {
        background: #5b9eaf;
        border-radius: 4px;
    }

    .dental-dashboard .dd-area-warning {
        padding: 0 20px 18px;
        color: #946020;
        font-size: 12px;
        line-height: 1.5;
    }

    .dental-dashboard .dd-stages {
        list-style: none;
        margin: 0;
        padding: 4px 20px;
    }

    .dental-dashboard .dd-stages li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 13px 0;
        border-bottom: 1px solid #edf2f6;
        font-size: 13px;
    }

    .dental-dashboard .dd-stages li:last-child {
        border-bottom: 0;
    }

    .dental-dashboard .dd-link {
        color: var(--dd-blue);
        font-size: 13px;
        font-weight: 600;
    }

    .dental-dashboard .dd-link:hover {
        text-decoration: underline;
    }

    .dental-dashboard .dd-table-scroll {
        overflow-x: auto;
    }

    .dental-dashboard table {
        width: 100%;
        min-width: 860px;
        border-collapse: collapse;
        text-align: left;
    }

    .dental-dashboard th {
        padding: 12px 16px;
        background: #f5f8fa;
        color: var(--dd-muted);
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .dental-dashboard td {
        padding: 14px 16px;
        border-top: 1px solid #edf2f6;
        font-size: 12px;
        vertical-align: top;
    }

    .dental-dashboard td .dd-note {
        margin-top: 3px;
    }

    .dental-dashboard tbody tr:hover {
        background: #fafcfd;
    }

    .dental-dashboard .dd-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 6px;
        background: #edf5f8;
        color: var(--dd-blue);
        font-size: 11px;
        white-space: nowrap;
    }

    .dental-dashboard .dd-overdue {
        display: block;
        margin-top: 4px;
        color: #946020;
        font-size: 11px;
        font-weight: 600;
    }

    .dental-dashboard .dd-empty {
        padding: 25px 20px;
        color: var(--dd-muted);
        font-size: 13px;
        line-height: 1.6;
    }

    .dental-dashboard .dd-table-footer {
        padding: 12px 20px;
        border-top: 1px solid var(--dd-border);
        background: #fafcfd;
    }

    .dental-dashboard .dd-footer {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 18px;
        color: var(--dd-muted);
        font-size: 12px;
    }

    @media (max-width: 900px) {
        .dental-dashboard .dd-columns {
            grid-template-columns: 1fr;
        }

        .dental-dashboard .dd-shortcuts {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .dental-dashboard .dd-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 14px;
        }

        .dental-dashboard h1 {
            font-size: 24px;
        }

        .dental-dashboard .dd-metrics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .dental-dashboard .dd-metric {
            padding: 14px;
        }

        .dental-dashboard .dd-number {
            font-size: 28px;
        }

        .dental-dashboard .dd-panel-header {
            flex-wrap: wrap;
        }

        .dental-dashboard .dd-areas {
            padding: 14px;
            gap: 10px;
        }

        .dental-dashboard .dd-area {
            padding: 11px;
        }
    }

    @media (max-width: 380px) {
        .dental-dashboard .dd-metrics,
        .dental-dashboard .dd-areas,
        .dental-dashboard .dd-shortcuts {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="dental-dashboard">

    {{-- ENCABEZADO --}}
    <header class="dd-header">
        <div>
            <p class="dd-eyebrow">
                Diseño Dental · {{ $fechaTexto }}
            </p>

            <h1>Panel de control</h1>

            <p class="dd-subtitle">
                Producción, entregas y seguimiento del laboratorio.
            </p>
        </div>

        <div class="dd-actions">
            <a
                href="{{ route('agenda.index', ['fecha' => $fechaHoy]) }}"
                class="dd-button dd-button-secondary"
            >
                Ver agenda
            </a>

            @if($puedeGestionarOrdenes)
                <a
                    href="{{ route('ordenes.create') }}"
                    class="dd-button"
                >
                    + Nueva orden
                </a>
            @endif
        </div>
    </header>

    {{-- INDICADORES --}}
    <div class="dd-metrics">

        <article class="dd-metric dd-metric-cyan">
            <p class="dd-label">Programadas hoy</p>

            <strong class="dd-number">{{ $trabajosHoy }}</strong>

            <p class="dd-note">
                {{ $porEntregarHoy }} pendientes de entrega hoy
            </p>
        </article>

        <article class="dd-metric">
            <p class="dd-label">Órdenes pendientes</p>

            <strong class="dd-number">{{ $ordenesPendientes }}</strong>

            <p class="dd-note">
                Pendientes de iniciar o continuar
            </p>
        </article>

        <article class="dd-metric dd-metric-cyan">
            <p class="dd-label">En producción</p>

            <strong class="dd-number">{{ $ordenesEnProceso }}</strong>

            <p class="dd-note">
                Trabajos actualmente en proceso
            </p>
        </article>

        <article class="dd-metric">
            <p class="dd-label">Terminadas</p>

            <strong class="dd-number">{{ $ordenesTerminadas }}</strong>

            <p class="dd-note">
                Producción finalizada, pendientes de entrega
            </p>
        </article>

        <article class="dd-metric dd-metric-cyan">
            <p class="dd-label">Entregadas</p>

            <strong class="dd-number">{{ $ordenesEntregadas }}</strong>

            <p class="dd-note">
                Total de órdenes con estado Entregado
            </p>
        </article>

        <article class="dd-metric dd-metric-alert">
            <p class="dd-label">Entregas atrasadas</p>

            <strong class="dd-number">{{ $ordenesAtrasadas }}</strong>

            <p class="dd-note">
                Fecha vencida y todavía sin entregar
            </p>
        </article>

    </div>

    {{-- ACCESOS --}}
    <nav class="dd-shortcuts" aria-label="Accesos del laboratorio">
        <a
            href="{{ route('agenda.index', ['fecha' => $fechaHoy]) }}"
            class="dd-shortcut"
        >
            <span>Agenda de producción</span>
            <span aria-hidden="true">→</span>
        </a>

        <a href="{{ route('ordenes.index') }}" class="dd-shortcut">
            <span>Órdenes de trabajo</span>
            <span aria-hidden="true">→</span>
        </a>

        <a href="{{ route('produccion.index') }}" class="dd-shortcut">
            <span>Producción</span>
            <span aria-hidden="true">→</span>
        </a>

        <a href="{{ route('inventario.index') }}" class="dd-shortcut">
            <span>Inventario</span>
            <span aria-hidden="true">→</span>
        </a>
    </nav>

    {{-- ÁREAS Y ETAPAS --}}
    <div class="dd-columns">

        <section class="dd-panel" aria-labelledby="dd-areas-title">
            <div class="dd-panel-header">
                <div>
                    <h2 id="dd-areas-title">
                        Órdenes abiertas por área
                    </h2>

                    <p class="dd-note">
                        Incluye producción terminada pendiente de entrega.
                    </p>
                </div>

                <span class="dd-count">
                    {{ $totalActivas }} órdenes
                </span>
            </div>

            <div class="dd-areas">
                @foreach($resumenAreas as $area)
                    <article class="dd-area">
                        <div class="dd-area-heading">
                            <span class="dd-area-code">
                                {{ $area['codigo'] }}
                            </span>

                            <h3>{{ $area['nombre'] }}</h3>
                        </div>

                        <div class="dd-area-total">
                            <strong>{{ $area['total'] }}</strong>

                            <span class="dd-note">
                                {{ $area['porcentaje'] }}% del total
                            </span>
                        </div>

                        <progress
                            value="{{ $area['total'] }}"
                            max="{{ max(1, $totalActivas) }}"
                            aria-label="Órdenes abiertas de {{ $area['nombre'] }}"
                        ></progress>
                    </article>
                @endforeach
            </div>

            @if($ordenesSinArea > 0)
                <p class="dd-area-warning">
                    {{ $ordenesSinArea }} órdenes abiertas necesitan
                    revisar su área de trabajo.
                </p>
            @endif
        </section>

        <section class="dd-panel" aria-labelledby="dd-stages-title">
            <div class="dd-panel-header">
                <div>
                    <h2 id="dd-stages-title">Etapas actuales</h2>

                    <p class="dd-note">
                        Distribución de las órdenes abiertas.
                    </p>
                </div>

                <a href="{{ route('agenda.index') }}" class="dd-link">
                    Ver agenda
                </a>
            </div>

            @if($resumenEtapas->isNotEmpty())
                <ul class="dd-stages">
                    @foreach($resumenEtapas as $etapa)
                        <li>
                            <span>
                                {{ $etapa->etapaActual?->nombre ?? 'Sin etapa asignada' }}
                            </span>

                            <span class="dd-count">
                                {{ $etapa->total }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="dd-empty">
                    No hay órdenes abiertas para mostrar en las etapas.
                </p>
            @endif
        </section>

    </div>

    {{-- ENTREGAS --}}
    <section class="dd-panel" aria-labelledby="dd-deliveries-title">
        <div class="dd-panel-header">
            <div>
                <h2 id="dd-deliveries-title">
                    Entregas por atender
                </h2>

                <p class="dd-note">
                    {{ $porEntregarHoy }} para hoy ·
                    {{ $ordenesAtrasadas }} atrasadas
                </p>
            </div>

            <a href="{{ route('ordenes.index') }}" class="dd-link">
                Consultar órdenes
            </a>
        </div>

        @if($entregasPorAtender->isNotEmpty())
            <div
                class="dd-table-scroll"
                role="region"
                aria-label="Listado de entregas por atender"
                tabindex="0"
            >
                <table>
                    <thead>
                        <tr>
                            <th scope="col">Orden</th>
                            <th scope="col">Paciente / odontólogo</th>
                            <th scope="col">Área / etapa</th>
                            <th scope="col">Entrega estimada</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Acción</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($entregasPorAtender as $orden)
                            @php
                                $fechaEntrega = \Carbon\Carbon::parse(
                                    $orden->fecha_entrega_estimada
                                );

                                $estaAtrasada =
                                    $fechaEntrega->toDateString() < $fechaHoy;

                                $nombrePaciente = trim(
                                    ($orden->paciente?->nombre ?? '')
                                    .' '.
                                    ($orden->paciente?->apellido ?? '')
                                );
                            @endphp

                            <tr>
                                <td>
                                    <a
                                        class="dd-link"
                                        href="{{ route('ordenes.show', $orden) }}"
                                    >
                                        {{ $orden->codigo_area ?: $orden->codigo }}
                                    </a>
                                </td>

                                <td>
                                    <p>
                                        {{ $nombrePaciente ?: 'Sin paciente registrado' }}
                                    </p>

                                    <p class="dd-note">
                                        {{ $orden->odontologo?->nombre ?? 'Sin odontólogo registrado' }}
                                    </p>
                                </td>

                                <td>
                                    <p>
                                        {{ $nombresAreas[$orden->area_trabajo] ?? 'Área por revisar' }}
                                    </p>

                                    <p class="dd-note">
                                        {{ $orden->etapaActual?->nombre ?? 'Sin etapa asignada' }}
                                    </p>
                                </td>

                                <td>
                                    {{ $fechaEntrega->format('d/m/Y') }}

                                    @if($estaAtrasada)
                                        <span class="dd-overdue">
                                            Atrasada
                                        </span>
                                    @else
                                        <span class="dd-note"> · Hoy</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="dd-badge">
                                        {{ $orden->estadoOrden?->nombre ?? 'Sin estado' }}
                                    </span>
                                </td>

                                <td>
                                    <a
                                        class="dd-link"
                                        href="{{ route('ordenes.show', $orden) }}"
                                        aria-label="Ver orden {{ $orden->codigo }}"
                                    >
                                        Ver orden
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p class="dd-note dd-table-footer">
                Se muestran {{ $entregasPorAtender->count() }}
                de {{ $porEntregarHoy + $ordenesAtrasadas }}
                entregas por atender, empezando por las fechas
                más antiguas.
            </p>
        @else
            <p class="dd-empty">
                No hay entregas pendientes para hoy ni órdenes
                con fecha de entrega vencida.
            </p>
        @endif
    </section>

    <footer class="dd-footer">
        <p>
            {{ $usuario->name }} · {{ $rol ?? 'Sin rol' }}
        </p>

        <p>Los indicadores cuentan órdenes de trabajo.</p>
    </footer>

</div>
@endsection