@extends('layouts.app')

@section('title', 'Movimientos de Inventario | Laboratorio Dental')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- =========================================================
         ENCABEZADO
    ========================================================== --}}
    <div class="mb-8">

        <p class="text-sm font-semibold text-blue-800 uppercase">
            Inventario
        </p>

        <h1 class="text-3xl font-bold text-slate-900">
            Movimientos de Inventario
        </h1>

        <p class="text-slate-500 mt-1">
            Historial de entradas, salidas, asignaciones
            y consumos de materiales.
        </p>

    </div>


    {{-- =========================================================
         INDICADORES
    ========================================================== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4
                gap-5 mb-8">

        <div class="bg-white border border-slate-200
                    border-l-4 border-l-blue-600
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase font-semibold text-slate-500">
                Movimientos hoy
            </p>

            <p class="text-3xl font-bold text-blue-700 mt-2">
                {{ $movimientosHoy }}
            </p>

        </div>


        <div class="bg-white border border-slate-200
                    border-l-4 border-l-emerald-500
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase font-semibold text-slate-500">
                Entradas
            </p>

            <p class="text-3xl font-bold text-emerald-600 mt-2">
                {{ $entradas }}
            </p>

        </div>


        <div class="bg-white border border-slate-200
                    border-l-4 border-l-violet-500
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase font-semibold text-slate-500">
                Asignaciones
            </p>

            <p class="text-3xl font-bold text-violet-600 mt-2">
                {{ $asignaciones }}
            </p>

        </div>


        <div class="bg-white border border-slate-200
                    border-l-4 border-l-amber-500
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase font-semibold text-slate-500">
                Consumos
            </p>

            <p class="text-3xl font-bold text-amber-600 mt-2">
                {{ $consumos }}
            </p>

        </div>

    </div>


    {{-- =========================================================
         FILTROS
    ========================================================== --}}
    <form
        method="GET"
        action="{{ route('inventario.movimientos') }}"
        class="bg-white border border-slate-200
               rounded-xl shadow-sm p-5 mb-8"
    >

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

            {{-- BUSCAR --}}
            <div>

                <label class="block text-xs font-semibold
                              text-slate-600 mb-2">
                    Buscar
                </label>

                <input
                    type="text"
                    name="buscar"
                    value="{{ request('buscar') }}"
                    placeholder="Material, orden o técnico..."
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-2.5"
                >

            </div>


            {{-- TIPO --}}
            <div>

                <label class="block text-xs font-semibold
                              text-slate-600 mb-2">
                    Tipo
                </label>

                <select
                    name="tipo"
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-2.5 bg-white"
                >

                    <option value="">
                        Todos
                    </option>

                    <option
                        value="Entrada"
                        @selected(request('tipo') === 'Entrada')
                    >
                        Entrada
                    </option>

                    <option
                        value="Salida"
                        @selected(request('tipo') === 'Salida')
                    >
                        Salida general
                    </option>

                    <option
                        value="Asignacion"
                        @selected(request('tipo') === 'Asignacion')
                    >
                        Asignación a técnico
                    </option>

                    <option
                        value="Consumo"
                        @selected(request('tipo') === 'Consumo')
                    >
                        Consumo en orden
                    </option>

                    <option
                        value="Ajuste"
                        @selected(request('tipo') === 'Ajuste')
                    >
                        Ajuste
                    </option>

                    <option
                        value="Devolucion"
                        @selected(request('tipo') === 'Devolucion')
                    >
                        Devolución
                    </option>

                </select>

            </div>


            {{-- TÉCNICO --}}
            <div>

                <label class="block text-xs font-semibold
                              text-slate-600 mb-2">
                    Técnico
                </label>

                <select
                    name="tecnico"
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-2.5 bg-white"
                >

                    <option value="">
                        Todos
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


            {{-- FECHA --}}
            <div>

                <label class="block text-xs font-semibold
                              text-slate-600 mb-2">
                    Fecha
                </label>

                <input
                    type="date"
                    name="fecha"
                    value="{{ request('fecha') }}"
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-2.5"
                >

            </div>

        </div>


        <div class="flex justify-end gap-3 mt-5">

            <a
                href="{{ route('inventario.movimientos') }}"
                class="px-5 py-2.5
                       border border-slate-300
                       rounded-lg text-slate-600
                       hover:bg-slate-50"
            >
                Limpiar
            </a>

            <button
                type="submit"
                class="px-5 py-2.5
                       bg-blue-800
                       text-white
                       rounded-lg
                       font-semibold
                       hover:bg-blue-900"
            >
                Filtrar
            </button>

        </div>

    </form>


    {{-- =========================================================
         HISTORIAL
    ========================================================== --}}
    <div class="bg-white border border-slate-200
                rounded-xl shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-200">

            <h2 class="font-bold text-slate-900">
                Historial de Movimientos
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Trazabilidad de los cambios realizados
                sobre el inventario.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50
                              text-xs text-slate-500 uppercase">

                    <tr>

                        <th class="text-left px-5 py-4">
                            Fecha
                        </th>

                        <th class="text-left px-5 py-4">
                            Material
                        </th>

                        <th class="text-left px-5 py-4">
                            Movimiento
                        </th>

                        <th class="text-left px-5 py-4">
                            Técnico
                        </th>

                        <th class="text-left px-5 py-4">
                            Orden
                        </th>

                        <th class="text-right px-5 py-4">
                            Cantidad
                        </th>

                        <th class="text-right px-5 py-4">
                            Anterior
                        </th>

                        <th class="text-right px-5 py-4">
                            Nuevo
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($movimientos as $movimiento)

                        @php

                            /*
                             * Interpretamos el movimiento
                             * según su contexto.
                             */

                            if (
                                $movimiento->tecnico_id
                                &&
                                $movimiento->orden_trabajo_id
                            ) {

                                $tipoVisible = 'Consumo';

                                $claseTipo =
                                    'bg-amber-100 text-amber-700';

                            } elseif (
                                $movimiento->tecnico_id
                                &&
                                !$movimiento->orden_trabajo_id
                                &&
                                $movimiento->tipo_movimiento === 'Salida'
                            ) {

                                $tipoVisible = 'Asignación';

                                $claseTipo =
                                    'bg-violet-100 text-violet-700';

                            } elseif (
                                $movimiento->tipo_movimiento === 'Entrada'
                            ) {

                                $tipoVisible = 'Entrada';

                                $claseTipo =
                                    'bg-emerald-100 text-emerald-700';

                            } elseif (
                                in_array(
                                    $movimiento->tipo_movimiento,
                                    ['Devolución', 'Devolucion'],
                                    true
                                )
                            ) {

                                $tipoVisible = 'Devolución';

                                $claseTipo =
                                    'bg-cyan-100 text-cyan-700';

                            } elseif (
                                $movimiento->tipo_movimiento === 'Ajuste'
                            ) {

                                $tipoVisible = 'Ajuste';

                                $claseTipo =
                                    'bg-slate-100 text-slate-700';

                            } else {

                                $tipoVisible =
                                    $movimiento->tipo_movimiento;

                                $claseTipo =
                                    'bg-red-100 text-red-700';
                            }

                        @endphp


                        <tr class="hover:bg-slate-50">

                            {{-- FECHA --}}
                            <td class="px-5 py-4 whitespace-nowrap">

                                <p class="font-medium text-slate-700">
                                    {{ $movimiento->fecha_movimiento
                                        ?->format('d/m/Y') }}
                                </p>

                                <p class="text-xs text-slate-400">
                                    {{ $movimiento->fecha_movimiento
                                        ?->format('H:i') }}
                                </p>

                            </td>


                            {{-- MATERIAL --}}
                            <td class="px-5 py-4">

                                <p class="font-semibold text-slate-900">
                                    {{ $movimiento->material?->nombre ?? '—' }}
                                </p>

                                <p class="text-xs text-slate-400">
                                    {{ $movimiento->material?->codigo ?? '—' }}
                                </p>

                            </td>


                            {{-- TIPO --}}
                            <td class="px-5 py-4">

                                <span class="
                                    inline-flex
                                    px-3 py-1
                                    rounded-full
                                    text-xs
                                    font-semibold
                                    {{ $claseTipo }}
                                ">
                                    {{ $tipoVisible }}
                                </span>

                            </td>


                            {{-- TÉCNICO --}}
                            <td class="px-5 py-4">

                                @if($movimiento->tecnico)

                                    {{ $movimiento
                                        ->tecnico
                                        ?->user
                                        ?->name ?? 'Técnico' }}

                                @else

                                    <span class="text-slate-400">
                                        —
                                    </span>

                                @endif

                            </td>


                            {{-- ORDEN --}}
                            <td class="px-5 py-4">

                                @if($movimiento->ordenTrabajo)

                                    <a
                                        href="{{ route(
                                            'ordenes.show',
                                            $movimiento->ordenTrabajo
                                        ) }}"
                                        class="font-semibold
                                               text-blue-700
                                               hover:underline"
                                    >
                                        {{ $movimiento
                                            ->ordenTrabajo
                                            ->codigo }}
                                    </a>

                                @else

                                    <span class="text-slate-400">
                                        —
                                    </span>

                                @endif

                            </td>


                            {{-- CANTIDAD --}}
                            <td class="px-5 py-4 text-right font-semibold">
                                {{ number_format(
                                    $movimiento->cantidad,
                                    2
                                ) }}
                            </td>


                            {{-- ANTERIOR --}}
                            <td class="px-5 py-4 text-right text-slate-500">

                                {{ $movimiento->stock_anterior !== null
                                    ? number_format(
                                        $movimiento->stock_anterior,
                                        2
                                    )
                                    : '—'
                                }}

                            </td>


                            {{-- NUEVO --}}
                            <td class="px-5 py-4 text-right font-semibold">

                                {{ $movimiento->stock_nuevo !== null
                                    ? number_format(
                                        $movimiento->stock_nuevo,
                                        2
                                    )
                                    : '—'
                                }}

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="8"
                                class="px-6 py-12
                                       text-center text-slate-400"
                            >
                                No se encontraron movimientos
                                con los filtros seleccionados.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($movimientos->hasPages())

            <div class="px-6 py-4 border-t border-slate-200">
                {{ $movimientos->links() }}
            </div>

        @endif

    </div>

</div>

@endsection