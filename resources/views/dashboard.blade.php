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
                Resumen de {{ $mesTexto }}. Órdenes agrupadas por su fecha de ingreso.
            </p>
        </div>

    </header>

    <style>
        .dental-dashboard .dd-period { display:flex; align-items:flex-end; flex-wrap:wrap; gap:12px; padding:18px; margin-bottom:18px; background:#fff; border:1px solid var(--dd-border); border-radius:14px; }
        .dental-dashboard .dd-period label { display:block; font-size:13px; font-weight:600; margin-bottom:7px; }
        .dental-dashboard .dd-period input { min-height:42px; max-width:100%; border:1px solid var(--dd-border); border-radius:8px; padding:8px 12px; color:var(--dd-text); background:#fff; font:inherit; }
        .dental-dashboard .dd-period-note { margin-bottom:18px; line-height:1.7; }
        .dental-dashboard .dd-previous { margin-bottom:18px; padding:16px 18px; border:1px solid #e8c98e; border-radius:12px; background:#fff9ed; font-size:13px; line-height:1.7; }
    </style>
    <form method="GET" action="{{ route('dashboard') }}" class="dd-period">
        <div>
            <label for="mes">Mes del resumen</label>
            <input type="month" id="mes" name="mes" value="{{ $mesSeleccionado }}" required aria-describedby="dd-period-help">
        </div>
        <button type="submit" class="dd-button">Consultar</button>
        <a href="{{ route('dashboard') }}" class="dd-button dd-button-secondary">Mes actual</a>
        @error('mes')<p role="alert">{{ $message }}</p>@enderror
    </form>
    <p id="dd-period-help" class="dd-note dd-period-note">
        Se muestran órdenes ingresadas en {{ $mesTexto }}, con su estado actual.
        Al consultar meses anteriores no se reconstruye el estado que tenían al cierre de ese mes.
    </p>
    @if($pendientesMesesAnteriores > 0)
        <aside class="dd-previous" aria-label="Pendientes de meses anteriores">
            <strong>{{ $pendientesMesesAnteriores }} órdenes de meses anteriores siguen abiertas.</strong>
            Son órdenes ingresadas antes de {{ $mesTexto }} y actualmente sin entregar ni anular.
            No se suman a los indicadores del mes seleccionado.
            <a href="{{ route('ordenes.index') }}" class="dd-link">Consultar el listado general de órdenes</a>
        </aside>
    @endif

    {{-- INDICADORES --}}
    <div class="dd-metrics">

        <article class="dd-metric dd-metric-cyan">
            <p class="dd-label">Órdenes ingresadas en el mes</p>

            <strong class="dd-number">{{ $totalOrdenesMes }}</strong>

            <p class="dd-note">
                {{ $ordenesCanceladas }} anuladas dentro de este total
            </p>
        </article>

        <article class="dd-metric">
            <p class="dd-label">Pendientes del mes</p>

            <strong class="dd-number">{{ $ordenesPendientes }}</strong>

            <p class="dd-note">
                Pendientes de iniciar o continuar
            </p>
        </article>

        <article class="dd-metric dd-metric-cyan">
            <p class="dd-label">En producción</p>

            <strong class="dd-number">{{ $ordenesEnProceso }}</strong>

            <p class="dd-note">
                Órdenes del mes actualmente en proceso
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
                Órdenes del mes actualmente entregadas
            </p>
        </article>

        <article class="dd-metric dd-metric-alert">
            <p class="dd-label">Entregas atrasadas</p>

            <strong class="dd-number">{{ $ordenesAtrasadas }}</strong>

            <p class="dd-note">
                Órdenes del mes con fecha vencida, al día de hoy
            </p>
        </article>

    </div>

    {{-- ACCESOS --}}
    <nav class="dd-shortcuts" aria-label="Accesos del laboratorio">
        <a href="{{ route('agenda.index', ['fecha' => $fechaHoy]) }}" class="dd-shortcut">
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
                        Órdenes del mes abiertas por área
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

                        <progress value="{{ $area['total'] }}" max="{{ max(1, $totalActivas) }}" aria-label="Órdenes abiertas de {{ $area['nombre'] }}"></progress>
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
                        Distribución actual de las órdenes abiertas del mes elegido.
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
                                {{ (int) $etapa->cantidad_ordenes }}
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
                    Entregas por atender del mes seleccionado
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
            <div class="dd-table-scroll" role="region" aria-label="Listado de entregas por atender" tabindex="0">
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

                                $estaAtrasada = $fechaEntrega->toDateString() < $fechaHoy;
                                $nombrePaciente = trim(($orden->paciente?->nombre ?? '').' '.($orden->paciente?->apellido ?? ''));
                            @endphp

                            <tr>
                                <td>
                                    <a class="dd-link" href="{{ route('ordenes.show', $orden) }}">
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
                                    <a class="dd-link" href="{{ route('ordenes.show', $orden) }}" aria-label="Ver orden {{ $orden->codigo }}">
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
                No hay órdenes del mes seleccionado con entrega pendiente para hoy
                ni con fecha de entrega vencida.
            </p>
        @endif
    </section>

    <footer class="dd-footer">
        <p>
            {{ $usuario->name }} · {{ $rol ?? 'Sin rol' }}
        </p>

        <p>Período: {{ $mesTexto }} · Estados y atrasos consultados al {{ $fechaTexto }}.</p>
    </footer>

</div>
@endsection
