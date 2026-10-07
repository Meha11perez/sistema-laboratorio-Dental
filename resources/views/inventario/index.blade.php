@extends('layouts.app')

@section('title', 'Inventario | Laboratorio Dental')

@section('content')

<div class="w-full min-w-0">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

        <div>
            <p class="text-sm font-semibold text-[#315875] uppercase tracking-wide">
                Inventario
            </p>

            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 mt-1">
                    Inventario General
            </h1>

            <p class="text-slate-500 mt-1">
                Control y consulta de materiales disponibles en el laboratorio.
            </p>
        </div>

        <a
            href="{{ route('inventario.create') }}"
           class="inline-flex w-full md:w-auto shrink-0 items-center justify-center gap-2 bg-[#315875] hover:bg-[#182d47] text-white px-5 py-3 rounded-lg font-semibold text-sm transition-colors">
            + Nuevo Material
        </a>

    </div>


    {{-- INDICADORES --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase font-semibold text-slate-500">
                Total materiales
            </p>

            <p class="text-3xl font-bold mt-2">
                {{ $totalMateriales }}
            </p>

        </div>


        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase font-semibold text-slate-500">
                Materiales activos
            </p>

            <p class="text-3xl font-bold mt-2 text-emerald-700">
                {{ $materialesActivos }}
            </p>

        </div>


        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase font-semibold text-slate-500">
                Stock bajo
            </p>

            <p class="text-3xl font-bold mt-2 text-red-600">
                {{ $stockBajo }}
            </p>

        </div>

    </div>


    {{-- FILTROS --}}
    <form
    method="GET"
    action="{{ route('inventario.index') }}"
    class="bg-white border border-slate-200 rounded-xl shadow-sm p-4 sm:p-5 mb-6"
>
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        {{-- BUSCADOR --}}
        <div class="min-w-0 sm:col-span-2">
            <label
                for="buscar-material"
                class="block text-sm font-semibold text-slate-700 mb-2"
            >
                Buscar material
            </label>

            <div class="relative">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 pointer-events-none"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                    aria-hidden="true"
                >
                    <circle cx="10.5" cy="10.5" r="6.5" />
                    <path d="m16 16 5 5" />
                </svg>

                <input
                    id="buscar-material"
                    type="search"
                    name="buscar"
                    value="{{ request('buscar') }}"
                    placeholder="Código o nombre del material..."
                    class="w-full min-w-0 border border-slate-200 rounded-lg bg-white pl-10 pr-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#315875] focus:border-[#315875]"
                >
            </div>
        </div>

        {{-- ESTADO --}}
        <div class="min-w-0">
            <label
                for="estado-material"
                class="block text-sm font-semibold text-slate-700 mb-2"
            >
                Estado
            </label>

            <select
                id="estado-material"
                name="estado"
                class="w-full min-w-0 border border-slate-200 rounded-lg px-4 py-2.5 bg-white text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#315875] focus:border-[#315875]"
            >
                <option value="">Todos</option>

                <option
                    value="activo"
                    @selected(request('estado') === 'activo')
                >
                    Activos
                </option>

                <option
                    value="inactivo"
                    @selected(request('estado') === 'inactivo')
                >
                    Inactivos
                </option>

                <option
                    value="bajo"
                    @selected(request('estado') === 'bajo')
                >
                    Stock bajo
                </option>
            </select>
        </div>

        {{-- BOTONES --}}
        <div class="flex items-end gap-3">
            <a
                href="{{ route('inventario.index') }}"
                class="inline-flex flex-1 items-center justify-center px-4 py-2.5 border border-slate-200 rounded-lg text-[#315875] text-sm font-semibold hover:bg-[#e7eef8] transition-colors"
            >
                Limpiar
            </a>

            <button
                type="submit"
                class="inline-flex flex-1 items-center justify-center px-4 py-2.5 bg-[#315875] hover:bg-[#182d47] text-white rounded-lg text-sm font-semibold transition-colors"
            >
                Filtrar
            </button>
        </div>

    </div>
</form>
    {{-- TABLA --}}
    <div class="bg-white border border-slate-200 rounded-xl
                shadow-sm overflow-hidden">

        <div class="w-full min-w-0 overflow-x-auto" tabindex="0" role="region" aria-label="Listado de materiales">

            <table class="w-full min-w-[900px] text-sm">

                <thead class="bg-[#eef3ff] text-slate-500 uppercase text-xs">

                    <tr>
                        <th class="text-left px-6 py-4">Código</th>
                        <th class="text-left px-6 py-4">Material</th>
                        <th class="text-left px-6 py-4">Unidad</th>
                        <th class="text-left px-6 py-4">Stock actual</th>
                        <th class="text-left px-6 py-4">Stock mínimo</th>
                        <th class="text-left px-6 py-4">Costo</th>
                        <th class="text-left px-6 py-4">Estado</th>
                        <th class="text-left px-6 py-4">Acciones</th>
                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($materiales as $material)

                        <tr class="hover:bg-slate-50">

                            <td class="px-6 py-4 whitespace-nowrap">
                                Q {{ number_format($material->costo_unitario, 2) }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $material->nombre }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $material->unidad_medida }}
                            </td>

                            <td class="px-6 py-4">

                                <span
                                    class="{{ $material->stock_actual <= $material->stock_minimo
                                        ? 'text-red-600 font-bold'
                                        : 'text-slate-700' }}"
                                >
                                    {{ number_format($material->stock_actual, 2) }}
                                </span>

                            </td>

                            <td class="px-6 py-4">
                                {{ number_format($material->stock_minimo, 2) }}
                            </td>

                            <td class="px-6 py-4">
                                Q {{ number_format($material->costo_unitario, 2) }}
                            </td>

                            <td class="px-6 py-4">

                                @if(!$material->estado)

                                    <span class="bg-slate-100 text-slate-600
                                                 px-3 py-1 rounded-full text-xs font-semibold">
                                        Inactivo
                                    </span>

                                @elseif($material->stock_actual <= $material->stock_minimo)

                                    <span class="bg-red-50 text-red-700
                                                 px-3 py-1 rounded-full text-xs font-semibold">
                                        Stock bajo
                                    </span>

                                @else

                                    <span class="bg-emerald-50 text-emerald-700
                                                 px-3 py-1 rounded-full text-xs font-semibold">
                                        Disponible
                                    </span>

                                @endif

                            </td>

                            <td class="px-6 py-4">

                               <div class="flex items-center gap-3 whitespace-nowrap">
                                    <a
                                        href="{{ route('inventario.show', $material) }}"
                                        class="text-[#315875] font-semibold hover:underline"
                                    >
                                        Ver
                                    </a>

                                    <a
                                        href="{{ route('inventario.asignar', $material) }}"
                                        class="text-emerald-700 font-semibold hover:underline"
                                    >
                                        Asignar
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="8"
                                class="px-6 py-12 text-center text-slate-400"
                            >
                                No hay materiales registrados.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($materiales->hasPages())

            <div class="px-6 py-4 border-t border-slate-200">
                {{ $materiales->withQueryString()->onEachSide(1)->links() }}
            </div>

        @endif

    </div>

</div>

@endsection