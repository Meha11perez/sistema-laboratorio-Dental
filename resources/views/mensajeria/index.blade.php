@extends('layouts.app')

@section('title', 'Mensajería | Laboratorio Dental')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- =========================================================
         ENCABEZADO
    ========================================================== --}}
    <div class="flex flex-col md:flex-row
                md:items-center md:justify-between
                gap-4 mb-8">

        <div>

            <p class="text-sm font-semibold text-blue-800 uppercase">
                Logística
            </p>

            <h1 class="text-3xl font-bold text-slate-900">
                Mensajería
            </h1>

            <p class="text-slate-500 mt-1">
                Control de rutas, recolecciones y entregas.
            </p>

        </div>


        <a
            href="{{ route('mensajeria.create') }}"
            class="inline-flex items-center justify-center
                   px-5 py-3 bg-blue-800 hover:bg-blue-900
                   text-white rounded-lg font-semibold text-sm"
        >
            + Nueva Ruta
        </a>

    </div>


    {{-- =========================================================
         MENSAJES
    ========================================================== --}}
    @if(session('success'))

        <div class="mb-6 bg-emerald-50
                    border border-emerald-200
                    text-emerald-700
                    rounded-xl px-5 py-4">

            {{ session('success') }}

        </div>

    @endif


    {{-- =========================================================
         FILTROS
    ========================================================== --}}
    <form
        method="GET"
        action="{{ route('mensajeria.index') }}"
        class="bg-white border border-slate-200
               rounded-xl shadow-sm p-5 mb-8"
    >

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- FECHA --}}
            <div>

                <label
                    class="block text-sm font-semibold
                           text-slate-700 mb-2"
                >
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


            {{-- ESTADO --}}
            <div>

                <label
                    class="block text-sm font-semibold
                           text-slate-700 mb-2"
                >
                    Estado
                </label>

                <select
                    name="estado"
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-2.5 bg-white"
                >

                    <option value="">
                        Todos
                    </option>

                    <option
                        value="Pendiente"
                        @selected(request('estado') === 'Pendiente')
                    >
                        Pendiente
                    </option>

                    <option
                        value="En ruta"
                        @selected(request('estado') === 'En ruta')
                    >
                        En ruta
                    </option>

                    <option
                        value="Finalizada"
                        @selected(request('estado') === 'Finalizada')
                    >
                        Finalizada
                    </option>

                    <option
                        value="Cancelada"
                        @selected(request('estado') === 'Cancelada')
                    >
                        Cancelada
                    </option>

                </select>

            </div>

        </div>


        <div class="flex justify-end gap-3 mt-5">

            <a
                href="{{ route('mensajeria.index') }}"
                class="px-5 py-2.5
                       border border-slate-300
                       rounded-lg font-semibold
                       text-slate-600 hover:bg-slate-50"
            >
                Limpiar
            </a>


            <button
                type="submit"
                class="px-6 py-2.5
                       bg-blue-800 hover:bg-blue-900
                       text-white rounded-lg font-semibold"
            >
                Filtrar
            </button>

        </div>

    </form>


    {{-- =========================================================
         TABLA DE RUTAS
    ========================================================== --}}
    <section
        class="bg-white border border-slate-200
               rounded-xl shadow-sm overflow-hidden"
    >

        <div class="px-6 py-5 border-b border-slate-200">

            <h2 class="text-lg font-bold text-slate-900">
                Rutas registradas
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Rutas asignadas a mensajeros.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead
                    class="bg-slate-50
                           text-slate-500 uppercase text-xs"
                >

                    <tr>

                        <th class="text-left px-6 py-4">
                            Fecha
                        </th>

                        <th class="text-left px-6 py-4">
                            Mensajero
                        </th>

                        <th class="text-center px-6 py-4">
                            Visitas
                        </th>

                        <th class="text-left px-6 py-4">
                            Salida
                        </th>

                        <th class="text-left px-6 py-4">
                            Regreso
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

                    @forelse($rutas as $ruta)

                        <tr class="hover:bg-slate-50">

                            {{-- FECHA --}}
                            <td class="px-6 py-4
                                       font-semibold text-slate-900">
                                {{ $ruta->fecha?->format('d/m/Y') ?? '—' }}
                            </td>

                            {{-- MENSAJERO --}}
                            <td class="px-6 py-4">
                                {{ $ruta->mensajero?->name ?? 'Sin asignar' }}
                            </td>

                            {{-- VISITAS --}}
                            <td class="px-6 py-4 text-center font-semibold">
                                {{ $ruta->detalles_count }}
                            </td>
                            {{-- SALIDA --}}
                            <td class="px-6 py-4">
                                {{ $ruta->hora_salida ?? '—' }}
                            </td>

                             {{-- REGRESO --}}
                            <td class="px-6 py-4">
                                {{ $ruta->hora_regreso ?? '—' }}
                            </td>

                            {{-- ESTADO --}}
                            <td class="px-6 py-4">

                                @if($ruta->estado === 'Finalizada')

                                    <span
                                        class="inline-flex
                                               bg-emerald-100
                                               text-emerald-700
                                               px-3 py-1
                                               rounded-full
                                               text-xs font-semibold"
                                    >
                                        Finalizada
                                    </span>

                                @elseif($ruta->estado === 'En ruta')

                                    <span
                                        class="inline-flex
                                               bg-blue-100
                                               text-blue-700
                                               px-3 py-1
                                               rounded-full
                                               text-xs font-semibold"
                                    >
                                        En ruta
                                    </span>

                                @elseif($ruta->estado === 'Cancelada')

                                    <span
                                        class="inline-flex
                                               bg-red-100
                                               text-red-700
                                               px-3 py-1
                                               rounded-full
                                               text-xs font-semibold"
                                    >
                                        Cancelada
                                    </span>

                                @else

                                    <span
                                        class="inline-flex
                                               bg-amber-100
                                               text-amber-700
                                               px-3 py-1
                                               rounded-full
                                               text-xs font-semibold"
                                    >
                                        Pendiente
                                    </span>

                                @endif

                            </td>
                            {{-- ACCIÓN --}}
                            <td class="px-6 py-4 text-right">

                                <a
                                    href="{{ route(
                                        'mensajeria.show',
                                        $ruta
                                    ) }}"
                                    class="text-blue-700
                                           font-semibold
                                           hover:underline"
                                >
                                    Ver ruta
                                </a>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="px-6 py-12
                                       text-center text-slate-400"
                            >
                                No hay rutas de mensajería registradas.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- =====================================================
             PAGINACIÓN
        ====================================================== --}}
        @if($rutas->hasPages())

            <div class="px-6 py-4 border-t border-slate-200">

                {{ $rutas->links() }}

            </div>

        @endif

    </section>

</div>

@endsection