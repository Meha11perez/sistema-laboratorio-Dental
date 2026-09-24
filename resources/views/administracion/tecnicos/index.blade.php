@extends('layouts.app')

@section('title', 'Técnicos | Laboratorio Dental')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col md:flex-row
                md:items-center md:justify-between
                gap-4 mb-8">

        <div>

            <p class="text-sm font-semibold
                      text-blue-800 uppercase">
                Administración
            </p>

            <h1 class="text-3xl font-bold text-slate-900">
                Técnicos
            </h1>

            <p class="text-slate-500 mt-1">
                Gestión del personal técnico del laboratorio.
            </p>

        </div>

        <button
            type="button"
            class="bg-blue-800
                   text-white
                   px-5 py-2.5
                   rounded-lg
                   font-semibold
                   opacity-60 cursor-not-allowed"
        >
            + Nuevo Técnico
        </button>

    </div>


    {{-- INDICADORES --}}
    <div class="grid grid-cols-1
                md:grid-cols-2
                xl:grid-cols-4
                gap-5 mb-8">

        <div class="bg-white
                    border border-slate-200
                    border-l-4 border-l-blue-600
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase
                      font-semibold text-slate-500">
                Total técnicos
            </p>

            <p class="text-3xl font-bold
                      text-blue-700 mt-2">
                {{ $totalTecnicos }}
            </p>

        </div>


        <div class="bg-white
                    border border-slate-200
                    border-l-4 border-l-emerald-500
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase
                      font-semibold text-slate-500">
                Activos
            </p>

            <p class="text-3xl font-bold
                      text-emerald-600 mt-2">
                {{ $tecnicosActivos }}
            </p>

        </div>


        <div class="bg-white
                    border border-slate-200
                    border-l-4 border-l-red-500
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase
                      font-semibold text-slate-500">
                Inactivos
            </p>

            <p class="text-3xl font-bold
                      text-red-600 mt-2">
                {{ $tecnicosInactivos }}
            </p>

        </div>


        <div class="bg-white
                    border border-slate-200
                    border-l-4 border-l-violet-500
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase
                      font-semibold text-slate-500">
                Con etapas asignadas
            </p>

            <p class="text-3xl font-bold
                      text-violet-600 mt-2">
                {{ $tecnicosConEtapas }}
            </p>

        </div>

    </div>


    {{-- FILTROS --}}
    <form
        method="GET"
        action="{{ route('administracion.tecnicos.index') }}"
        class="bg-white
               border border-slate-200
               rounded-xl shadow-sm
               p-5 mb-8"
    >

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>

                <label class="block text-xs
                              font-semibold
                              text-slate-600 mb-2">
                    Buscar
                </label>

                <input
                    type="text"
                    name="buscar"
                    value="{{ request('buscar') }}"
                    placeholder="Nombre, correo, especialidad..."
                    class="w-full
                           border border-slate-300
                           rounded-lg
                           px-4 py-2.5"
                >

            </div>


            <div>

                <label class="block text-xs
                              font-semibold
                              text-slate-600 mb-2">
                    Estado
                </label>

                <select
                    name="estado"
                    class="w-full
                           border border-slate-300
                           rounded-lg
                           px-4 py-2.5
                           bg-white"
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

                </select>

            </div>

        </div>


        <div class="flex justify-end gap-3 mt-5">

            <a
                href="{{ route('administracion.tecnicos.index') }}"
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
                Filtrar
            </button>

        </div>

    </form>


    {{-- TABLA --}}
    <div class="bg-white
                border border-slate-200
                rounded-xl
                shadow-sm
                overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-200">

            <h2 class="font-bold text-slate-900">
                Personal técnico
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Técnicos registrados y etapas de producción asignadas.
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

                        <th class="text-left px-6 py-4">
                            Teléfono
                        </th>

                        <th class="text-left px-6 py-4">
                            Fecha ingreso
                        </th>

                        <th class="text-center px-6 py-4">
                            Etapas
                        </th>

                        <th class="text-left px-6 py-4">
                            Estado
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

                                <p class="text-xs text-slate-400 mt-1">
                                    {{ $tecnico->user?->email ?? '—' }}
                                </p>

                            </td>


                            {{-- ESPECIALIDAD --}}
                            <td class="px-6 py-4 text-slate-600">

                                {{ $tecnico->especialidad
                                    ?: 'Sin especialidad' }}

                            </td>


                            {{-- TELÉFONO --}}
                            <td class="px-6 py-4 text-slate-600">

                                {{ $tecnico->telefono ?: '—' }}

                            </td>


                            {{-- FECHA --}}
                            <td class="px-6 py-4 text-slate-600">

                                {{ $tecnico->fecha_ingreso
                                    ?->format('d/m/Y') ?? '—' }}

                            </td>


                            {{-- ETAPAS --}}
                            <td class="px-6 py-4 text-center">

                                <span class="inline-flex
                                             bg-violet-50
                                             text-violet-700
                                             px-3 py-1
                                             rounded-full
                                             text-xs
                                             font-semibold">

                                    {{ $tecnico->etapas_produccion_count }}

                                </span>

                            </td>


                            {{-- ESTADO --}}
                            <td class="px-6 py-4">

                                @if($tecnico->estado)

                                    <span class="inline-flex
                                                 bg-emerald-100
                                                 text-emerald-700
                                                 px-3 py-1
                                                 rounded-full
                                                 text-xs
                                                 font-semibold">
                                        Activo
                                    </span>

                                @else

                                    <span class="inline-flex
                                                 bg-red-100
                                                 text-red-700
                                                 px-3 py-1
                                                 rounded-full
                                                 text-xs
                                                 font-semibold">
                                        Inactivo
                                    </span>

                                @endif

                            </td>


                            {{-- ACCIÓN --}}
                            <td class="px-6 py-4 text-right">

                                <span class="text-xs text-slate-400">
                                    Próximamente
                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="px-6 py-12
                                       text-center
                                       text-slate-400"
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