@extends('layouts.app')

@section('title', 'Producción | Laboratorio Dental')

@section('content')

<div class="w-full min-w-0">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <p class="text-sm font-semibold text-[#315875] uppercase">
                Producción
            </p>

            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                Control de Producción
            </h1>

            <p class="text-slate-500 mt-1">
                Supervisión de trabajos activos por área, etapa,
                técnico y fecha de entrega.
            </p>
        </div>

    </div>

    {{-- INDICADORES OPERATIVOS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

        {{-- SIN ASIGNAR --}}
        <a
            href="{{ route('produccion.index', [
                'tecnico' => 'sin_asignar'
            ]) }}"
            class="bg-white border border-slate-200
                   border-l-4 border-l-amber-500
                   rounded-xl shadow-sm p-5
                   hover:shadow-md transition"
        >
            <p class="text-xs uppercase font-semibold text-slate-500">
                Sin asignar
            </p>

            <p class="text-3xl font-bold text-amber-600 mt-2">
                {{ $sinAsignar }}
            </p>

            <p class="text-xs text-slate-400 mt-1">
                Trabajos sin técnico responsable
            </p>
        </a>

        {{-- EN PRODUCCIÓN --}}
        <div
            class="bg-white border border-slate-200
                   border-l-4 border-l-[#315875]
                   rounded-xl shadow-sm p-5"
        >
            <p class="text-xs uppercase font-semibold text-slate-500">
                En producción
            </p>

            <p class="text-3xl font-bold text-[#315875] mt-2">
                {{ $enProduccion }}
            </p>

            <p class="text-xs text-slate-400 mt-1">
                Trabajos actualmente activos
            </p>
        </div>

        {{-- PRÓXIMAS A ENTREGAR --}}
        <div
            class="bg-white border border-slate-200
                   border-l-4 border-l-violet-500
                   rounded-xl shadow-sm p-5"
        >
            <p class="text-xs uppercase font-semibold text-slate-500">
                Próximas a entregar
            </p>

            <p class="text-3xl font-bold text-violet-600 mt-2">
                {{ $proximasEntrega }}
            </p>

            <p class="text-xs text-slate-400 mt-1">
                Dentro de los próximos 2 días
            </p>
        </div>

        {{-- ATRASADAS --}}
        <a
            href="{{ route('produccion.index', [
                'filtro' => 'atrasadas'
            ]) }}"
            class="bg-white border border-slate-200
                   border-l-4 border-l-red-500
                   rounded-xl shadow-sm p-5
                   hover:shadow-md transition"
        >
            <p class="text-xs uppercase font-semibold text-slate-500">
                Atrasadas
            </p>

            <p class="text-3xl font-bold text-red-600 mt-2">
                {{ $atrasadas }}
            </p>

            <p class="text-xs text-slate-400 mt-1">
                Fecha estimada ya vencida
            </p>
        </a>

    </div>

    {{-- FILTROS --}}
    <form
        method="GET"
        action="{{ route('produccion.index') }}"
        class="bg-white border border-slate-200
               rounded-xl shadow-sm p-5 mb-8"
    >

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            {{-- BUSCAR --}}
            <div>
                <label
                    class="block text-xs font-semibold
                           text-slate-600 mb-1"
                >
                    Buscar
                </label>

                <input
                    type="text"
                    name="buscar"
                    value="{{ request('buscar') }}"
                    placeholder="Orden, código o paciente..."
                    class="w-full min-w-0 border border-slate-200 rounded-lg
                           bg-white px-4 py-2.5 text-sm text-slate-800
                           focus:outline-none focus:ring-2
                           focus:ring-[#315875] focus:border-[#315875]"
                >
            </div>

            {{-- ÁREA --}}
            <div>
                <label
                    class="block text-xs font-semibold
                           text-slate-600 mb-1"
                >
                    Área
                </label>

                <select
                    name="area"
                    class="w-full min-w-0 border border-slate-200 rounded-lg
                           bg-white px-4 py-2.5 text-sm text-slate-800
                           focus:outline-none focus:ring-2
                           focus:ring-[#315875] focus:border-[#315875]"
                >
                    <option value="">
                        Todas las áreas
                    </option>

                    <option
                        value="removible"
                        @selected(request('area') === 'removible')
                    >
                        Prótesis Removibles
                    </option>

                    <option
                        value="fija"
                        @selected(request('area') === 'fija')
                    >
                        Prótesis Fijas
                    </option>

                    <option
                        value="cromo_cobalto"
                        @selected(request('area') === 'cromo_cobalto')
                    >
                        Cromo Cobalto
                    </option>

                    <option
                        value="ortodoncia"
                        @selected(request('area') === 'ortodoncia')
                    >
                        Aparatos de Ortodoncia
                    </option>
                </select>
            </div>

        </div>

        {{-- BOTONES --}}
        <div class="flex flex-col sm:flex-row sm:justify-end gap-3 mt-5">

            <a
                href="{{ route('produccion.index') }}"
                class="inline-flex items-center justify-center
                       px-5 py-2.5 border border-slate-200
                       rounded-lg text-[#315875] text-sm font-semibold
                       hover:bg-[#e7eef8] transition-colors"
            >
                Limpiar
            </a>

            <button
                type="submit"
                class="inline-flex items-center justify-center
                       px-6 py-2.5 bg-[#315875] hover:bg-[#182d47]
                       text-white rounded-lg text-sm font-semibold
                       transition-colors"
            >
                Filtrar
            </button>

        </div>

    </form>

    {{-- TABLA DE PRODUCCIÓN ACTIVA --}}
    <section
        class="bg-white border border-slate-200
               rounded-xl shadow-sm overflow-hidden"
    >

        <div class="px-6 py-5 border-b border-slate-200">

            <h2 class="text-lg font-bold text-slate-900">
                Producción Activa
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Trabajos que todavía requieren seguimiento
                dentro del laboratorio.
            </p>

        </div>

        <div
            class="w-full min-w-0 overflow-x-auto"
            tabindex="0"
            role="region"
            aria-label="Listado de producción activa"
        >

            <table class="w-full min-w-[1100px] text-sm">

                <thead class="bg-[#eef3ff] text-slate-500 uppercase text-xs">
                    <tr>
                        <th class="text-left px-5 py-4">
                            Orden
                        </th>

                        <th class="text-left px-5 py-4">
                            Área / Código
                        </th>

                        <th class="text-left px-5 py-4">
                            Trabajo
                        </th>

                        <th class="text-left px-5 py-4">
                            Etapa actual
                        </th>

                        <th class="text-left px-5 py-4">
                            Técnico
                        </th>

                        <th class="text-left px-5 py-4">
                            Estado
                        </th>

                        <th class="text-left px-5 py-4">
                            Entrega
                        </th>

                        <th class="text-right px-5 py-4">
                            Acción
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse($ordenes as $orden)

                        @php
                            $estado = $orden->estadoOrden?->nombre;

                            $atrasada =
                                $orden->fecha_entrega_estimada
                                &&
                                \Carbon\Carbon::parse(
                                    $orden->fecha_entrega_estimada
                                )
                                ->startOfDay()
                                ->lt(
                                    now()->startOfDay()
                                );

                            $nombreArea = match($orden->area_trabajo) {
                                'removible' => 'Prótesis Removibles',
                                'fija' => 'Prótesis Fijas',
                                'cromo_cobalto' => 'Cromo Cobalto',
                                'ortodoncia' => 'Aparatos de Ortodoncia',
                                default => 'Sin área',
                            };
                        @endphp

                        <tr
                            class="hover:bg-slate-50
                                   {{ $atrasada ? 'bg-red-50/40' : '' }}"
                        >

                            {{-- ORDEN --}}
                            <td class="px-5 py-4 align-top">

                                <p class="font-bold text-[#315875] whitespace-nowrap">
                                    {{ $orden->codigo }}
                                </p>

                                <p class="text-xs text-slate-500 mt-1">
                                    {{ $orden->paciente?->nombre ?? 'Sin paciente' }}
                                </p>

                                @if($orden->prioridad === 'Urgente')
                                    <span
                                        class="inline-block mt-1
                                               text-xs font-bold text-red-600"
                                    >
                                        Urgente
                                    </span>
                                @endif

                            </td>

                            {{-- ÁREA / CÓDIGO --}}
                            <td class="px-5 py-4 align-top">

                                <p class="font-semibold text-slate-800">
                                    {{ $nombreArea }}
                                </p>

                                <p
                                    class="text-xs text-[#315875]
                                           font-semibold mt-1 whitespace-nowrap"
                                >
                                    {{ $orden->codigo_area ?? '—' }}
                                </p>

                            </td>

                            {{-- TIPO DE TRABAJO --}}
                            <td class="px-5 py-4 align-top">

                                <p class="font-medium text-slate-800">
                                    {{ $orden->tipoProtesis?->nombre ?? '—' }}
                                </p>

                            </td>

                            {{-- ETAPA --}}
                            <td class="px-5 py-4 align-top">

                                @if($orden->etapaActual)

                                    <span
                                        class="inline-flex px-3 py-1 rounded-full
                                               bg-violet-50 text-violet-700
                                               text-xs font-semibold"
                                    >
                                        {{ $orden->etapaActual->nombre }}
                                    </span>

                                @else

                                    <span class="text-amber-600 font-semibold">
                                        Sin etapa
                                    </span>

                                @endif

                            </td>

                            {{-- TÉCNICO --}}
                            <td class="px-5 py-4 align-top">

                                @if($orden->tecnicoActual)

                                    <p class="font-medium text-slate-800">
                                        {{
                                            $orden->tecnicoActual->user?->name
                                            ??
                                            'Técnico #' . $orden->tecnicoActual->id
                                        }}
                                    </p>

                                @else

                                    <span class="text-amber-600 font-semibold">
                                        Sin asignar
                                    </span>

                                @endif

                            </td>

                            {{-- ESTADO --}}
                            <td class="px-5 py-4 align-top whitespace-nowrap">

                                @if($estado === 'En proceso')

                                    <span
                                        class="inline-flex bg-[#e7eef8]
                                               text-[#315875] px-3 py-1
                                               rounded-full text-xs font-semibold"
                                    >
                                        En proceso
                                    </span>

                                @else

                                    <span
                                        class="inline-flex bg-amber-100
                                               text-amber-700 px-3 py-1
                                               rounded-full text-xs font-semibold"
                                    >
                                        {{ $estado ?? 'Pendiente' }}
                                    </span>

                                @endif

                            </td>

                            {{-- ENTREGA --}}
                            <td class="px-5 py-4 align-top">

                                @if($orden->fecha_entrega_estimada)

                                    <p
                                        class="font-semibold whitespace-nowrap
                                               {{ $atrasada
                                                   ? 'text-red-700'
                                                   : 'text-slate-700' }}"
                                    >
                                        {{
                                            \Carbon\Carbon::parse(
                                                $orden->fecha_entrega_estimada
                                            )->format('d/m/Y')
                                        }}
                                    </p>

                                    @if($atrasada)
                                        <span class="text-xs font-bold text-red-600">
                                            Atrasada
                                        </span>
                                    @endif

                                @else

                                    <span class="text-slate-400">
                                        Sin fecha
                                    </span>

                                @endif

                            </td>

                            {{-- ACCIÓN --}}
                            <td class="px-5 py-4 text-right align-top">

                                <a
                                    href="{{ route(
                                        'ordenes.show',
                                        [
                                            'orden' => $orden,
                                            'origen' => 'produccion',
                                        ]
                                    ) }}"
                                    class="text-[#315875] font-semibold
                                           hover:underline whitespace-nowrap"
                                >
                                    Gestionar
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="8"
                                class="px-6 py-12 text-center"
                            >
                                <p class="font-semibold text-slate-600">
                                    No hay trabajos activos.
                                </p>

                                <p class="text-sm text-slate-400 mt-1">
                                    No se encontraron órdenes que
                                    requieran seguimiento de producción.
                                </p>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- PAGINACIÓN --}}
        @if($ordenes->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $ordenes->withQueryString()->onEachSide(1)->links() }}
            </div>
        @endif

    </section>

</div>

@endsection