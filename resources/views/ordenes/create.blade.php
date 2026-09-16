@extends('layouts.app')

@section('title', 'Nueva Orden | Laboratorio Dental')

@section('content')

<div class="max-w-6xl mx-auto">

        {{-- ENCABEZADO --}}
        @if(isset($orden))

        <div class="mb-6 bg-amber-50 border border-amber-200 rounded-xl px-5 py-4">

            <p class="text-sm font-bold text-amber-700 uppercase">
                Creando repetición
            </p>

            <p class="text-slate-700 mt-2">
                Esta orden será una repetición de
                <span class="font-bold">
                    {{ $orden->codigo }}
                </span>
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 text-sm">

                <div>
                    <span class="text-slate-400">Paciente:</span>

                    <p class="font-semibold">
                        {{ $orden->paciente?->nombre }}
                        {{ $orden->paciente?->apellido }}
                    </p>
                </div>

                <div>
                    <span class="text-slate-400">Odontólogo:</span>

                    <p class="font-semibold">
                        {{ $orden->odontologo?->nombre }}
                    </p>
                </div>

                <div>
                    <span class="text-slate-400">Prótesis:</span>

                    <p class="font-semibold">
                        {{ $orden->tipoProtesis?->nombre }}
                    </p>
                </div>

            </div>

        </div>

    @endif

    <div class="flex items-center justify-between mb-8">

        <div>
            <h1 class="text-3xl font-bold text-slate-900">
                Nueva Orden de Trabajo
            </h1>

            <p class="text-slate-500 mt-1">
                Registre la información del trabajo recibido en el laboratorio.
            </p>
        </div>

        <a
            href="{{ route('ordenes.index') }}"
    class="text-sm text-slate-600 hover:text-blue-900 font-semibold"
        >
            ← Volver
        </a>

    </div>


    {{-- ERRORES --}}
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


    <form method="POST" action="{{ route('ordenes.store') }}">

        @csrf

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

            {{-- DATOS GENERALES --}}
            <div class="p-6 border-b border-slate-200">

                <h2 class="text-lg font-bold text-slate-900">
                    Datos generales
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Información principal de la orden.
                </p>

            </div>


            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- CAJA --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Código de caja
                    </label>

                    <input
                        type="text"
                        name="codigo_caja"
                        value="{{ old('codigo_caja') }}"
                        placeholder="Ej. V14, A47, F30"
                        class="w-full border border-slate-300 rounded-lg px-4 py-3
                               focus:ring-2 focus:ring-blue-100
                               focus:border-blue-800 outline-none"
                    >
                </div>


                {{-- FECHA INGRESO --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Fecha de ingreso *
                    </label>

                    <input
                        type="date"
                        name="fecha_ingreso"
                        value="{{ old('fecha_ingreso', now()->format('Y-m-d')) }}"
                        min="{{ now()->format('Y-m-d') }}"
                        class="w-full border border-slate-300 rounded-lg px-4 py-2.5"
                    >
                </div>

                {{-- FECHA ENTREGA --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Fecha de entrega estimada
                    </label>

                    <input
                        type="date"
                        name="fecha_entrega_estimada"
                        value="{{ old('fecha_entrega_estimada') }}"
                        min="{{ now()->format('Y-m-d') }}"
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-100
                               focus:border-blue-800 outline-none">
                </div>

                {{-- ODONTÓLOGO --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Odontólogo *
                    </label>

                    <select
                        name="odontologo_id"
                        required
                        class="w-full border border-slate-300 rounded-lg px-4 py-3
                               bg-white focus:ring-2 focus:ring-blue-100
                               focus:border-blue-800 outline-none"
                    >

                        <option value="">
                            Seleccione...
                        </option>

                        @foreach ($odontologos as $odontologo)

                            <option
                                value="{{ $odontologo->id }}"
                                    @selected(
                                        old(
                                            'odontologo_id',
                                            isset($orden) ? $orden->odontologo_id : null
                                        ) == $odontologo->id
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
                        class="w-full border border-slate-300 rounded-lg px-4 py-3
                               bg-white focus:ring-2 focus:ring-blue-100
                               focus:border-blue-800 outline-none"
                    >

                        <option value="">
                            Seleccione...
                        </option>

                        @foreach ($pacientes as $paciente)

                            <option
                                value="{{ $paciente->id }}"
                                    @selected(
                                        old(
                                            'paciente_id',
                                            isset($orden) ? $orden->paciente_id : null
                                        ) == $paciente->id
                                    )
                                >
                                {{ $paciente->nombre }}
                                {{ $paciente->apellido }}
                            </option>

                        @endforeach

                    </select>
                </div>


                {{-- PRIORIDAD --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Prioridad *
                    </label>

                    <select
                        name="prioridad"
                        required
                        class="w-full border border-slate-300 rounded-lg px-4 py-3
                               bg-white focus:ring-2 focus:ring-blue-100
                               focus:border-blue-800 outline-none"
                    >

                        <option value="Normal" @selected(old('prioridad') === 'Normal')>
                            Normal
                        </option>

                        <option value="Urgente" @selected(old('prioridad') === 'Urgente')>
                            Urgente
                        </option>

                    </select>

                </div>

            </div>


            {{-- INFORMACIÓN DEL TRABAJO --}}
            <div class="p-6 border-y border-slate-200 bg-slate-50">

                <h2 class="text-lg font-bold text-slate-900">
                    Información del trabajo
                </h2>

            </div>


            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            {{-- TIPO DE PRÓTESIS --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Tipo de prótesis *
                </label>

                <select
                    name="tipo_protesis_id"
                    required
                    class="w-full border border-slate-300 rounded-lg px-4 py-3 bg-white"
                >
                    @foreach($tiposProtesis as $tipo)
                        <option
                            value="{{ $tipo->id }}"
                            @selected(
                                old(
                                    'tipo_protesis_id',
                                    isset($orden) ? $orden->tipo_protesis_id : null
                                ) == $tipo->id
                            )
                        >
                            {{ $tipo->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- TIPO DE ORDEN --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Tipo de orden
                </label>

                @if(isset($orden))

                    {{-- REPETICIÓN --}}
                    <input
                        type="text"
                        value="Repetición"
                        disabled
                        class="w-full bg-amber-50 border border-amber-200
                            text-amber-700 font-semibold
                            rounded-lg px-4 py-3"
                    >

                    <input
                        type="hidden"
                        name="tipo_orden"
                        value="Repeticion"
                    >

                    <input
                        type="hidden"
                        name="orden_origen_id"
                        value="{{ $orden->id }}"
                    >
                    @if(isset($devolucionId))
                        <input
                            type="hidden"
                            name="devolucion_id"
                            value="{{ $devolucionId }}"
                        >
                    @endif

                @else

                    {{-- NUEVA --}}
                    <input
                        type="text"
                        value="Nueva"
                        disabled
                        class="w-full bg-blue-50 border border-blue-200
                            text-blue-700 font-semibold
                            rounded-lg px-4 py-3"
                    >

                    <input
                        type="hidden"
                        name="tipo_orden"
                        value="Nueva"
                    >

                @endif

            </div>

        @if(isset($orden))
        <div class="md:col-span-2 lg:col-span-3">

            <label class="block text-sm font-semibold text-slate-700 mb-2">
                Motivo de repetición *
            </label>

            <select
                name="motivo_repeticion"
                required
                class="w-full border border-slate-300 rounded-lg
                    px-4 py-3 bg-white"
            >

                <option value="">
                    Seleccione...
                </option>

                <option value="Paciente no conforme"
                    @selected(old('motivo_repeticion') === 'Paciente no conforme')>
                    Paciente no conforme
                </option>

                <option value="Problema de ajuste"
                    @selected(old('motivo_repeticion') === 'Problema de ajuste')>
                    Problema de ajuste
                </option>

                <option value="Cambio de color"
                    @selected(old('motivo_repeticion') === 'Cambio de color')>
                    Cambio de color
                </option>

                <option value="Fractura"
                    @selected(old('motivo_repeticion') === 'Fractura')>
                    Fractura
                </option>

                <option value="Error de laboratorio"
                    @selected(old('motivo_repeticion') === 'Error de laboratorio')>
                    Error de laboratorio
                </option>

                <option value="Otro"
                    @selected(old('motivo_repeticion') === 'Otro')>
                    Otro
                </option>

            </select>

        </div>

    @endif

            {{-- CANTIDAD --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Cantidad *
                    </label>

                    <input
                        type="number"
                        name="cantidad"
                        min="1"
                        value="{{ old(
                            'cantidad',
                            isset($orden) ? $orden->cantidad : 1
                        ) }}"
                        required
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
                        value="{{ old(
                            'color',
                            isset($orden) ? $orden->color : ''
                        ) }}"
                        placeholder="Ej. A2, Chromascop 130..."
                        class="w-full border border-slate-300 rounded-lg px-4 py-3"
                    >
                </div>


                {{-- ETAPA --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        {{ isset($orden)
                            ? 'Etapa a la que regresa *'
                            : 'Etapa inicial' }}
                    </label>

                    <select
                        name="etapa_actual_id"
                        {{ isset($orden) ? 'required' : '' }}
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 bg-white"
                    >

                        <option value="">
                            {{ isset($orden)
                                ? 'Seleccione la etapa a la que regresa...'
                                : 'Sin asignar' }}
                        </option>

                        @foreach ($etapas as $etapa)

                            <option
                                value="{{ $etapa->id }}"
                                @selected(old('etapa_actual_id') == $etapa->id)
                            >
                                {{ $etapa->nombre }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- TÉCNICO --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Técnico inicial
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
                                @selected(old('tecnico_actual_id') == $tecnico->id)
                            >
                                {{ $tecnico->user?->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- TOTAL --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Monto total
                    </label>

                    <div class="relative">

                        <span class="absolute left-4 top-3 text-slate-500">
                            Q
                        </span>

                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="total"
                            value="{{ old('total') }}"
                            placeholder="0.00"
                            class="w-full border border-slate-300 rounded-lg
                                   pl-9 pr-4 py-3"
                        >

                    </div>

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
                    placeholder="Ej. 1 unilateral flexible inferior izquierdo..."
                    class="w-full border border-slate-300 rounded-lg px-4 py-3
                           resize-none focus:ring-2 focus:ring-blue-100
                           focus:border-blue-800 outline-none"
                >{{ old('especificaciones') }}</textarea>

            </div>


            {{-- OBSERVACIONES --}}
            <div class="px-6 pb-6">

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Observaciones
                </label>

                <textarea
                    name="observaciones"
                    rows="3"
                    placeholder="Ej. Modelo superior e inferior + registro de mordida..."
                    class="w-full border border-slate-300 rounded-lg px-4 py-3 resize-none"
                >{{ old('observaciones') }}</textarea>

            </div>


            {{-- BOTONES --}}
            <div class="px-6 py-5 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">

                <a
                    href="{{ route('ordenes.index') }}"
                    class="px-5 py-3 border border-slate-300 rounded-lg
                           text-slate-700 font-semibold hover:bg-white"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="px-6 py-3 bg-blue-800 hover:bg-blue-900
                           text-white rounded-lg font-semibold shadow-sm"
                >
                    Guardar Orden
                </button>

            </div>

        </div>
        
    </form>

</div>

@endsection