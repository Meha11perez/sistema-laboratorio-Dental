@extends('layouts.app')

@section('title', 'Inventario del Técnico | Laboratorio Dental')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- ENCABEZADO --}}
    <div class="mb-8">

        <a
            href="{{ route('inventario.tecnicos') }}"
            class="text-sm font-semibold
                   text-blue-700 hover:underline"
        >
            ← Volver a Inventario por Técnico
        </a>


        <p class="text-sm font-semibold
                  text-blue-800 uppercase mt-5">
            Inventario
        </p>

        <h1 class="text-3xl font-bold text-slate-900">
            {{ $tecnico->user?->name ?? 'Técnico' }}
        </h1>

        <p class="text-slate-500 mt-1">

            {{ $tecnico->especialidad ?? 'Sin especialidad registrada' }}

            · {{ $totalMateriales }}
            material(es) con existencia

        </p>

    </div>


    {{-- BUSCADOR --}}
    <form
        method="GET"
        action="{{ route(
            'inventario.tecnicos.show',
            $tecnico
        ) }}"
        class="bg-white border border-slate-200
               rounded-xl p-5 shadow-sm mb-8"
    >

        <div class="flex flex-col md:flex-row gap-3">

            <div class="flex-1">

                <label class="block text-xs
                              font-semibold text-slate-600 mb-2">
                    Buscar material
                </label>

                <input
                    type="text"
                    name="buscar"
                    value="{{ request('buscar') }}"
                    placeholder="Código o nombre..."
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-2.5"
                >

            </div>


            <div class="flex items-end gap-2">

                <a
                    href="{{ route(
                        'inventario.tecnicos.show',
                        $tecnico
                    ) }}"
                    class="px-5 py-2.5
                           border border-slate-300
                           rounded-lg
                           text-slate-600
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
                    Buscar
                </button>

            </div>

        </div>

    </form>


    {{-- INVENTARIO --}}
    <div class="bg-white border border-slate-200
                rounded-xl shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-200">

            <h2 class="font-bold text-slate-900">
                Materiales asignados
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Existencia actual bajo responsabilidad del técnico.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50
                              text-xs text-slate-500 uppercase">

                    <tr>

                        <th class="text-left px-6 py-4">
                            Código
                        </th>

                        <th class="text-left px-6 py-4">
                            Material
                        </th>

                        <th class="text-left px-6 py-4">
                            Unidad
                        </th>

                        <th class="text-right px-6 py-4">
                            Disponible
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($inventarioTecnico as $inventario)

                        <tr class="hover:bg-slate-50">

                            <td class="px-6 py-4
                                       text-blue-700 font-medium">

                                {{ $inventario->material?->codigo ?? '—' }}

                            </td>


                            <td class="px-6 py-4
                                       font-semibold text-slate-800">

                                {{ $inventario->material?->nombre ?? '—' }}

                            </td>


                            <td class="px-6 py-4 text-slate-600">

                                {{ $inventario->material?->unidad_medida ?? '—' }}

                            </td>


                            <td class="px-6 py-4 text-right">

                                @if((float) $inventario->cantidad > 0)

                                    <span class="inline-flex
                                                 bg-emerald-100
                                                 text-emerald-700
                                                 px-3 py-1
                                                 rounded-full
                                                 text-xs font-semibold">

                                        {{ number_format(
                                            $inventario->cantidad,
                                            2
                                        ) }}

                                    </span>

                                @else

                                    <span class="inline-flex
                                                 bg-red-100
                                                 text-red-700
                                                 px-3 py-1
                                                 rounded-full
                                                 text-xs font-semibold">
                                        Sin existencia
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="4"
                                class="px-6 py-12
                                       text-center text-slate-400"
                            >
                                Este técnico no tiene materiales asignados.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($inventarioTecnico->hasPages())

            <div class="px-6 py-4 border-t border-slate-200">
                {{ $inventarioTecnico->links() }}
            </div>

        @endif

    </div>

</div>

@endsection