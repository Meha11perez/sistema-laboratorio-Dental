@extends('layouts.app')

@section('title', 'Editar Técnico | Laboratorio Dental')

@section('content')

<div class="max-w-5xl mx-auto">

    {{-- ===================================================== --}}
    {{-- ENCABEZADO --}}
    {{-- ===================================================== --}}

    <div class="mb-8">

        <a
            href="{{ route('administracion.tecnicos.index') }}"
            class="text-sm
                   font-semibold
                   text-blue-700
                   hover:underline"
        >
            ← Volver a Técnicos
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
            Editar Técnico
        </h1>


        <p class="text-slate-500 mt-1">
            Actualice la información y las etapas
            asignadas al técnico.
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
            'administracion.tecnicos.update',
            $tecnico
        ) }}"
        class="bg-white
               border border-slate-200
               rounded-xl
               shadow-sm
               p-6"
    >

        @csrf
        @method('PUT')


        {{-- ================================================= --}}
        {{-- USUARIO ASOCIADO --}}
        {{-- ================================================= --}}

        <div class="mb-6">

            <label
                class="block
                       text-sm
                       font-semibold
                       text-slate-700
                       mb-2"
            >
                Usuario asociado
            </label>


            <div class="bg-slate-50
                        border border-slate-200
                        rounded-lg
                        px-4 py-3">

                <p class="font-semibold
                          text-slate-800">

                    {{ $tecnico->user?->name
                        ?? 'Sin usuario asociado' }}

                </p>


                <p class="text-sm
                          text-slate-500
                          mt-1">

                    {{ $tecnico->user?->email
                        ?? '—' }}

                </p>

            </div>


            <p class="text-xs
                      text-slate-400
                      mt-2">
                El usuario asociado no se modifica
                desde esta pantalla.
            </p>

        </div>


        {{-- ================================================= --}}
        {{-- DATOS DEL TÉCNICO --}}
        {{-- ================================================= --}}

        <div class="grid
                    grid-cols-1
                    md:grid-cols-2
                    gap-5">


            {{-- ESPECIALIDAD --}}
            <div>

                <label
                    for="especialidad"
                    class="block
                           text-sm
                           font-semibold
                           text-slate-700
                           mb-2"
                >
                    Especialidad
                </label>


                <input
                    type="text"
                    name="especialidad"
                    id="especialidad"
                    maxlength="150"
                    value="{{ old(
                        'especialidad',
                        $tecnico->especialidad
                    ) }}"
                    placeholder="Ej. Ortodoncia"
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
                        $tecnico->telefono
                    ) }}"
                    placeholder="Ej. 5555-5555"
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


            {{-- FECHA DE INGRESO --}}
            <div>

                <label
                    for="fecha_ingreso"
                    class="block
                           text-sm
                           font-semibold
                           text-slate-700
                           mb-2"
                >
                    Fecha de ingreso
                </label>


                <input
                    type="date"
                    name="fecha_ingreso"
                    id="fecha_ingreso"
                    value="{{ old(
                        'fecha_ingreso',
                        $tecnico->fecha_ingreso
                            ?->format('Y-m-d')
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

        </div>


        {{-- ================================================= --}}
        {{-- ETAPAS DE PRODUCCIÓN --}}
        {{-- ================================================= --}}

        <div class="mt-8
                    pt-6
                    border-t border-slate-200">


            <h2 class="text-lg
                       font-bold
                       text-slate-900">
                Etapas de producción
            </h2>


            <p class="text-sm
                      text-slate-500
                      mt-1
                      mb-5">
                Seleccione las etapas que este técnico
                puede realizar.
            </p>


            @php

                $etapasSeleccionadas = old(
                    'etapas',
                    $tecnico
                        ->etapasProduccion
                        ->pluck('id')
                        ->toArray()
                );

            @endphp


            <div class="grid
                        grid-cols-1
                        md:grid-cols-2
                        gap-3">


                @foreach($etapas as $etapa)

                    <label
                        class="flex
                               items-start
                               gap-3
                               border border-slate-200
                               rounded-lg
                               p-4
                               cursor-pointer
                               hover:bg-slate-50"
                    >


                        <input
                            type="checkbox"
                            name="etapas[]"
                            value="{{ $etapa->id }}"
                            @checked(
                                in_array(
                                    $etapa->id,
                                    $etapasSeleccionadas
                                )
                            )
                            class="mt-1
                                   rounded
                                   border-slate-300
                                   text-blue-700"
                        >


                        <div>

                            <p class="font-semibold
                                      text-slate-800">

                                {{ $etapa->nombre }}

                            </p>


                            @if($etapa->descripcion)

                                <p class="text-xs
                                          text-slate-500
                                          mt-1">

                                    {{ $etapa->descripcion }}

                                </p>

                            @endif

                        </div>

                    </label>

                @endforeach

            </div>

        </div>


        {{-- ================================================= --}}
        {{-- ACCIONES --}}
        {{-- ================================================= --}}

        <div class="flex
                    justify-end
                    gap-3
                    mt-8
                    pt-5
                    border-t border-slate-200">


            <a
                href="{{ route(
                    'administracion.tecnicos.index'
                ) }}"
                class="px-5
                       py-2.5
                       border border-slate-300
                       rounded-lg
                       text-slate-600
                       hover:bg-slate-50"
            >
                Cancelar
            </a>


            <button
                type="submit"
                class="px-5
                       py-2.5
                       bg-blue-800
                       hover:bg-blue-900
                       text-white
                       rounded-lg
                       font-semibold"
            >
                Guardar Cambios
            </button>

        </div>

    </form>

</div>

@endsection