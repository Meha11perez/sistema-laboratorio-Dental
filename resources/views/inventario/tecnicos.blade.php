@extends('layouts.app')

@section('title', 'Inventario por Técnico | Laboratorio Dental')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- ENCABEZADO --}}
    <div class="mb-8">

        <p class="text-sm font-semibold text-blue-800 uppercase">
            Inventario
        </p>

        <h1 class="text-3xl font-bold text-slate-900">
            Inventario por Técnico
        </h1>

        <p class="text-slate-500 mt-1">
            Consulta los materiales actualmente asignados
            a cada técnico del laboratorio.
        </p>

    </div>


    {{-- INDICADORES --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

        {{-- TÉCNICOS ACTIVOS --}}
        <div class="bg-white border border-slate-200
                    border-l-4 border-l-blue-600
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase font-semibold text-slate-500">
                Técnicos activos
            </p>

            <p class="text-3xl font-bold text-blue-700 mt-2">
                {{ $totalTecnicos }}
            </p>

            <p class="text-xs text-slate-400 mt-1">
                Personal técnico registrado
            </p>

        </div>


        {{-- CON INVENTARIO --}}
        <div class="bg-white border border-slate-200
                    border-l-4 border-l-emerald-500
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase font-semibold text-slate-500">
                Con inventario
            </p>

            <p class="text-3xl font-bold text-emerald-600 mt-2">
                {{ $tecnicosConInventario }}
            </p>

            <p class="text-xs text-slate-400 mt-1">
                Técnicos con materiales asignados
            </p>

        </div>


        {{-- SIN INVENTARIO --}}
        <div class="bg-white border border-slate-200
                    border-l-4 border-l-amber-500
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase font-semibold text-slate-500">
                Sin inventario
            </p>

            <p class="text-3xl font-bold text-amber-600 mt-2">
                {{ $tecnicosSinInventario }}
            </p>

            <p class="text-xs text-slate-400 mt-1">
                Sin materiales disponibles
            </p>

        </div>


        {{-- MATERIALES --}}
        <div class="bg-white border border-slate-200
                    border-l-4 border-l-violet-500
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase font-semibold text-slate-500">
                Materiales asignados
            </p>

            <p class="text-3xl font-bold text-violet-600 mt-2">
                {{ $materialesAsignados }}
            </p>

            <p class="text-xs text-slate-400 mt-1">
                Tipos distintos de material
            </p>

        </div>

    </div>


    {{-- BUSCADOR --}}
    <form
        method="GET"
        action="{{ route('inventario.tecnicos') }}"
        class="bg-white border border-slate-200
               rounded-xl shadow-sm p-5 mb-8"
    >

        <div class="flex flex-col md:flex-row gap-3">

            <div class="flex-1">

                <label class="block text-xs font-semibold
                              text-slate-600 mb-2">
                    Buscar técnico
                </label>

                <input
                    type="text"
                    name="buscar"
                    value="{{ request('buscar') }}"
                    placeholder="Nombre o especialidad..."
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-2.5"
                >

            </div>


            <div class="flex items-end gap-2">

                <a
                    href="{{ route('inventario.tecnicos') }}"
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
                           bg-blue-800 text-white
                           rounded-lg font-semibold
                           hover:bg-blue-900"
                >
                    Buscar
                </button>

            </div>

        </div>

    </form>


    {{-- LISTADO --}}
    <div class="bg-white border border-slate-200
                rounded-xl shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-200">

            <h2 class="font-bold text-slate-900">
                Técnicos
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Consulta el inventario actualmente
                bajo responsabilidad de cada técnico.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50
                              text-xs uppercase
                              text-slate-500">

                    <tr>

                        <th class="text-left px-6 py-4">
                            Técnico
                        </th>

                        <th class="text-left px-6 py-4">
                            Especialidad
                        </th>

                        <th class="text-center px-6 py-4">
                            Materiales
                        </th>

                        <th class="text-right px-6 py-4">
                            Acción
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($tecnicos as $tecnico)

                        <tr class="hover:bg-slate-50">

                            {{-- TÉCNICO --}}
                            <td class="px-6 py-4">

                                <p class="font-semibold text-slate-900">
                                    {{ $tecnico->user?->name ?? 'Sin usuario' }}
                                </p>

                                @if($tecnico->telefono)

                                    <p class="text-xs text-slate-400 mt-1">
                                        {{ $tecnico->telefono }}
                                    </p>

                                @endif

                            </td>


                            {{-- ESPECIALIDAD --}}
                            <td class="px-6 py-4 text-slate-600">

                                {{ $tecnico->especialidad
                                    ?? 'Sin especialidad' }}

                            </td>


                            {{-- CANTIDAD DE MATERIALES --}}
                            <td class="px-6 py-4 text-center">

                                @if($tecnico->materiales_asignados > 0)

                                    <span class="inline-flex
                                                 bg-emerald-100
                                                 text-emerald-700
                                                 px-3 py-1
                                                 rounded-full
                                                 text-xs font-semibold">

                                        {{ $tecnico->materiales_asignados }}

                                    </span>

                                @else

                                    <span class="inline-flex
                                                 bg-slate-100
                                                 text-slate-500
                                                 px-3 py-1
                                                 rounded-full
                                                 text-xs font-semibold">
                                        0
                                    </span>

                                @endif

                            </td>


                            {{-- ACCIÓN --}}
                            <td class="px-6 py-4 text-right">

                                <a
                                    href="{{ route(
                                        'inventario.tecnicos.show',
                                        $tecnico
                                    ) }}"
                                    class="text-blue-700
                                           font-semibold
                                           hover:underline"
                                >
                                    Ver inventario
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="4"
                                class="px-6 py-12
                                       text-center text-slate-400"
                            >
                                No se encontraron técnicos.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($tecnicos->hasPages())

            <div class="px-6 py-4 border-t border-slate-200">
                {{ $tecnicos->links() }}
            </div>

        @endif

    </div>

</div>

@endsection