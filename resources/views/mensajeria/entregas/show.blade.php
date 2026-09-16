@extends('layouts.app')

@section('title', 'Detalle de Entrega')

@section('content')

<div class="max-w-5xl mx-auto space-y-6">

    <div class="flex items-center justify-between">

        <div>
            <p class="text-sm font-semibold text-blue-700">
                MENSAJERÍA
            </p>

            <h1 class="text-3xl font-bold text-slate-900">
                Detalle de Entrega
            </h1>

            <p class="text-slate-500 mt-1">
                Información completa de la entrega realizada.
            </p>
        </div>

        <a
            href="{{ route('mensajeria.entregas') }}"
            class="px-4 py-2 border border-slate-300 rounded-lg
                   text-sm font-semibold text-slate-600 hover:bg-slate-50"
        >
            Volver
        </a>

    </div>


    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">

        <div class="flex items-center justify-between mb-6">

            <h2 class="text-lg font-bold text-slate-900">
                Entrega #{{ $detalle->id }}
            </h2>

            @php
                $estadoClase = match($detalle->estado) {
                    'Realizada' => 'bg-emerald-100 text-emerald-700',
                    'No realizada' => 'bg-red-100 text-red-700',
                    'Reprogramada' => 'bg-amber-100 text-amber-700',
                    default => 'bg-slate-100 text-slate-700',
                };
            @endphp

            <span
                class="px-3 py-1 rounded-full text-xs font-semibold {{ $estadoClase }}"
            >
                {{ $detalle->estado }}
            </span>

        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <p class="text-xs uppercase font-semibold text-slate-400">
                    Fecha
                </p>

                <p class="font-semibold text-slate-700">
                    {{ $detalle->rutaMensajeria?->fecha?->format('d/m/Y') ?? '—' }}
                </p>
            </div>


            <div>
                <p class="text-xs uppercase font-semibold text-slate-400">
                    Ruta
                </p>

                <p class="font-semibold text-slate-700">
                    Ruta #{{ $detalle->ruta_mensajeria_id }}
                </p>
            </div>


            <div>
                <p class="text-xs uppercase font-semibold text-slate-400">
                    Mensajero
                </p>

                <p class="font-semibold text-slate-700">
                    {{ $detalle->rutaMensajeria?->mensajero?->name ?? '—' }}
                </p>
            </div>


            <div>
                <p class="text-xs uppercase font-semibold text-slate-400">
                    Hora de entrega
                </p>

                <p class="font-semibold text-slate-700">
                    {{ $detalle->hora_realizada ?? '—' }}
                </p>
            </div>


            <div>
                <p class="text-xs uppercase font-semibold text-slate-400">
                    Orden de trabajo
                </p>

                <p class="font-semibold text-slate-700">
                    {{ $detalle->ordenTrabajo?->codigo ?? '—' }}
                </p>
            </div>


            <div>
                <p class="text-xs uppercase font-semibold text-slate-400">
                    Paciente
                </p>

                <p class="font-semibold text-slate-700">
                    {{ $detalle->ordenTrabajo?->paciente?->nombre ?? '—' }}
                </p>
            </div>


            <div>
                <p class="text-xs uppercase font-semibold text-slate-400">
                    Odontólogo
                </p>

                <p class="font-semibold text-slate-700">
                    {{ $detalle->odontologo?->nombre ?? '—' }}
                </p>
            </div>


            <div>
                <p class="text-xs uppercase font-semibold text-slate-400">
                    Clínica
                </p>

                <p class="font-semibold text-slate-700">
                    {{ $detalle->clinica?->nombre ?? '—' }}
                </p>
            </div>


            <div class="md:col-span-2">
                <p class="text-xs uppercase font-semibold text-slate-400">
                    Dirección
                </p>

                <p class="font-semibold text-slate-700">
                    {{ $detalle->direccion_referencia ?? '—' }}
                </p>
            </div>


            <div>
                <p class="text-xs uppercase font-semibold text-slate-400">
                    Recibido por
                </p>

                <p class="font-semibold text-slate-700">
                    {{ $detalle->recibido_por ?? '—' }}
                </p>
            </div>


            <div class="md:col-span-2">
                <p class="text-xs uppercase font-semibold text-slate-400">
                    Observaciones
                </p>

                <p class="text-slate-700">
                    {{ $detalle->observaciones ?? 'Sin observaciones' }}
                </p>
            </div>

        </div>


        @if($detalle->firma_recibido)

            <div class="mt-8 pt-6 border-t border-slate-200">

                <p class="text-xs uppercase font-semibold text-slate-400 mb-2">
                    Firma de recibido
                </p>

                <img
                    src="{{ asset('storage/' . $detalle->firma_recibido) }}"
                    alt="Firma de recibido"
                    class="max-h-40 border rounded-lg bg-white p-2"
                >

            </div>

        @endif


        <div class="mt-8 pt-6 border-t border-slate-200 flex gap-5">

            @if($detalle->ordenTrabajo)

                <a
                    href="{{ route('ordenes.show', $detalle->ordenTrabajo) }}"
                    class="text-blue-700 font-semibold hover:underline"
                >
                    Ver orden de trabajo
                </a>

            @endif

            <a
                href="{{ route('mensajeria.show', $detalle->ruta_mensajeria_id) }}"
                class="text-blue-700 font-semibold hover:underline"
            >
                Ver ruta de mensajería
            </a>

        </div>

    </div>

</div>

@endsection