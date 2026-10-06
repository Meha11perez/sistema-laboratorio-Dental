@extends('layouts.app')

@section('title', 'Editar Orden | Laboratorio Dental')

@section('content')

<div class="max-w-6xl mx-auto">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

        <div>
            <p class="text-sm font-semibold text-blue-800">
                ORDEN {{ $orden->codigo }}
            </p>

            <h1 class="text-3xl font-bold text-slate-900">
                Editar Orden de Trabajo
            </h1>

            <p class="text-slate-500 mt-1">
                Actualice la información del trabajo registrado.
            </p>
        </div>

        <a
            href="{{ route('ordenes.show', $orden) }}"
            class="text-sm text-slate-600 hover:text-blue-900 font-semibold"
        >
            ← Volver al detalle
        </a>

    </div>

    @if ($errors->any())

        <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 text-red-700">

            <p class="font-semibold mb-2">
                Revise los siguientes campos:
            </p>

            <ul class="list-disc pl-5 text-sm">

                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif

    <form
        method="POST"
        action="{{ route('ordenes.update', $orden) }}"
    >

        @csrf
        @method('PUT')

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

            {{-- DATOS GENERALES --}}
            <div class="p-6 border-b border-slate-200">

                <h2 class="text-lg font-bold">
                    Datos generales
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Información principal de la orden.
                </p>

            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- CÓDIGO --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Código de orden
                    </label>

                    <input
                        type="text"
                        value="{{ $orden->codigo }}"
                        disabled
                        class="w-full bg-slate-100 border border-slate-300
                               rounded-lg px-4 py-3 text-slate-500"
                    >
                </div>

                {{-- CÓDIGO DEL ÁREA --}}
                <div>

                    <p class="text-xs
                            font-semibold
                            text-slate-400
                            uppercase">
                        Código del área
                    </p>

                    <p class="font-semibold
                            text-blue-700
                            mt-1">
                        {{ $orden->codigo_area
                            ?? 'Orden anterior sin código de área' }}
                    </p>

                </div>

                {{-- PRIORIDAD --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Prioridad *
                    </label>

                    <select
                        name="prioridad"
                        required
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 bg-white"
                    >

                        <option
                            value="Normal"
                            @selected(old('prioridad', $orden->prioridad) === 'Normal')
                        >
                            Normal
                        </option>

                        <option
                            value="Urgente"
                            @selected(old('prioridad', $orden->prioridad) === 'Urgente')
                        >
                            Urgente
                        </option>

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
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 bg-white"
                    >

                        @foreach ($odontologos as $odontologo)

                            <option
                                value="{{ $odontologo->id }}"
                                @selected(
                                    old('odontologo_id', $orden->odontologo_id)
                                    == $odontologo->id
                                )
                            >
                                {{ $odontologo->nombre }}
                            </option>

                        @endforeach

                    </select>
                </div>

                {{-- PACIENTE --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Paciente *
                    </label>

                    <select
                        name="paciente_id"
                        required
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 bg-white"
                    >

                        @foreach ($pacientes as $paciente)

                            <option
                                value="{{ $paciente->id }}"
                                @selected(
                                    old('paciente_id', $orden->paciente_id)
                                    == $paciente->id
                                )
                            >
                                {{ $paciente->nombre }}
                                {{ $paciente->apellido }}
                            </option>

                        @endforeach

                    </select>
                </div>

                @include('ordenes.partials.produccion')

            </div>

            {{-- ESTADO Y PRODUCCIÓN --}}
            <div class="p-6 border-y border-slate-200 bg-slate-50">

                <h2 class="text-lg font-bold">
                    Estado y Producción
                </h2>

            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- ESTADO --}}
                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Estado *
                    </label>

                    <select
                        name="estado_orden_id"
                        required
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 bg-white"
                    >

                        @foreach ($estados as $estado)

                            <option
                                value="{{ $estado->id }}"
                                @selected(
                                    old('estado_orden_id', $orden->estado_orden_id)
                                    == $estado->id
                                )
                            >
                                {{ $estado->nombre }}
                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- TÉCNICO --}}
                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Técnico actual
                    </label>

                    <select
                        name="tecnico_actual_id"
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 bg-white"
                    >

                        <option value="">
                            Sin asignar
                        </option>

                        @foreach ($tecnicos as $tecnico)

                            <option
                                value="{{ $tecnico->id }}"
                                @selected(
                                    old('tecnico_actual_id', $orden->tecnico_actual_id)
                                    == $tecnico->id
                                )
                            >
                                {{ $tecnico->user?->name }}
                            </option>

                        @endforeach

                    </select>

                </div>

            </div>

            {{-- FECHAS --}}
            <div class="p-6 border-y border-slate-200 bg-slate-50">

                <h2 class="text-lg font-bold">
                    Fechas
                </h2>

            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">

                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Fecha de ingreso *
                    </label>

                    <input
                        type="date"
                        name="fecha_ingreso"
                        required
                        value="{{ old(
                            'fecha_ingreso',
                            $orden->fecha_ingreso?->format('Y-m-d')
                        ) }}"
                        class="w-full border border-slate-300 rounded-lg px-4 py-3"
                    >

                </div>

                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Entrega estimada
                    </label>

                    <input
                        type="date"
                        name="fecha_entrega_estimada"
                        value="{{ old('fecha_entrega_estimada', $orden->fecha_entrega_estimada?->format('Y-m-d')) }}"
                        class="w-full border border-slate-300 rounded-lg px-4 py-3"
                    >

                </div>

                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Entrega real
                    </label>

                    <input
                        type="date"
                        name="fecha_entrega_real"
                        value="{{ old(
                            'fecha_entrega_real',
                            $orden->fecha_entrega_real?->format('Y-m-d')
                        ) }}"
                        class="w-full border border-slate-300 rounded-lg px-4 py-3"
                    >

                </div>

            </div>

            {{-- DATOS DEL TRABAJO --}}
            <div class="p-6 border-y border-slate-200 bg-slate-50">

                <h2 class="text-lg font-bold">
                    Información del trabajo
                </h2>

            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- CANTIDAD --}}
                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Cantidad *
                    </label>

                    <input
                        type="number"
                        min="1"
                        name="cantidad"
                        required
                        value="{{ old('cantidad', $orden->cantidad) }}"
                        class="w-full border border-slate-300 rounded-lg px-4 py-3"
                    >

                </div>

                {{-- TOTAL --}}
                <div>
                    <label for="total" class="block text-sm font-semibold text-slate-700 mb-2">
                        Total de la orden (Q)
                    </label>
                    <input
                        id="total"
                        type="number"
                        name="total"
                        min="0"
                        step="0.01"
                        required
                        value="{{ old('total', $orden->total ?? 0) }}"
                        class="w-full border border-slate-300 rounded-lg px-4 py-3"
                    >
                </div>

                {{-- COLOR --}}
                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Color
                    </label>

                    <input
                        type="text"
                        name="color"
                        value="{{ old('color', $orden->color) }}"
                        class="w-full border border-slate-300 rounded-lg px-4 py-3"
                    >

                </div>

            </div>

            {{-- ESPECIFICACIONES --}}
            <div class="px-6 pb-6">

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Especificaciones *
                </label>

                <textarea
                    name="especificaciones"
                    rows="4"
                    required
                    class="w-full border border-slate-300 rounded-lg px-4 py-3 resize-none"
                >{{ old('especificaciones', $orden->especificaciones) }}</textarea>

            </div>

            {{-- OBSERVACIONES --}}
            <div class="px-6 pb-6">

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Observaciones
                </label>

                <textarea
                    name="observaciones"
                    rows="3"
                    class="w-full border border-slate-300 rounded-lg px-4 py-3 resize-none"
                >{{ old('observaciones', $orden->observaciones) }}</textarea>

            </div>

            {{-- BOTONES --}}
            <div class="px-6 py-5 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">

                <a
                    href="{{ route('ordenes.show', $orden) }}"
                    class="px-5 py-3 border border-slate-300 rounded-lg
                           text-slate-700 font-semibold hover:bg-white"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="px-6 py-3 bg-blue-800 hover:bg-blue-900
                           text-white rounded-lg font-semibold"
                >
                    Guardar Cambios
                </button>

            </div>

        </div>

    </form>

</div>

@endsection
