@extends('layouts.app')

@section('title', 'Panel de control | Laboratorio Dental')

@section('content')
    @include('partials.estilos-dashboard')

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