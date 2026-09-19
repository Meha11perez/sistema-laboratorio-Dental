@extends('layouts.app')

@section('title', 'Mi Inventario | Laboratorio Dental')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- ENCABEZADO --}}
    <div class="mb-7">

        <p class="text-xs font-semibold text-blue-700 uppercase tracking-widest">
            Inventario
        </p>

        <h1 class="text-3xl font-bold text-slate-900 mt-1">
            Mi inventario
        </h1>

        <p class="text-sm text-slate-500 mt-1">
            Materiales actualmente asignados para tus trabajos.
        </p>

    </div>


    {{-- BUSCADOR --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5 mb-6 shadow-sm">

        <form
            method="GET"
            action="{{ route('inventario.index') }}"
            class="flex flex-col sm:flex-row gap-3"
        >

            <div class="flex-1">

                <label class="block text-xs font-medium text-slate-600 mb-2">
                    Buscar material
                </label>

                <input
                    type="text"
                    name="buscar"
                    value="{{ request('buscar') }}"
                    placeholder="Código o nombre..."
                    class="
                        w-full
                        rounded-lg
                        border-slate-300
                        focus:border-blue-500
                        focus:ring-blue-500
                    "
                >

            </div>


            <div class="flex items-end gap-2">

                <button
                    type="submit"
                    class="
                        px-5 py-2.5
                        bg-blue-700
                        text-white
                        font-semibold
                        rounded-lg
                        hover:bg-blue-800
                        transition
                    "
                >
                    Filtrar
                </button>

                <a
                    href="{{ route('inventario.index') }}"
                    class="
                        px-5 py-2.5
                        border border-slate-300
                        text-slate-600
                        rounded-lg
                        hover:bg-slate-50
                    "
                >
                    Limpiar
                </a>

            </div>

        </form>

    </div>


    {{-- INVENTARIO --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="px-5 py-4 border-b border-slate-200">

            <h2 class="font-bold text-slate-900">
                Materiales asignados
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                {{ $inventarioTecnico->total() }}
                material(es) disponible(s)
            </p>

        </div>


        {{-- ESCRITORIO --}}
        <div class="hidden md:block overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50 text-xs text-slate-500 uppercase">

                    <tr>
                        <th class="text-left px-5 py-4">
                            Código
                        </th>

                        <th class="text-left px-5 py-4">
                            Material
                        </th>

                        <th class="text-left px-5 py-4">
                            Unidad
                        </th>

                        <th class="text-right px-5 py-4">
                            Disponible
                        </th>
                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($inventarioTecnico as $inventario)

                        <tr>

                            <td class="px-5 py-4 text-blue-700 font-medium">
                                {{ $inventario->material?->codigo }}
                            </td>

                            <td class="px-5 py-4 font-medium text-slate-800">
                                {{ $inventario->material?->nombre }}
                            </td>

                            <td class="px-5 py-4 text-slate-600">
                                {{ $inventario->material?->unidad_medida }}
                            </td>

                            <td class="px-5 py-4 text-right">

                                <span
                                    class="
                                        inline-flex
                                        px-3 py-1
                                        rounded-full
                                        text-xs
                                        font-semibold

                                        {{ $inventario->cantidad > 0
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-red-100 text-red-700'
                                        }}
                                    "
                                >
                                    {{ number_format($inventario->cantidad, 2) }}
                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="4"
                                class="px-5 py-10 text-center text-slate-500"
                            >
                                No tienes materiales asignados actualmente.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- MÓVIL --}}
        <div class="md:hidden p-4 space-y-3">

            @forelse($inventarioTecnico as $inventario)

                <div
                    class="
                        border border-slate-200
                        rounded-xl
                        p-4
                    "
                >

                    <div class="flex items-start justify-between gap-3">

                        <div>

                            <p class="text-xs text-slate-400">
                                {{ $inventario->material?->codigo }}
                            </p>

                            <p class="font-bold text-slate-900">
                                {{ $inventario->material?->nombre }}
                            </p>

                            <p class="text-sm text-slate-500 mt-1">
                                {{ $inventario->material?->unidad_medida }}
                            </p>

                        </div>


                        <span
                            class="
                                px-3 py-1
                                rounded-full
                                text-sm
                                font-semibold
                                bg-green-100
                                text-green-700
                            "
                        >
                            {{ number_format($inventario->cantidad, 2) }}
                        </span>

                    </div>

                </div>

            @empty

                <div class="text-center text-slate-500 py-10">
                    No tienes materiales asignados actualmente.
                </div>

            @endforelse

        </div>


        @if($inventarioTecnico->hasPages())

            <div class="px-5 py-4 border-t border-slate-200">

                {{ $inventarioTecnico->links() }}

            </div>

        @endif

    </div>

</div>

@endsection