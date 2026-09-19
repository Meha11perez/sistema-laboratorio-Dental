@extends('layouts.app')

@section('title', 'Inventario | Laboratorio Dental')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

        <div>
            <p class="text-sm font-semibold text-blue-800 uppercase tracking-wide">
                Inventario
            </p>

            <h1 class="text-3xl font-bold text-slate-900 mt-1">
                Inventario General
            </h1>

            <p class="text-slate-500 mt-1">
                Control y consulta de materiales disponibles en el laboratorio.
            </p>
        </div>

        <a
            href="{{ route('inventario.create') }}"
            class="inline-flex items-center justify-center gap-2
                bg-blue-800 hover:bg-blue-900
                text-white px-5 py-3 rounded-lg
                font-semibold text-sm transition"
        >
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
        class="bg-white border border-slate-200 rounded-xl
               shadow-sm p-5 mb-6"
    >

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Buscar material
                </label>

                <input
                    type="text"
                    name="buscar"
                    value="{{ request('buscar') }}"
                    placeholder="Código o nombre..."
                    class="w-full border border-slate-300 rounded-lg
                           px-4 py-2.5"
                >
            </div>


            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Estado
                </label>

                <select
                    name="estado"
                    class="w-full border border-slate-300 rounded-lg
                           px-4 py-2.5 bg-white"
                >

                    <option value="">
                        Todos
                    </option>

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


            <div class="flex items-end gap-3">

                <a
                    href="{{ route('inventario.index') }}"
                    class="px-5 py-2.5 border border-slate-300
                           rounded-lg text-slate-600 font-semibold"
                >
                    Limpiar
                </a>

                <button
                    type="submit"
                    class="px-6 py-2.5 bg-blue-800
                           hover:bg-blue-900 text-white
                           rounded-lg font-semibold"
                >
                    Filtrar
                </button>

            </div>

        </div>

    </form>


    {{-- TABLA --}}
    <div class="bg-white border border-slate-200 rounded-xl
                shadow-sm overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50 text-slate-500 uppercase text-xs">

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

                            <td class="px-6 py-4 font-semibold text-blue-900">
                                {{ $material->codigo ?? '—' }}
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

                               <div class="flex items-center gap-3">
                                <a
                                    href="{{ route('inventario.show', $material) }}"
                                    class="text-blue-700 font-semibold hover:underline"
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
                {{ $materiales->links() }}
            </div>

        @endif

    </div>

</div>

@endsection