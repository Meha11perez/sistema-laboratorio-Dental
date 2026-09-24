@extends('layouts.app')

@section('title', 'Producción | Laboratorio Dental')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- =========================================================
         ENCABEZADO
    ========================================================== --}}
    <div class="flex flex-col md:flex-row md:items-center
                md:justify-between gap-4 mb-8">

        <div>

            <p class="text-sm font-semibold text-blue-800 uppercase">
                Producción
            </p>

            <h1 class="text-3xl font-bold text-slate-900">
                Control de Producción
            </h1>

            <p class="text-slate-500 mt-1">
                Supervisión de trabajos activos por etapa,
                técnico y fecha de entrega.
            </p>

        </div>

    </div>


    {{-- =========================================================
         INDICADORES OPERATIVOS
    ========================================================== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4
                gap-5 mb-8">


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
        <div class="bg-white border border-slate-200
                    border-l-4 border-l-blue-600
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase font-semibold text-slate-500">
                En producción
            </p>

            <p class="text-3xl font-bold text-blue-700 mt-2">
                {{ $enProduccion }}
            </p>

            <p class="text-xs text-slate-400 mt-1">
                Trabajos actualmente activos
            </p>

        </div>


        {{-- PRÓXIMAS --}}
        <div class="bg-white border border-slate-200
                    border-l-4 border-l-violet-500
                    rounded-xl shadow-sm p-5">

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


    {{-- =========================================================
         FILTROS
    ========================================================== --}}
    <form
        method="GET"
        action="{{ route('produccion.index') }}"
        class="bg-white border border-slate-200
               rounded-xl shadow-sm p-5 mb-8"
    >

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

            {{-- BUSCAR --}}
            <div>

                <label class="block text-xs font-semibold
                              text-slate-600 mb-1">
                    Buscar
                </label>

                <input
                    type="text"
                    name="buscar"
                    value="{{ request('buscar') }}"
                    placeholder="Orden o paciente..."
                    class="w-full border border-slate-300
                           rounded-lg px-3 py-2"
                >

            </div>


            {{-- ESTADO --}}
            <div>

                <label class="block text-xs font-semibold
                              text-slate-600 mb-1">
                    Estado
                </label>

                <select
                    name="estado"
                    class="w-full border border-slate-300
                           rounded-lg px-3 py-2 bg-white"
                >

                    <option value="">
                        Todos
                    </option>

                    @foreach($estados as $estado)

                        <option
                            value="{{ $estado->id }}"
                            @selected(
                                request('estado') == $estado->id
                            )
                        >
                            {{ $estado->nombre }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- ETAPA --}}
            <div>

                <label class="block text-xs font-semibold
                              text-slate-600 mb-1">
                    Etapa
                </label>

                <select
                    name="etapa"
                    class="w-full border border-slate-300
                           rounded-lg px-3 py-2 bg-white"
                >

                    <option value="">
                        Todas
                    </option>

                    @foreach($etapas as $etapa)

                        <option
                            value="{{ $etapa->id }}"
                            @selected(
                                request('etapa') == $etapa->id
                            )
                        >
                            {{ $etapa->nombre }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- TÉCNICO --}}
            <div>

                <label class="block text-xs font-semibold
                              text-slate-600 mb-1">
                    Técnico
                </label>

                <select
                    name="tecnico"
                    class="w-full border border-slate-300
                           rounded-lg px-3 py-2 bg-white"
                >

                    <option value="">
                        Todos
                    </option>

                    <option
                        value="sin_asignar"
                        @selected(
                            request('tecnico') === 'sin_asignar'
                        )
                    >
                        Sin asignar
                    </option>

                    @foreach($tecnicos as $tecnico)

                        <option
                            value="{{ $tecnico->id }}"
                            @selected(
                                request('tecnico') == $tecnico->id
                            )
                        >
                            {{ $tecnico->user?->name ?? 'Sin usuario' }}
                        </option>

                    @endforeach

                </select>

            </div>

        </div>


        <div class="flex justify-end gap-3 mt-5">

            <a
                href="{{ route('produccion.index') }}"
                class="px-5 py-2 border border-slate-300
                       rounded-lg text-slate-600
                       hover:bg-slate-50"
            >
                Limpiar
            </a>

            <button
                type="submit"
                class="px-5 py-2 bg-blue-800
                       text-white rounded-lg
                       font-semibold hover:bg-blue-900"
            >
                Filtrar
            </button>

        </div>

    </form>


    {{-- =========================================================
         TABLA DE PRODUCCIÓN ACTIVA
    ========================================================== --}}
    <section class="bg-white border border-slate-200
                    rounded-xl shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-200">

            <h2 class="text-lg font-bold text-slate-900">
                Producción Activa
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Trabajos que todavía requieren seguimiento
                dentro del laboratorio.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50 text-slate-500 uppercase text-xs">

                    <tr>

                        <th class="text-left px-5 py-4">
                            Orden
                        </th>

                        <th class="text-left px-5 py-4">
                            Prótesis
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

                            $estado =
                                $orden->estadoOrden?->nombre;

                            $atrasada =
                                $orden->fecha_entrega_estimada
                                &&
                                \Carbon\Carbon::parse(
                                    $orden->fecha_entrega_estimada
                                )->startOfDay()->lt(
                                    now()->startOfDay()
                                );

                        @endphp


                        <tr class="
                            hover:bg-slate-50
                            {{ $atrasada ? 'bg-red-50/40' : '' }}
                        ">

                            {{-- ORDEN --}}
                            <td class="px-5 py-4">

                                <p class="font-bold text-blue-900">
                                    {{ $orden->codigo }}
                                </p>

                                <p class="text-xs text-slate-400 mt-1">
                                    {{ $orden->paciente?->nombre ?? 'Sin paciente' }}
                                </p>

                                @if($orden->prioridad === 'Urgente')

                                    <span class="inline-block mt-1
                                                 text-xs font-bold
                                                 text-red-600">
                                        Urgente
                                    </span>

                                @endif

                            </td>


                            {{-- PRÓTESIS --}}
                            <td class="px-5 py-4">

                                {{ $orden->tipoProtesis?->nombre ?? '—' }}

                            </td>


                            {{-- ETAPA --}}
                            <td class="px-5 py-4">

                                @if($orden->etapaActual)

                                    <span class="inline-flex px-3 py-1
                                                 rounded-full
                                                 bg-violet-50
                                                 text-violet-700
                                                 text-xs font-semibold">

                                        {{ $orden->etapaActual->nombre }}

                                    </span>

                                @else

                                    <span class="text-amber-600 font-semibold">
                                        Sin etapa
                                    </span>

                                @endif

                            </td>


                            {{-- TÉCNICO --}}
                            <td class="px-5 py-4">

                                @if($orden->tecnicoActual)

                                    {{ $orden->tecnicoActual->user?->name
                                        ?? 'Técnico #' . $orden->tecnicoActual->id }}

                                @else

                                    <span class="text-amber-600 font-semibold">
                                        Sin asignar
                                    </span>

                                @endif

                            </td>


                            {{-- ESTADO --}}
                            <td class="px-5 py-4">

                                @if($estado === 'En proceso')

                                    <span class="bg-blue-100
                                                 text-blue-700
                                                 px-3 py-1 rounded-full
                                                 text-xs font-semibold">
                                        En proceso
                                    </span>

                                @else

                                    <span class="bg-amber-100
                                                 text-amber-700
                                                 px-3 py-1 rounded-full
                                                 text-xs font-semibold">

                                        {{ $estado ?? 'Pendiente' }}

                                    </span>

                                @endif

                            </td>


                            {{-- ENTREGA --}}
                            <td class="px-5 py-4">

                                @if($orden->fecha_entrega_estimada)

                                    <p class="
                                        font-semibold
                                        {{ $atrasada
                                            ? 'text-red-700'
                                            : 'text-slate-700' }}
                                    ">

                                        {{ \Carbon\Carbon::parse(
                                            $orden->fecha_entrega_estimada
                                        )->format('d/m/Y') }}

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
                            <td class="px-5 py-4 text-right">
                            <a
                                href="{{ route('ordenes.show', [
                                    'orden' => $orden,
                                    'origen' => 'produccion',
                                ]) }}"
                                class="text-blue-700
                                    font-semibold
                                    hover:underline">
                                    
                                Gestionar
                            </a>
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="px-6 py-12 text-center"
                            >

                                <p class="font-semibold text-slate-600">
                                    No hay trabajos activos.
                                </p>

                                <p class="text-sm text-slate-400 mt-1">
                                    No se encontraron órdenes que requieran
                                    seguimiento de producción.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($ordenes->hasPages())

            <div class="px-6 py-4 border-t border-slate-200">
                {{ $ordenes->links() }}
            </div>

        @endif

    </section>

</div>

@endsection