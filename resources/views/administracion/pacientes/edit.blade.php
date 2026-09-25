@extends('layouts.app')

@section('title', 'Editar Paciente | Laboratorio Dental')

@section('content')

<div class="max-w-5xl mx-auto">

    {{-- ===================================================== --}}
    {{-- ENCABEZADO --}}
    {{-- ===================================================== --}}

    <div class="mb-8">

        <a
            href="{{ route('administracion.pacientes.index') }}"
            class="text-sm
                   font-semibold
                   text-blue-700
                   hover:underline"
        >
            ← Volver a Pacientes
        </a>


        <p class="text-sm
                  font-semibold
                  text-blue-800
                  uppercase
                  mt-5">
            Administración
        </p>


        <h1 class="text-3xl
                   font-bold
                   text-slate-900">
            Editar Paciente
        </h1>


        <p class="text-slate-500 mt-1">
            Actualice la información del paciente.
        </p>

    </div>


    {{-- ===================================================== --}}
    {{-- ERRORES --}}
    {{-- ===================================================== --}}

    @if($errors->any())

        <div class="mb-6
                    bg-red-50
                    border border-red-200
                    rounded-lg
                    px-5 py-4">

            <p class="font-semibold
                      text-red-700
                      mb-2">
                Revise la información ingresada.
            </p>

            <ul class="text-sm
                       text-red-600
                       list-disc
                       ml-5">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ===================================================== --}}
    {{-- FORMULARIO --}}
    {{-- ===================================================== --}}

    <form
        method="POST"
        action="{{ route(
            'administracion.pacientes.update',
            $paciente
        ) }}"
        class="bg-white
               border border-slate-200
               rounded-xl
               shadow-sm
               p-6"
    >

        @csrf
        @method('PUT')


        <div class="mb-6">

            <h2 class="text-lg
                       font-bold
                       text-slate-900">
                Datos del paciente
            </h2>

            <p class="text-sm
                      text-slate-500
                      mt-1">
                Modifique únicamente la información necesaria.
            </p>

        </div>


        <div class="grid
                    grid-cols-1
                    md:grid-cols-2
                    gap-5">


            {{-- ODONTÓLOGO --}}
            <div class="md:col-span-2">

                <label
                    for="odontologo_id"
                    class="block
                           text-sm
                           font-semibold
                           text-slate-700
                           mb-2"
                >
                    Odontólogo

                    <span class="text-red-500">
                        *
                    </span>
                </label>


                <select
                    name="odontologo_id"
                    id="odontologo_id"
                    required
                    class="w-full
                           border border-slate-300
                           rounded-lg
                           px-4 py-2.5
                           bg-white
                           focus:outline-none
                           focus:ring-2
                           focus:ring-blue-200
                           focus:border-blue-500"
                >

                    <option value="">
                        Seleccione un odontólogo
                    </option>


                    @foreach($odontologos as $odontologo)

                        <option
                            value="{{ $odontologo->id }}"
                            @selected(
                                old(
                                    'odontologo_id',
                                    $paciente->odontologo_id
                                )
                                == $odontologo->id
                            )
                        >

                            {{ $odontologo->codigo_cliente }}
                            -
                            {{ $odontologo->nombre }}

                            @if($odontologo->clinica)
                                —
                                {{ $odontologo->clinica->nombre }}
                            @endif

                            @if(!$odontologo->estado)
                                — INACTIVO
                            @endif

                        </option>

                    @endforeach

                </select>

            </div>


            {{-- NOMBRE --}}
            <div>

                <label
                    for="nombre"
                    class="block
                           text-sm
                           font-semibold
                           text-slate-700
                           mb-2"
                >
                    Nombre

                    <span class="text-red-500">
                        *
                    </span>
                </label>


                <input
                    type="text"
                    name="nombre"
                    id="nombre"
                    required
                    maxlength="150"
                    value="{{ old(
                        'nombre',
                        $paciente->nombre
                    ) }}"
                    class="w-full
                           border border-slate-300
                           rounded-lg
                           px-4 py-2.5
                           focus:outline-none
                           focus:ring-2
                           focus:ring-blue-200
                           focus:border-blue-500"
                >

            </div>


            {{-- APELLIDO --}}
            <div>

                <label
                    for="apellido"
                    class="block
                           text-sm
                           font-semibold
                           text-slate-700
                           mb-2"
                >
                    Apellido
                </label>


                <input
                    type="text"
                    name="apellido"
                    id="apellido"
                    maxlength="150"
                    value="{{ old(
                        'apellido',
                        $paciente->apellido
                    ) }}"
                    class="w-full
                           border border-slate-300
                           rounded-lg
                           px-4 py-2.5
                           focus:outline-none
                           focus:ring-2
                           focus:ring-blue-200
                           focus:border-blue-500"
                >

            </div>


            {{-- TELÉFONO --}}
            <div>

                <label
                    for="telefono"
                    class="block
                           text-sm
                           font-semibold
                           text-slate-700
                           mb-2"
                >
                    Teléfono
                </label>


                <input
                    type="text"
                    name="telefono"
                    id="telefono"
                    maxlength="20"
                    value="{{ old(
                        'telefono',
                        $paciente->telefono
                    ) }}"
                    class="w-full
                           border border-slate-300
                           rounded-lg
                           px-4 py-2.5
                           focus:outline-none
                           focus:ring-2
                           focus:ring-blue-200
                           focus:border-blue-500"
                >

            </div>


            {{-- ESTADO --}}
            <div>

                <label
                    class="block
                           text-sm
                           font-semibold
                           text-slate-700
                           mb-2">
                    Estado actual
                </label>


                <div class="h-[42px]
                            flex items-center">

                    @if($paciente->estado)

                        <span class="inline-flex
                                     bg-emerald-100
                                     text-emerald-700
                                     px-3 py-1
                                     rounded-full
                                     text-xs
                                     font-semibold">
                            Activo
                        </span>

                    @else

                        <span class="inline-flex
                                     bg-red-100
                                     text-red-700
                                     px-3 py-1
                                     rounded-full
                                     text-xs
                                     font-semibold">
                            Inactivo
                        </span>

                    @endif

                </div>

            </div>


            {{-- OBSERVACIONES --}}
            <div class="md:col-span-2">

                <label
                    for="observaciones"
                    class="block
                           text-sm
                           font-semibold
                           text-slate-700
                           mb-2"
                >
                    Observaciones
                </label>


                <textarea
                    name="observaciones"
                    id="observaciones"
                    rows="4"
                    maxlength="1000"
                    class="w-full
                           border border-slate-300
                           rounded-lg
                           px-4 py-2.5
                           resize-none
                           focus:outline-none
                           focus:ring-2
                           focus:ring-blue-200
                           focus:border-blue-500"
                >{{ old(
                    'observaciones',
                    $paciente->observaciones
                ) }}</textarea>

            </div>

        </div>


        {{-- INFORMACIÓN --}}
        <div class="mt-8
                    bg-slate-50
                    border border-slate-200
                    rounded-xl
                    px-5 py-4">

            <p class="text-sm
                      font-semibold
                      text-slate-700">
                Órdenes asociadas
            </p>

            <p class="text-2xl
                      font-bold
                      text-violet-700
                      mt-1">

                {{ $paciente->ordenesTrabajo()->count() }}

            </p>

            <p class="text-xs
                      text-slate-500
                      mt-1">
                Las órdenes existentes conservarán la relación
                histórica con este paciente.
            </p>

        </div>


        {{-- ================================================= --}}
        {{-- ACCIONES --}}
        {{-- ================================================= --}}

        <div class="flex
                    flex-col-reverse
                    sm:flex-row
                    sm:justify-end
                    gap-3
                    mt-8
                    pt-5
                    border-t border-slate-200">

            <a
                href="{{ route(
                    'administracion.pacientes.index'
                ) }}"
                class="inline-flex
                       items-center
                       justify-center
                       px-5 py-2.5
                       border border-slate-300
                       rounded-lg
                       text-slate-600
                       hover:bg-slate-50"
            >
                Cancelar
            </a>


            <button
                type="submit"
                class="inline-flex
                       items-center
                       justify-center
                       px-5 py-2.5
                       bg-blue-800
                       hover:bg-blue-900
                       text-white
                       rounded-lg
                       font-semibold
                       shadow-sm"
            >
                Guardar Cambios
            </button>

        </div>

    </form>

</div>

@endsection