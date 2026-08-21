@extends('layouts.app')

@section('title', 'Registrar Devolución | Laboratorio Dental')

@section('content')

<div class="max-w-5xl mx-auto">

    {{-- ENCABEZADO --}}
    <div class="mb-8">

        <a
            href="{{ route('ordenes.show', $orden) }}"
            class="text-sm font-semibold text-blue-700 hover:underline"
        >
            ← Volver a la orden
        </a>

        <h1 class="text-3xl font-bold text-slate-900 mt-3">
            Registrar Devolución
        </h1>

        <p class="text-slate-500 mt-1">
            Registre la devolución o garantía correspondiente a esta orden.
        </p>

    </div>


    {{-- DATOS DE LA ORDEN --}}
    <div class="bg-blue-50 border border-blue-200
                rounded-xl p-5 mb-6">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <div>
                <p class="text-xs uppercase font-semibold text-slate-400">
                    Orden
                </p>

                <p class="font-bold text-slate-900 mt-1">
                    {{ $orden->codigo }}
                </p>
            </div>


            <div>
                <p class="text-xs uppercase font-semibold text-slate-400">
                    Paciente
                </p>

                <p class="font-semibold text-slate-900 mt-1">
                    {{ $orden->paciente?->nombre }}
                    {{ $orden->paciente?->apellido }}
                </p>
            </div>


            <div>
                <p class="text-xs uppercase font-semibold text-slate-400">
                    Odontólogo
                </p>

                <p class="font-semibold text-slate-900 mt-1">
                    {{ $orden->odontologo?->nombre }}
                </p>
            </div>


            <div>
                <p class="text-xs uppercase font-semibold text-slate-400">
                    Prótesis
                </p>

                <p class="font-semibold text-slate-900 mt-1">
                    {{ $orden->tipoProtesis?->nombre }}
                </p>
            </div>

        </div>

    </div>


    {{-- ERRORES --}}
    @if($errors->any())

        <div class="mb-6 bg-red-50 border border-red-200
                    rounded-xl p-4 text-red-700">

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
        action="{{ route('devoluciones.store', $orden) }}"
        class="bg-white border border-slate-200
               rounded-xl shadow-sm overflow-hidden"
    >

        @csrf


        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- TIPO --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Tipo *
                </label>

                <select
                    name="tipo"
                    required
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-3 bg-white"
                >

                    <option value="">
                        Seleccione...
                    </option>

                    <option
                        value="Garantia"
                        @selected(old('tipo') === 'Garantia')
                    >
                        Garantía
                    </option>

                    <option
                        value="Devolucion"
                        @selected(old('tipo') === 'Devolucion')
                    >
                        Devolución
                    </option>

                </select>

            </div>


            {{-- FECHA --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Fecha de devolución *
                </label>

                <input
                    type="date"
                    name="fecha_devolucion"
                    value="{{ old(
                        'fecha_devolucion',
                        now()->toDateString()
                    ) }}"
                    required
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-3"
                >

            </div>


            {{-- MOTIVO --}}
            <div class="md:col-span-2">

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Motivo *
                </label>

                <input
                    type="text"
                    name="motivo"
                    value="{{ old('motivo') }}"
                    required
                    placeholder="Ej. Paciente no conforme con prueba de dientes"
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-3"
                >

            </div>


            {{-- TÉCNICO --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Técnico responsable
                </label>

                <select
                    name="tecnico_responsable_id"
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-3 bg-white"
                >

                    <option value="">
                        Sin asignar
                    </option>

                    @foreach($tecnicos as $tecnico)

                        <option
                            value="{{ $tecnico->id }}"
                            @selected(
                                old('tecnico_responsable_id')
                                == $tecnico->id
                            )
                        >
                            {{ $tecnico->user?->name }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- ESTADO --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Estado *
                </label>

                <select
                    name="estado"
                    required
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-3 bg-white"
                >

                    <option value="Pendiente">
                        Pendiente
                    </option>

                    <option value="En revision">
                        En revisión
                    </option>

                    <option value="Resuelta">
                        Resuelta
                    </option>

                </select>

            </div>


            {{-- REPETICIÓN --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    ¿Requiere repetición? *
                </label>

                <select
                    name="requiere_repeticion"
                    required
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-3 bg-white"
                >

                    <option value="0"
                        @selected(old('requiere_repeticion') === '0')>
                        No
                    </option>

                    <option value="1"
                        @selected(old('requiere_repeticion') === '1')>
                        Sí
                    </option>

                </select>

            </div>


            {{-- PÉRDIDA --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Pérdida estimada
                </label>

                <div class="relative">

                    <span class="absolute left-4 top-3 text-slate-500">
                        Q
                    </span>

                    <input
                        type="number"
                        name="perdida_estimada"
                        value="{{ old('perdida_estimada', 0) }}"
                        min="0"
                        step="0.01"
                        class="w-full border border-slate-300
                               rounded-lg pl-9 pr-4 py-3"
                    >

                </div>

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
                    placeholder="Información adicional..."
                >{{ old('observaciones') }}</textarea>

            </div>

        </div>


        {{-- BOTONES --}}
        <div class="px-6 py-5 bg-slate-50
                    border-t border-slate-200
                    flex justify-end gap-3">

            <a
                href="{{ route('ordenes.show', $orden) }}"
                class="px-5 py-3 border border-slate-300
                       rounded-lg font-semibold text-slate-600"
            >
                Cancelar
            </a>

            <button
                type="submit"
                class="px-6 py-3 bg-blue-800
                       hover:bg-blue-900 text-white
                       rounded-lg font-semibold"
            >
                Registrar Devolución
            </button>

        </div>

    </form>

</div>

@endsection