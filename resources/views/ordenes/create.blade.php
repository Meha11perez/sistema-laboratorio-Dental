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

       <a href="{{ route('ordenes.index') }}"
            class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-blue-100 text-blue-800 border border-blue-200 font-semibold text-sm hover:bg-blue-200 transition"
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

        {{-- ===================================================== --}}
        {{-- SELECCIÓN DEL ÁREA DE TRABAJO --}}
        {{-- ===================================================== --}}

        <div class="bg-white
                    border border-slate-200
                    rounded-2xl
                    shadow-sm
                    overflow-hidden
                    mb-6">

            <div class="p-6
                        border-b border-slate-200">

                <h2 class="text-lg
                        font-bold
                        text-slate-900">
                    Área de trabajo
                </h2>

                <p class="text-sm
                        text-slate-500
                        mt-1">
                    Seleccione el área a la que pertenece el trabajo.
                    El sistema generará automáticamente su código especial.
                </p>

            </div>


            <div class="p-6">

                @php

                    $areaSeleccionada = old(
                        'area_trabajo',
                        isset($orden)
                            ? $orden->area_trabajo
                            : null
                    );

                @endphp


                <input
                    type="hidden"
                    name="area_trabajo"
                    id="area_trabajo"
                    value="{{ $areaSeleccionada }}"
                >


                <div class="grid
                            grid-cols-1
                            sm:grid-cols-2
                            xl:grid-cols-4
                            gap-4">


                    {{-- ============================================ --}}
                    {{-- REMOVIBLES --}}
                    {{-- ============================================ --}}

                    <button
                        type="button"
                        data-area="removible"
                        class="area-card
                            relative
                            text-left
                            border-2
                            rounded-xl
                            p-5
                            transition
                            hover:border-blue-500
                            hover:bg-blue-50
                            {{ $areaSeleccionada === 'removible'
                                    ? 'border-blue-700 bg-blue-50'
                                    : 'border-slate-200 bg-white'
                            }}"
                    >

                        <div class="flex
                                    items-center
                                    justify-between
                                    gap-3">

                            <span class="inline-flex
                                        items-center
                                        justify-center
                                        w-10 h-10
                                        rounded-lg
                                        bg-blue-100
                                        text-blue-700
                                        font-bold">
                                PR
                            </span>

                            <span
                                class="area-check
                                    {{ $areaSeleccionada === 'removible'
                                            ? ''
                                            : 'hidden'
                                    }}
                                    text-blue-700
                                    font-bold"
                            >
                                ✓
                            </span>

                        </div>


                        <p class="font-bold
                                text-slate-900
                                mt-4">
                            Prótesis removibles
                        </p>

                        <p class="text-xs
                                text-slate-500
                                mt-1">
                            Código especial PR
                        </p>

                    </button>


                    {{-- ============================================ --}}
                    {{-- FIJAS --}}
                    {{-- ============================================ --}}

                    <button
                        type="button"
                        data-area="fija"
                        class="area-card
                            relative
                            text-left
                            border-2
                            rounded-xl
                            p-5
                            transition
                            hover:border-violet-500
                            hover:bg-violet-50
                            {{ $areaSeleccionada === 'fija'
                                    ? 'border-violet-700 bg-violet-50'
                                    : 'border-slate-200 bg-white'
                            }}"
                    >

                        <div class="flex
                                    items-center
                                    justify-between
                                    gap-3">

                            <span class="inline-flex
                                        items-center
                                        justify-center
                                        w-10 h-10
                                        rounded-lg
                                        bg-violet-100
                                        text-violet-700
                                        font-bold">
                                PF
                            </span>

                            <span
                                class="area-check
                                    {{ $areaSeleccionada === 'fija'
                                            ? ''
                                            : 'hidden'
                                    }}
                                    text-violet-700
                                    font-bold"
                            >
                                ✓
                            </span>

                        </div>


                        <p class="font-bold
                                text-slate-900
                                mt-4">
                            Prótesis fijas
                        </p>

                        <p class="text-xs
                                text-slate-500
                                mt-1">
                            Código especial PF
                        </p>

                    </button>


                    {{-- ============================================ --}}
                    {{-- CROMO COBALTO --}}
                    {{-- ============================================ --}}

                    <button
                        type="button"
                        data-area="cromo_cobalto"
                        class="area-card
                            relative
                            text-left
                            border-2
                            rounded-xl
                            p-5
                            transition
                            hover:border-amber-500
                            hover:bg-amber-50
                            {{ $areaSeleccionada === 'cromo_cobalto'
                                    ? 'border-amber-600 bg-amber-50'
                                    : 'border-slate-200 bg-white'
                            }}"
                    >

                        <div class="flex
                                    items-center
                                    justify-between
                                    gap-3">

                            <span class="inline-flex
                                        items-center
                                        justify-center
                                        w-10 h-10
                                        rounded-lg
                                        bg-amber-100
                                        text-amber-700
                                        font-bold">
                                CC
                            </span>

                            <span
                                class="area-check
                                    {{ $areaSeleccionada === 'cromo_cobalto'
                                            ? ''
                                            : 'hidden'
                                    }}
                                    text-amber-700
                                    font-bold"
                            >
                                ✓
                            </span>

                        </div>


                        <p class="font-bold
                                text-slate-900
                                mt-4">
                            Cromo cobalto
                        </p>

                        <p class="text-xs
                                text-slate-500
                                mt-1">
                            Código especial CC
                        </p>

                    </button>


                    {{-- ============================================ --}}
                    {{-- ORTODONCIA --}}
                    {{-- ============================================ --}}

                    <button
                        type="button"
                        data-area="ortodoncia"
                        class="area-card
                            relative
                            text-left
                            border-2
                            rounded-xl
                            p-5
                            transition
                            hover:border-emerald-500
                            hover:bg-emerald-50
                            {{ $areaSeleccionada === 'ortodoncia'
                                    ? 'border-emerald-600 bg-emerald-50'
                                    : 'border-slate-200 bg-white'
                            }}"
                    >

                        <div class="flex
                                    items-center
                                    justify-between
                                    gap-3">

                            <span class="inline-flex
                                        items-center
                                        justify-center
                                        w-10 h-10
                                        rounded-lg
                                        bg-emerald-100
                                        text-emerald-700
                                        font-bold">
                                AO
                            </span>

                            <span
                                class="area-check
                                    {{ $areaSeleccionada === 'ortodoncia'
                                            ? ''
                                            : 'hidden'
                                    }}
                                    text-emerald-700
                                    font-bold"
                            >
                                ✓
                            </span>

                        </div>


                        <p class="font-bold
                                text-slate-900
                                mt-4">
                            Aparatos de ortodoncia
                        </p>

                        <p class="text-xs
                                text-slate-500
                                mt-1">
                            Código especial AO
                        </p>

                    </button>

                </div>


                {{-- MENSAJE --}}
                <div
                    id="mensajeArea"
                    class="mt-5
                        rounded-lg
                        bg-slate-50
                        border border-slate-200
                        px-4 py-3
                        text-sm
                        text-slate-600"
                >

                    @if($areaSeleccionada)

                        Área seleccionada correctamente.

                    @else

                        Seleccione un área para continuar.

                    @endif

                </div>

            </div>

        </div>

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

                {{-- FECHA INGRESO --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Fecha de ingreso 
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
                        Odontólogo 
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
                        Paciente 
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
                        Prioridad 
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
                {{-- CLASIFICACIÓN: LA ETAPA DEPENDE DEL ÁREA SELECCIONADA --}}
                @include('ordenes.partials.produccion', ['mostrarArea' => false])

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
                Motivo de repetición 
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
                        Cantidad 
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

            </div>

            {{-- ESPECIFICACIONES --}}
            <div class="px-6 pb-6">

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Especificaciones 
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

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const areaInput =
            document.getElementById('area_trabajo');

        const cards =
            document.querySelectorAll('.area-card');

        const mensaje =
            document.getElementById('mensajeArea');


        /*
        |--------------------------------------------------------------------------
        | CONFIGURACIÓN DE LAS ÁREAS
        |--------------------------------------------------------------------------
        */

        const configuracion = {

            removible: {

                borde: 'border-blue-700',

                fondo: 'bg-blue-50',

                mensaje:
                    'Prótesis removibles seleccionada — código PR.',

                etapas: [
                    'Rodetes y cubetas individuales',
                    'Prueba de dientes',
                    'Terminados'
                ]

            },


            fija: {

                borde: 'border-violet-700',

                fondo: 'bg-violet-50',

                mensaje:
                    'Prótesis fijas seleccionada — código PF.',

                etapas: [
                    'Prueba de metal',
                    'Biscochos',
                    'Terminados'
                ]

            },


            cromo_cobalto: {

                borde: 'border-amber-600',

                fondo: 'bg-amber-50',

                mensaje:
                    'Cromo cobalto seleccionado — código CC.',

                etapas: [
                    'Cromos'
                ]

            },


            ortodoncia: {

                borde: 'border-emerald-600',

                fondo: 'bg-emerald-50',

                mensaje:
                    'Aparatos de ortodoncia seleccionada — código AO.',

                etapas: [
                    'Ortodoncia'
                ]

            }

        };

        /*
        |--------------------------------------------------------------------------
        | LIMPIAR ESTILOS DE TARJETAS
        |--------------------------------------------------------------------------
        */

        function limpiarTarjetas() {

            cards.forEach(function (card) {

                card.classList.remove(
                    'border-blue-700',
                    'bg-blue-50',

                    'border-violet-700',
                    'bg-violet-50',

                    'border-amber-600',
                    'bg-amber-50',

                    'border-emerald-600',
                    'bg-emerald-50'
                );


                card.classList.add(
                    'border-slate-200',
                    'bg-white'
                );


                const check =
                    card.querySelector('.area-check');


                if (check) {

                    check.classList.add('hidden');

                }

            });

        }


        /*
        |--------------------------------------------------------------------------
        | SELECCIONAR ÁREA
        |--------------------------------------------------------------------------
        */

        function seleccionarArea(area) {

            const card =
                document.querySelector(
                    '[data-area="' + area + '"]'
                );


            if (
                !card
                ||
                !configuracion[area]
            ) {
                return;
            }


            limpiarTarjetas();


            /*
            | Guardar área
            */

            areaInput.value =area;

            /*
            | Estilo tarjeta seleccionada
            */

            card.classList.remove('border-slate-200', 'bg-white'
            );


            card.classList.add( configuracion[area].borde, configuracion[area].fondo
            );


            const check =
                card.querySelector(
                    '.area-check'
                );


            if (check) {

                check.classList.remove(
                    'hidden'
                );

            }


            /*
            | Mensaje
            */

            if (mensaje) { mensaje.textContent = configuracion[area].mensaje;

            }


            /*
            | Filtrar campos dependientes
            */

            areaInput.dispatchEvent(new Event('change', { bubbles: true }));

        }

        /*
        |--------------------------------------------------------------------------
        | EVENTOS
        |--------------------------------------------------------------------------
        */

        cards.forEach(function (card) {

            card.addEventListener(
                'click',
                function () {

                    seleccionarArea(
                        this.dataset.area
                    );

                }
            );

        });


        /*
        |--------------------------------------------------------------------------
        | RESTAURAR SELECCIÓN
        |--------------------------------------------------------------------------
        |
        | Importante si Laravel devuelve el formulario
        | por un error de validación o si se crea
        | una repetición.
        |
        */

        if (
            areaInput
            &&
            areaInput.value
        ) {

            seleccionarArea(
                areaInput.value
            );

        }

    });
</script>

@endsection