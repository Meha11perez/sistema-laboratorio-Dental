@extends('layouts.app')

@section('title', 'Agregar Visita | Mensajería')

@section('content')

<div class="max-w-5xl mx-auto">

    {{-- ENCABEZADO --}}
    <div class="mb-8">

        <a
            href="{{ route('mensajeria.show', $ruta) }}"
            class="text-sm font-semibold text-blue-700 hover:underline"
        >
            ← Volver a la Ruta
        </a>

        <h1 class="text-3xl font-bold text-slate-900 mt-3">
            Agregar Visita
        </h1>

        <p class="text-slate-500 mt-1">
            Registre una entrega o recolección dentro de la ruta.
        </p>

    </div>


    {{-- INFORMACIÓN DE LA RUTA --}}
    <div class="bg-blue-50 border border-blue-200
                rounded-xl p-5 mb-6">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <div>
                <p class="text-xs uppercase font-semibold text-slate-400">
                    Fecha
                </p>

                <p class="font-bold text-slate-900 mt-1">
                    {{ $ruta->fecha?->format('d/m/Y') ?? '—' }}
                </p>
            </div>


            <div>
                <p class="text-xs uppercase font-semibold text-slate-400">
                    Mensajero
                </p>

                <p class="font-bold text-slate-900 mt-1">
                    {{ $ruta->mensajero?->name ?? 'Sin asignar' }}
                </p>
            </div>


            <div>
                <p class="text-xs uppercase font-semibold text-slate-400">
                    Estado
                </p>

                <p class="font-bold text-slate-900 mt-1">
                    {{ $ruta->estado }}
                </p>
            </div>

        </div>

    </div>


    {{-- ERRORES --}}
    @if($errors->any())

        <div class="mb-6 bg-red-50 border border-red-200
                    text-red-700 rounded-xl p-4">

            <p class="font-semibold mb-2">
                Revise los siguientes campos:
            </p>

            <ul class="list-disc pl-5 text-sm">

                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif


    {{-- FORMULARIO --}}
    <form
        method="POST"
        action="{{ route('mensajeria.detalles.store', $ruta) }}"
        class="bg-white border border-slate-200
               rounded-xl shadow-sm overflow-hidden"
    >

        @csrf


        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- TIPO DE MOVIMIENTO --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Tipo de movimiento *
                </label>

                <select
                    name="tipo_movimiento"
                    required
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-3 bg-white"
                >

                    <option value="">
                        Seleccione...
                    </option>

                    <option
                        value="Entrega"
                        @selected(old('tipo_movimiento') === 'Entrega')
                    >
                        Entrega
                    </option>

                    <option
                        value="Recolección"
                        @selected(old('tipo_movimiento') === 'Recolección')
                    >
                        Recolección
                    </option>

                </select>

            </div>


            {{-- ORDEN DE VISITA --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Orden de visita *
                </label>

                <input
                    type="number"
                    name="orden_visita"
                    value="{{ old('orden_visita', $siguienteOrden) }}"
                    min="1"
                    required
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-3"
                >

            </div>


            {{-- ORDEN DE TRABAJO --}}
            <div class="md:col-span-2">

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Orden de trabajo
                </label>

                <select
                    name="orden_trabajo_id"
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-3 bg-white"
                >

                    <option value="">
                        Sin orden asociada
                    </option>

                    @foreach($ordenes as $orden)

                        <option
                            value="{{ $orden->id }}"
                            @selected(
                                old('orden_trabajo_id') == $orden->id
                            )
                        >
                            {{ $orden->codigo }}
                            —
                            {{ $orden->paciente?->nombre }}
                            {{ $orden->paciente?->apellido }}
                            —
                            {{ $orden->odontologo?->nombre }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- ODONTÓLOGO --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Odontólogo *
                </label>

                <select
                    name="odontologo_id"
                    required
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-3 bg-white"
                >

                    <option value="">
                        Seleccione...
                    </option>

                    @foreach($odontologos as $odontologo)

                        <option
                            value="{{ $odontologo->id }}"
                            @selected(
                                old('odontologo_id') == $odontologo->id
                            )
                        >
                            {{ $odontologo->nombre }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- CLÍNICA --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Clínica
                </label>

                <select
                    name="clinica_id"
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-3 bg-white"
                >

                    <option value="">
                        Sin clínica
                    </option>

                    @foreach($clinicas as $clinica)

                        <option
                            value="{{ $clinica->id }}"
                            @selected(
                                old('clinica_id') == $clinica->id
                            )
                        >
                            {{ $clinica->nombre }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- DIRECCIÓN --}}
            <div class="md:col-span-2">

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Dirección de referencia
                </label>

                <input
                    type="text"
                    name="direccion_referencia"
                    value="{{ old('direccion_referencia') }}"
                    placeholder="Ej. Zona 1, Clínica San Juan..."
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-3"
                >

            </div>


            {{-- OBSERVACIONES --}}
            <div class="md:col-span-2">

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Observaciones
                </label>

                <textarea
                    name="observaciones"
                    rows="4"
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-3 resize-none"
                    placeholder="Indicaciones adicionales para la visita..."
                >{{ old('observaciones') }}</textarea>

            </div>

        </div>


        {{-- BOTONES --}}
        <div class="px-6 py-5 bg-slate-50
                    border-t border-slate-200
                    flex justify-end gap-3">

            <a
                href="{{ route('mensajeria.show', $ruta) }}"
                class="px-5 py-3 border border-slate-300
                       rounded-lg font-semibold text-slate-600
                       hover:bg-slate-100"
            >
                Cancelar
            </a>

            <button
                type="submit"
                class="px-6 py-3 bg-blue-800
                       hover:bg-blue-900 text-white
                       rounded-lg font-semibold"
            >
                Agregar Visita
            </button>

        </div>

    </form>

</div>

@endsection