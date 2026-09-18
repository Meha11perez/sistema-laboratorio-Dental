@extends('layouts.app')

@section('title', 'Entregas')

@section('content')

<div class="max-w-7xl mx-auto space-y-6">

    <div>
        <h1 class="text-3xl font-bold text-slate-900">
            Entregas
        </h1>

        <p class="text-slate-500 mt-1">
            Seguimiento de trabajos entregados a odontólogos y clínicas.
        </p>
    </div>

    {{-- FILTROS --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">

        <form
            method="GET"
            action="{{ route('mensajeria.entregas') }}"
            class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end"
        >

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">
                    Fecha
                </label>

                <input
                    type="date"
                    name="fecha"
                    value="{{ request('fecha') }}"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm"
                >
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">
                    Estado
                </label>

                <select
                    name="estado"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm"
                >
                    <option value="">Todos</option>

                    @foreach([
                        'Pendiente',
                        'Realizada',
                        'No realizada',
                        'Reprogramada'
                    ] as $estado)

                        <option
                            value="{{ $estado }}"
                            @selected(request('estado') === $estado)
                        >
                            {{ $estado }}
                        </option>

                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">
                    Odontólogo
                </label>

                <select
                    name="odontologo_id"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm"
                >
                    <option value="">Todos</option>

                    @foreach($odontologos as $odontologo)
                        <option
                            value="{{ $odontologo->id }}"
                            @selected(
                                request('odontologo_id') == $odontologo->id
                            )
                        >
                            {{ $odontologo->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">
                    Clínica
                </label>

                <select
                    name="clinica_id"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm"
                >
                    <option value="">Todas</option>

                    @foreach($clinicas as $clinica)
                        <option
                            value="{{ $clinica->id }}"
                            @selected(
                                request('clinica_id') == $clinica->id
                            )
                        >
                            {{ $clinica->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-700 text-white
                           rounded-lg text-sm font-semibold
                           hover:bg-blue-800"
                >
                    Filtrar
                </button>

                <a
                    href="{{ route('mensajeria.entregas') }}"
                    class="px-4 py-2 border border-slate-300
                           rounded-lg text-sm font-semibold
                           text-slate-600 hover:bg-slate-50"
                >
                    Limpiar
                </a>
            </div>

        </form>

    </div>

    {{-- TABLA --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-200">

            <h2 class="text-lg font-bold text-slate-900">
                Historial de Entregas
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                {{ $entregas->total() }}
                entrega(s) encontrada(s)
            </p>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                    <tr>
                        <th class="text-left px-6 py-4">Fecha</th>
                        <th class="text-left px-6 py-4">Ruta</th>
                        <th class="text-left px-6 py-4">Orden</th>
                        <th class="text-left px-6 py-4">Paciente</th>
                        <th class="text-left px-6 py-4">Odontólogo</th>
                        <th class="text-left px-6 py-4">Clínica</th>
                        <th class="text-left px-6 py-4">Estado</th>
                        <th class="text-left px-6 py-4">Hora</th>
                        <th class="text-right px-6 py-4">Acción</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse($entregas as $detalle)

                        <tr class="hover:bg-slate-50">

                            <td class="px-6 py-4">
                                {{ $detalle->rutaMensajeria?->fecha?->format('d/m/Y') ?? '—' }}
                            </td>

                            <td class="px-6 py-4 font-semibold">
                                Ruta #{{ $detalle->ruta_mensajeria_id }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $detalle->ordenTrabajo?->codigo ?? '—' }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $detalle->ordenTrabajo?->paciente?->nombre ?? '—' }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $detalle->odontologo?->nombre ?? '—' }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $detalle->clinica?->nombre ?? '—' }}
                            </td>

                        <td class="px-6 py-4">

                        @php
                            $atrasada =
                                $detalle->estado === 'Pendiente'
                                && $detalle->rutaMensajeria?->fecha
                                && $detalle->rutaMensajeria->fecha->isBefore(
                                    \Carbon\Carbon::today('America/Guatemala')
                                );

                            if ($atrasada) {
                                $estadoTexto = 'Atrasada';
                                $estadoClase = 'bg-red-100 text-red-700';
                            } else {
                                $estadoTexto = $detalle->estado;

                                $estadoClase = match($detalle->estado) {
                                    'Realizada' =>
                                        'bg-emerald-100 text-emerald-700',

                                    'No realizada' =>
                                        'bg-red-100 text-red-700',

                                    'Reprogramada' =>
                                        'bg-amber-100 text-amber-700',

                                    default =>
                                        'bg-slate-100 text-slate-700',
                                };
                            }
                        @endphp

                        <span
                            class="inline-flex px-3 py-1 rounded-full
                                text-xs font-semibold {{ $estadoClase }}"
                        >
                            {{ $estadoTexto }}
                        </span>
                            </td>

                            <td class="px-6 py-4">
                                {{ $detalle->hora_realizada ?? '—' }}
                            </td>

                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-3">

                            @if($atrasada)
                                <a
                                    href="{{ route(
                                        'mensajeria.detalles.reprogramar.form',
                                        $detalle
                                    ) }}"
                                    class="text-amber-700 font-semibold hover:underline"
                                >
                                    Reprogramar
                                </a>
                            @endif

                            <a
                                href="{{ route(
                                    'mensajeria.entregas.show',
                                    $detalle->id
                                ) }}"
                                class="text-blue-700 font-semibold hover:underline"
                            >
                                Ver detalle
                            </a>

                        </div>
                    </td>
               </tr>

                    @empty

                        <tr>
                            <td
                                colspan="9"
                                class="px-6 py-12 text-center text-slate-400"
                            >
                                No se encontraron entregas.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if($entregas->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $entregas->links() }}
            </div>
        @endif

    </div>

</div>

@endsection