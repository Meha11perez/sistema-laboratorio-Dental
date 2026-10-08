@extends('layouts.app')

@section('title', 'Mensajería | Laboratorio Dental')

@section('content')

<div class="w-full min-w-0">

    {{-- =========================================================
         ENCABEZADO
    ========================================================== --}}
    <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
        <div>

            <p class="text-sm font-semibold text-[#315875] uppercase">
                Logística
            </p>

            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                Mensajería
            </h1>

            <p class="text-slate-500 mt-1">
                Control de rutas, recolecciones y entregas.
            </p>

        </div>


        @if(in_array(auth()->user()?->role?->nombre, ['Administrador', 'Recepcion']))
        <a href="{{ route('mensajeria.create') }}" class="inline-flex w-full sm:w-auto shrink-0 items-center
                justify-center gap-2 px-5 py-3
                bg-[#315875] hover:bg-[#182d47]
                text-white rounded-lg font-semibold text-sm
                transition-colors"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-4 h-4"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                aria-hidden="true"
            >
                <path d="M12 5v14M5 12h14" />
            </svg>

            Nueva Ruta
        </a>
        @endif

    </div>


    @if(auth()->user()?->role?->nombre === 'Mensajero')
        <div class="mb-5 flex flex-wrap items-center gap-2">
            <span class="text-sm text-slate-500">Mis rutas del {{ \Carbon\Carbon::parse(request('fecha'))->format('d/m/Y') }}</span>
            @foreach(['Ayer' => -1, 'Hoy' => 0, 'Mañana' => 1] as $etiqueta => $dias)
                <a class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-[#315875] hover:bg-slate-50"
                    href="{{ route('mensajeria.index', ['fecha' => now('America/Guatemala')->addDays($dias)->toDateString()]) }}">{{ $etiqueta }}</a>
            @endforeach
            <span class="text-xs text-slate-500">Para otra fecha, utilice el filtro.</span>
        </div>
    @endif

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

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

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
                    class="w-full min-w-0 border border-slate-200 rounded-lg bg-white px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#315875] focus:border-[#315875]">

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
                    class="w-full min-w-0 border border-slate-200 rounded-lg bg-white px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#315875] focus:border-[#315875]">

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


        <div class="flex flex-col sm:flex-row sm:justify-end gap-3 mt-5">

            <a
                href="{{ route('mensajeria.index') }}"
                class="inline-flex items-center justify-center
                    px-5 py-2.5 border border-slate-200
                    rounded-lg text-[#315875] text-sm font-semibold
                    hover:bg-[#e7eef8] transition-colors"
            >
                Limpiar
            </a>

            <button
                type="submit"
                class="inline-flex items-center justify-center
                    px-6 py-2.5 bg-[#315875] hover:bg-[#182d47]
                    text-white rounded-lg text-sm font-semibold
                    transition-colors"
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


        <div class="w-full min-w-0 overflow-x-auto" tabindex="0" role="region" aria-label="Listado de rutas de mensajería">
            
            <table class="w-full min-w-[900px] text-sm">

                <thead class="bg-[#eef3ff] text-slate-500 uppercase text-xs">

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
                                               bg-[#e7eef8]
                                               text-[#315875]
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

                {{ $rutas->withQueryString()->onEachSide(1)->links() }}

            </div>

        @endif

    </section>

</div>

@endsection