@extends('layouts.app')

@section('title', 'Detalle del Material | Laboratorio Dental')

@section('content')

<div class="max-w-5xl mx-auto">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col md:flex-row md:items-center
                md:justify-between gap-4 mb-8">

        <div>

            <a
                href="{{ route('inventario.index') }}"
                class="text-sm font-semibold text-blue-700 hover:underline"
            >
                ← Volver al inventario
            </a>

            <h1 class="text-3xl font-bold text-slate-900 mt-3">
                {{ $material->nombre }}
            </h1>

            <p class="text-slate-500 mt-1">
                Información general del material.
            </p>

        </div>

        {{-- ESTADO --}}
        <div>

            @if(!$material->estado)

                <span class="inline-flex bg-slate-100 text-slate-600
                             px-4 py-2 rounded-full text-sm font-semibold">
                    Inactivo
                </span>

            @elseif($material->stock_actual <= $material->stock_minimo)

                <span class="inline-flex bg-red-50 text-red-700
                             px-4 py-2 rounded-full text-sm font-semibold">
                    Stock bajo
                </span>

            @else

                <span class="inline-flex bg-emerald-50 text-emerald-700
                             px-4 py-2 rounded-full text-sm font-semibold">
                    Disponible
                </span>

            @endif

        </div>

    </div>


    {{-- INFORMACIÓN PRINCIPAL --}}
    <div class="bg-white border border-slate-200
                rounded-xl shadow-sm overflow-hidden mb-6">

        <div class="px-6 py-4 border-b border-slate-200">

            <h2 class="font-bold text-slate-900">
                Información del Material
            </h2>

        </div>


        <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            {{-- CÓDIGO --}}
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase">
                    Código
                </p>

                <p class="font-semibold text-slate-900 mt-1">
                    {{ $material->codigo ?? '—' }}
                </p>
            </div>


            {{-- NOMBRE --}}
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase">
                    Material
                </p>

                <p class="font-semibold text-slate-900 mt-1">
                    {{ $material->nombre }}
                </p>
            </div>


            {{-- UNIDAD --}}
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase">
                    Unidad de medida
                </p>

                <p class="font-semibold text-slate-900 mt-1">
                    {{ $material->unidad_medida }}
                </p>
            </div>


            {{-- STOCK ACTUAL --}}
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase">
                    Stock actual
                </p>

                <p class="text-2xl font-bold mt-1
                    {{ $material->stock_actual <= $material->stock_minimo
                        ? 'text-red-600'
                        : 'text-emerald-700' }}">
                    {{ number_format($material->stock_actual, 2) }}
                </p>
            </div>


            {{-- STOCK MÍNIMO --}}
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase">
                    Stock mínimo
                </p>

                <p class="text-2xl font-bold text-slate-900 mt-1">
                    {{ number_format($material->stock_minimo, 2) }}
                </p>
            </div>


            {{-- COSTO --}}
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase">
                    Costo unitario
                </p>

                <p class="text-2xl font-bold text-slate-900 mt-1">
                    Q {{ number_format($material->costo_unitario, 2) }}
                </p>
            </div>

        </div>

    </div>


    {{-- DESCRIPCIÓN --}}
    <div class="bg-white border border-slate-200
                rounded-xl shadow-sm p-6 mb-6">

        <h2 class="font-bold text-slate-900 mb-3">
            Descripción
        </h2>

        <p class="text-slate-600">
            {{ $material->descripcion ?? 'Sin descripción registrada.' }}
        </p>

    </div>

    {{-- MOVIMIENTOS --}}
    <div class="bg-white border border-slate-200
                rounded-xl shadow-sm overflow-hidden">

       {{-- CABECERA DE MOVIMIENTOS --}}
        <div class="flex flex-col sm:flex-row sm:items-center
                    sm:justify-between gap-4
                    px-6 py-4 border-b border-slate-200">

            <div>

                <h2 class="font-bold text-slate-900">
                    Movimientos de Inventario
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Entradas, salidas y asignaciones registradas
                    para este material.
                </p>

            </div>


            {{-- BOTONES --}}
            <div class="flex flex-wrap gap-3">

                {{-- ASIGNAR A TÉCNICO --}}
                <a
                    href="{{ route(
                        'inventario.asignar',
                        $material
                    ) }}"
                    class="inline-flex items-center justify-center
                        bg-emerald-600 hover:bg-emerald-700
                        text-white px-5 py-3 rounded-lg
                        font-semibold text-sm transition"
                >
                    Asignar a técnico
                </a>


                {{-- REGISTRAR MOVIMIENTO --}}
                <a
                    href="{{ route(
                        'inventario.movimiento.create',
                        $material
                    ) }}"
                    class="inline-flex items-center justify-center
                        bg-blue-800 hover:bg-blue-900
                        text-white px-5 py-3 rounded-lg
                        font-semibold text-sm transition"
                >
                    + Registrar Movimiento
                </a>

            </div>

        </div>

        {{-- HISTORIAL --}}
        <div class="overflow-x-auto">

        <table class="w-full text-sm">

            <thead class="bg-slate-50 text-slate-500 uppercase text-xs">

                <tr>
                    <th class="text-left px-6 py-4">Fecha</th>
                    <th class="text-left px-6 py-4">Tipo</th>
                    <th class="text-left px-6 py-4">Cantidad</th>
                    <th class="text-left px-6 py-4">Stock anterior</th>
                    <th class="text-left px-6 py-4">Stock nuevo</th>
                    <th class="text-left px-6 py-4">Registrado por</th>
                    <th class="text-left px-6 py-4">Observaciones</th>
                </tr>

            </thead>

            <tbody class="divide-y divide-slate-100">

                @forelse(
                    $material->movimientosInventario
                        ->sortByDesc('fecha_movimiento')
                    as $movimiento
                )

                    <tr class="hover:bg-slate-50">

                        {{-- FECHA --}}
                        <td class="px-6 py-4 whitespace-nowrap">

                            {{ $movimiento->fecha_movimiento?->format('d/m/Y H:i') }}

                        </td>

                {{-- TIPO --}}
                <td class="px-6 py-4">

                    @if($movimiento->tipo_movimiento === 'Entrada')

                        <span class="inline-flex px-3 py-1 rounded-full
                                    bg-emerald-50 text-emerald-700
                                    text-xs font-semibold">
                            Entrada
                        </span>


                    @elseif($movimiento->tipo_movimiento === 'Salida')

                        <span class="inline-flex px-3 py-1 rounded-full
                                    bg-red-50 text-red-700
                                    text-xs font-semibold">
                            Salida
                        </span>


                    @elseif($movimiento->tipo_movimiento === 'Consumo')

                        <span class="inline-flex px-3 py-1 rounded-full
                                    bg-purple-50 text-purple-700
                                    text-xs font-semibold">
                            Consumo
                        </span>


                    @elseif($movimiento->tipo_movimiento === 'Devolución')

                        <span class="inline-flex px-3 py-1 rounded-full
                                    bg-blue-50 text-blue-700
                                    text-xs font-semibold">
                            Devolución
                        </span>


                    @else

                        <span class="inline-flex px-3 py-1 rounded-full
                                    bg-amber-50 text-amber-700
                                    text-xs font-semibold">
                            Ajuste
                        </span>

                    @endif

                </td>
                        {{-- CANTIDAD --}}
                        <td class="px-6 py-4 font-semibold">

                            {{ number_format($movimiento->cantidad, 2) }}

                        </td>


                        {{-- STOCK ANTERIOR --}}
                        <td class="px-6 py-4">

                            {{ number_format($movimiento->stock_anterior, 2) }}

                        </td>


                        {{-- STOCK NUEVO --}}
                        <td class="px-6 py-4 font-bold text-slate-900">

                            {{ number_format($movimiento->stock_nuevo, 2) }}

                        </td>


                        {{-- USUARIO --}}
                        <td class="px-6 py-4">

                            {{ $movimiento->usuarioRegistro?->name ?? '—' }}

                        </td>


                        {{-- OBSERVACIONES --}}
                        <td class="px-6 py-4 text-slate-500">

                            {{ $movimiento->observaciones ?? '—' }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="7"
                            class="px-6 py-10 text-center text-slate-400"
                        >
                            Todavía no hay movimientos registrados.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

        </div>
</div>

@endsection