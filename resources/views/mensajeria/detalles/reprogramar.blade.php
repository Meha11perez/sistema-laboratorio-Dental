@extends('layouts.app')

@section('title', 'Reprogramar Visita')

@section('content')

<div class="max-w-3xl mx-auto">

    <div class="mb-8">

        <a
            href="{{ route(
                'mensajeria.show',
                $detalle->ruta_mensajeria_id
            ) }}"
            class="text-sm font-semibold
                   text-blue-700 hover:underline"
        >
            ← Volver a la Ruta
        </a>

        <h1 class="text-3xl font-bold
                   text-slate-900 mt-3">
            Reprogramar Visita
        </h1>

        <p class="text-slate-500 mt-1">
            Seleccione una nueva ruta para esta visita.
        </p>

    </div>

    {{-- MENSAJES DE ERROR --}}
    @if(session('error'))

        <div class="mb-6 bg-red-50
                    border border-red-200
                    text-red-700
                    rounded-xl px-5 py-4">
            {{ session('error') }}
        </div>

    @endif

    @if($errors->any())

        <div class="mb-6 bg-red-50
                    border border-red-200
                    text-red-700
                    rounded-xl px-5 py-4">

            <ul class="list-disc pl-5">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif

    {{-- INFORMACIÓN DE LA VISITA --}}
    <div class="bg-white
                border border-slate-200
                rounded-xl shadow-sm
                overflow-hidden mb-6">

        <div class="px-6 py-5
                    border-b border-slate-200">

            <h2 class="text-lg font-bold
                       text-slate-900">
                Información de la visita
            </h2>

        </div>

        <div class="p-6 grid
                    grid-cols-1 md:grid-cols-2
                    gap-6">

            <div>

                <p class="text-xs uppercase
                          font-semibold text-slate-400">
                    Tipo
                </p>

                <p class="font-semibold
                          text-slate-900 mt-1">
                    {{ $detalle->tipo_movimiento }}
                </p>

            </div>

            <div>

                <p class="text-xs uppercase
                          font-semibold text-slate-400">
                    Orden
                </p>

                <p class="font-semibold
                          text-slate-900 mt-1">

                    {{ $detalle->ordenTrabajo?->codigo ?? '—' }}

                </p>

            </div>

            <div>

                <p class="text-xs uppercase
                          font-semibold text-slate-400">
                    Odontólogo
                </p>

                <p class="font-semibold
                          text-slate-900 mt-1">

                    {{ $detalle->odontologo?->nombre ?? '—' }}

                </p>

            </div>

            <div>

                <p class="text-xs uppercase
                          font-semibold text-slate-400">
                    Clínica
                </p>

                <p class="font-semibold
                          text-slate-900 mt-1">

                    {{ $detalle->clinica?->nombre ?? '—' }}

                </p>

            </div>

            <div class="md:col-span-2">

                <p class="text-xs uppercase
                          font-semibold text-slate-400">
                    Dirección
                </p>

                <p class="text-slate-700 mt-1">

                    {{ $detalle->direccion_referencia ?? '—' }}

                </p>

            </div>

            @if($detalle->observaciones)

                <div class="md:col-span-2">

                    <p class="text-xs uppercase
                              font-semibold text-slate-400">
                        Observaciones anteriores
                    </p>

                    <p class="text-slate-700 mt-1">

                        {{ $detalle->observaciones }}

                    </p>

                </div>

            @endif

        </div>

    </div>
    {{-- FORMULARIO --}}
    <div class="bg-white
                border border-slate-200
                rounded-xl shadow-sm
                overflow-hidden">

        <div class="px-6 py-5
                    border-b border-slate-200">

            <h2 class="text-lg font-bold
                       text-slate-900">
                Nueva ruta
            </h2>

        </div>
        <form
            method="POST"
            action="{{ route(
                'mensajeria.detalles.reprogramar',
                $detalle
            ) }}"
            class="p-6"
        >
            @csrf

            <div>

                <label
                    for="ruta_mensajeria_id"
                    class="block text-sm
                           font-semibold
                           text-slate-700 mb-2"
                >
                    Ruta disponible
                </label>

                <select
                    name="ruta_mensajeria_id"
                    id="ruta_mensajeria_id"
                    required
                    class="w-full border
                           border-slate-300
                           rounded-lg px-4 py-3
                           bg-white"
                >

                    <option value="">
                        Seleccione una ruta...
                    </option>

                    @foreach($rutasDisponibles as $rutaDestino)

                        <option
                            value="{{ $rutaDestino->id }}"
                            @selected(
                                old('ruta_mensajeria_id')
                                == $rutaDestino->id
                   
                                )
                        >
                            {{ $rutaDestino->fecha?->format('d/m/Y') }}
                            —
                            {{ $rutaDestino->mensajero?->name
                                ?? 'Sin mensajero' }}
                        </option>

                    @endforeach

                </select>

                @if($rutasDisponibles->isEmpty())

                    <p class="text-sm
                              text-amber-700 mt-3">
                        No existen rutas pendientes
                        disponibles para reprogramar
                        esta visita.
                    </p>

                @endif

            </div>

            <div class="flex flex-col
                        sm:flex-row
                        sm:justify-end
                        gap-3 mt-8">

                <a
                    href="{{ route(
                        'mensajeria.show',
                        $detalle->ruta_mensajeria_id
                    ) }}"
                    class="inline-flex
                           justify-center
                           px-5 py-2.5
                           border border-slate-300
                           rounded-lg
                           font-semibold
                           text-slate-700
                           hover:bg-slate-50"
                >
                    Cancelar
                </a>


                <button
                    type="submit"
                    @disabled($rutasDisponibles->isEmpty())
                    class="inline-flex
                           justify-center
                           px-5 py-2.5
                           bg-blue-800
                           hover:bg-blue-900
                           text-white
                           rounded-lg
                           font-semibold
                           disabled:opacity-50
                           disabled:cursor-not-allowed"
                >
                    Reprogramar Visita
                </button>

            </div>

        </form>

    </div>

</div>

@endsection