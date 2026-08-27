@extends('layouts.app')

@section('title', 'Garantías y Devoluciones')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- =========================================================
         ENCABEZADO
    ========================================================== --}}
    <div class="mb-8">

        <p class="text-sm font-semibold text-blue-800 uppercase">
            Producción
        </p>

        <h1 class="text-3xl font-bold text-slate-900">
            Garantías y Devoluciones
        </h1>

        <p class="text-slate-500 mt-1">
            Seguimiento de garantías, devoluciones y repeticiones.
        </p>

    </div>


    {{-- =========================================================
     TARJETAS DE RESUMEN
========================================================== --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-5 mb-8">

    {{-- GARANTÍAS VIGENTES --}}
    <div class="bg-white border-l-4 border-emerald-600
                rounded-xl shadow-sm p-5">

        <p class="text-xs uppercase text-slate-500 font-semibold">
            Garantías vigentes
        </p>

        <p class="text-3xl font-bold text-emerald-700 mt-2">
            {{ $garantiasActivas }}
        </p>

        <p class="text-xs text-slate-400 mt-2">
            Más de 15 días antes del vencimiento.
        </p>

    </div>


    {{-- POR VENCER --}}
    <div class="bg-white border-l-4 border-amber-500
                rounded-xl shadow-sm p-5">

        <p class="text-xs uppercase text-slate-500 font-semibold">
            Por vencer
        </p>

        <p class="text-3xl font-bold text-amber-700 mt-2">
            {{ $garantiasPorVencer }}
        </p>

        <p class="text-xs text-slate-400 mt-2">
            Vencen dentro de los próximos 15 días.
        </p>

    </div>


    {{-- VENCIDAS --}}
    <div class="bg-white border-l-4 border-red-600
                rounded-xl shadow-sm p-5">

        <p class="text-xs uppercase text-slate-500 font-semibold">
            Garantías vencidas
        </p>

        <p class="text-3xl font-bold text-red-700 mt-2">
            {{ $garantiasVencidas }}
        </p>

        <p class="text-xs text-slate-400 mt-2">
            Garantías fuera del período establecido.
        </p>

    </div>


    {{-- DEVOLUCIONES --}}
    <div class="bg-white border-l-4 border-purple-600
                rounded-xl shadow-sm p-5">

        <p class="text-xs uppercase text-slate-500 font-semibold">
            Devoluciones
        </p>

        <p class="text-3xl font-bold text-purple-700 mt-2">
            {{ $totalDevoluciones }}
        </p>

        <p class="text-xs text-slate-400 mt-2">
            Incidencias registradas.
        </p>

    </div>


    {{-- REPETICIONES --}}
    <div class="bg-white border-l-4 border-blue-700
                rounded-xl shadow-sm p-5">

        <p class="text-xs uppercase text-slate-500 font-semibold">
            Repeticiones
        </p>

        <p class="text-3xl font-bold text-blue-700 mt-2">
            {{ $totalRepeticiones }}
        </p>

        <p class="text-xs text-slate-400 mt-2">
            Órdenes generadas nuevamente.
        </p>

    </div>

</div>

    {{-- =========================================================
         FILTROS
    ========================================================== --}}
    <form
        method="GET"
        action="{{ route('garantias.index') }}"
        class="bg-white border border-slate-200
               rounded-xl shadow-sm p-5 mb-8"
    >

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

            {{-- TIPO --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
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
                        value="Devolución"
                        @selected(request('tipo') === 'Devolución')
                    >
                        Devolución
                    </option>

                    <option
                        value="Repetición"
                        @selected(request('tipo') === 'Repetición')
                    >
                        Repetición
                    </option>

                    <option
                        value="Corrección"
                        @selected(request('tipo') === 'Corrección')
                    >
                        Corrección
                    </option>

                </select>

            </div>


            {{-- ESTADO --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
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

                    <option value="Registrada"
                        @selected(request('estado') === 'Registrada')>
                        Registrada
                    </option>

                    <option value="En revisión"
                        @selected(request('estado') === 'En revisión')>
                        En revisión
                    </option>

                    <option value="En corrección"
                        @selected(request('estado') === 'En corrección')>
                        En corrección
                    </option>

                    <option value="Resuelta"
                        @selected(request('estado') === 'Resuelta')>
                        Resuelta
                    </option>

                    <option value="Rechazada"
                        @selected(request('estado') === 'Rechazada')>
                        Rechazada
                    </option>

                </select>

            </div>


            {{-- GARANTÍA --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Garantía
                </label>

                <select
                    name="garantia"
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-2.5 bg-white"
                >

                    <option value="">
                        Todas
                    </option>

                    <option
                        value="si"
                        @selected(request('garantia') === 'si')
                    >
                        Con garantía
                    </option>

                    <option
                        value="no"
                        @selected(request('garantia') === 'no')
                    >
                        Sin garantía
                    </option>

                </select>

            </div>


            {{-- FECHA --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
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
                href="{{ route('garantias.index') }}"
                class="px-5 py-2.5 border border-slate-300
                       rounded-lg font-semibold text-slate-600
                       hover:bg-slate-50"
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

    </form>


    {{-- =========================================================
         TABLA
    ========================================================== --}}
    <section class="bg-white border border-slate-200
                    rounded-xl shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-200">

            <h2 class="text-lg font-bold text-slate-900">
                Registro de Incidencias
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Devoluciones, correcciones y trabajos que requieren repetición.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50 text-slate-500 uppercase text-xs">

                    <tr>
                        <th class="text-left px-6 py-4">Orden</th>
                        <th class="text-left px-6 py-4">Paciente</th>
                        <th class="text-left px-6 py-4">Odontólogo</th>
                        <th class="text-left px-6 py-4">Tipo</th>
                        <th class="text-left px-6 py-4">Motivo</th>
                        <th class="text-left px-6 py-4">Fecha</th>
                        <th class="text-left px-6 py-4">Garantía</th>
                        <th class="text-left px-6 py-4">Estado</th>
                        <th class="text-left px-6 py-4">Acción</th>
                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($devoluciones as $devolucion)

                        <tr class="hover:bg-slate-50">

                            {{-- ORDEN --}}
                            <td class="px-6 py-4 font-semibold text-blue-900">
                                {{ $devolucion->ordenTrabajo?->codigo ?? '—' }}
                            </td>


                            {{-- PACIENTE --}}
                            <td class="px-6 py-4">
                                {{ $devolucion->ordenTrabajo?->paciente?->nombre ?? '—' }}
                                {{ $devolucion->ordenTrabajo?->paciente?->apellido ?? '' }}
                            </td>


                            {{-- ODONTÓLOGO --}}
                            <td class="px-6 py-4">
                                {{ $devolucion->ordenTrabajo?->odontologo?->nombre ?? '—' }}
                            </td>


                            {{-- TIPO --}}
                            <td class="px-6 py-4">

                                @if($devolucion->tipo === 'Devolución')

                                    <span class="bg-purple-100 text-purple-700
                                                 px-3 py-1 rounded-full
                                                 text-xs font-semibold">
                                        Devolución
                                    </span>

                                @elseif($devolucion->tipo === 'Repetición')

                                    <span class="bg-amber-100 text-amber-700
                                                 px-3 py-1 rounded-full
                                                 text-xs font-semibold">
                                        ↻ Repetición
                                    </span>

                                @else

                                    <span class="bg-blue-100 text-blue-700
                                                 px-3 py-1 rounded-full
                                                 text-xs font-semibold">
                                        Corrección
                                    </span>

                                @endif

                            </td>


                            {{-- MOTIVO --}}
                            <td class="px-6 py-4">
                                {{ $devolucion->motivo }}
                            </td>


                            {{-- FECHA --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $devolucion->fecha_devolucion?->format('d/m/Y') ?? '—' }}
                            </td>

                            {{-- GARANTÍA --}}
                            <td class="px-6 py-4">

                                @if($devolucion->garantia)

                                    @php
                                        $estadoGarantia = $devolucion->garantia->estado_actual;
                                    @endphp

                                    @if($estadoGarantia === 'Vigente')

                                        <span class="inline-flex bg-emerald-100 text-emerald-700
                                                    px-3 py-1 rounded-full text-xs font-semibold">
                                            Vigente
                                        </span>

                                    @elseif($estadoGarantia === 'Por vencer')

                                        <span class="inline-flex bg-amber-100 text-amber-700
                                                    px-3 py-1 rounded-full text-xs font-semibold">
                                            Por vencer
                                        </span>

                                    @elseif($estadoGarantia === 'Vencida')

                                        <span class="inline-flex bg-red-100 text-red-700
                                                    px-3 py-1 rounded-full text-xs font-semibold">
                                            Vencida
                                        </span>

                                    @else

                                        <span class="inline-flex bg-slate-100 text-slate-600
                                                    px-3 py-1 rounded-full text-xs font-semibold">
                                            {{ $estadoGarantia }}
                                        </span>

                                    @endif

                                @else

                                    <span class="inline-flex bg-slate-100 text-slate-500
                                                px-3 py-1 rounded-full text-xs font-semibold">
                                        Sin garantía
                                    </span>

                                @endif

                            </td>


                            {{-- ESTADO --}}
                            <td class="px-6 py-4">
                                <span class="bg-slate-100 text-slate-700
                                             px-3 py-1 rounded-full
                                             text-xs font-semibold">
                                    {{ $devolucion->estado }}
                                </span>
                            </td>


                            {{-- ACCIÓN --}}
                            <td class="px-6 py-4">

                                @if($devolucion->ordenTrabajo)

                                    <a
                                        href="{{ route(
                                            'ordenes.show',
                                            $devolucion->ordenTrabajo
                                        ) }}"
                                        class="text-blue-700 font-semibold hover:underline"
                                    >
                                        Ver orden
                                    </a>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="9"
                                class="px-6 py-12 text-center text-slate-400"
                            >
                                No hay devoluciones registradas.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINACIÓN --}}
        @if($devoluciones->hasPages())

            <div class="px-6 py-4 border-t border-slate-200">
                {{ $devoluciones->links() }}
            </div>

        @endif

    </section>

</div>

@endsection