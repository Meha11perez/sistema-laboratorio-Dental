@extends('layouts.app')

@section('title', 'Editar Clínica | Laboratorio Dental')

@section('content')

<div class="max-w-5xl mx-auto">

    {{-- ===================================================== --}}
    {{-- ENCABEZADO --}}
    {{-- ===================================================== --}}

    <div class="mb-8">

        <a
            href="{{ route('administracion.clinicas.index') }}"
            class="text-sm
                   font-semibold
                   text-blue-700
                   hover:underline"
        >
            ← Volver a Clínicas
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
            Editar Clínica
        </h1>


        <p class="text-slate-500 mt-1">
            Actualice la información general y de contacto de la clínica.
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
            'administracion.clinicas.update',
            $clinica
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
        {{-- DATOS GENERALES --}}
        {{-- ================================================= --}}

        <div class="mb-6">

            <h2 class="text-lg
                       font-bold
                       text-slate-900">
                Datos de la clínica
            </h2>

            <p class="text-sm
                      text-slate-500
                      mt-1">
                Información principal de identificación y contacto.
            </p>

        </div>


        <div class="grid
                    grid-cols-1
                    md:grid-cols-2
                    gap-5">


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
                    Nombre de la clínica

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
                        $clinica->nombre
                    ) }}"
                    placeholder="Ej. Clínica Dental Sonrisas"
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


            {{-- NIT --}}
            <div>

                <label
                    for="nit"
                    class="block
                           text-sm
                           font-semibold
                           text-slate-700
                           mb-2"
                >
                    NIT
                </label>


                <input
                    type="text"
                    name="nit"
                    id="nit"
                    maxlength="30"
                    value="{{ old(
                        'nit',
                        $clinica->nit
                    ) }}"
                    placeholder="Ej. 1234567-8"
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


            {{-- ASISTENTE / SECRETARIA --}}
            <div>

                <label
                    for="asistente_secretaria"
                    class="block
                           text-sm
                           font-semibold
                           text-slate-700
                           mb-2"
                >
                    Asistente o secretaria
                </label>


                <input
                    type="text"
                    name="asistente_secretaria"
                    id="asistente_secretaria"
                    maxlength="150"
                    value="{{ old(
                        'asistente_secretaria',
                        $clinica->asistente_secretaria
                    ) }}"
                    placeholder="Ej. María López"
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
                    Teléfono de clínica
                </label>


                <input
                    type="text"
                    name="telefono"
                    id="telefono"
                    maxlength="20"
                    value="{{ old(
                        'telefono',
                        $clinica->telefono
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


            {{-- CORREO --}}
            <div>

                <label
                    for="correo"
                    class="block
                           text-sm
                           font-semibold
                           text-slate-700
                           mb-2"
                >
                    Correo electrónico
                </label>


                <input
                    type="email"
                    name="correo"
                    id="correo"
                    maxlength="150"
                    value="{{ old(
                        'correo',
                        $clinica->correo
                    ) }}"
                    placeholder="Ej. contacto@clinica.com"
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


            {{-- DEPARTAMENTO --}}
            <div>

                <label
                    for="departamento"
                    class="block
                           text-sm
                           font-semibold
                           text-slate-700
                           mb-2"
                >
                    Departamento
                </label>


                <input
                    type="text"
                    name="departamento"
                    id="departamento"
                    maxlength="100"
                    value="{{ old(
                        'departamento',
                        $clinica->departamento
                    ) }}"
                    placeholder="Ej. Guatemala"
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


            {{-- MUNICIPIO --}}
            <div>

                <label
                    for="municipio"
                    class="block
                           text-sm
                           font-semibold
                           text-slate-700
                           mb-2"
                >
                    Municipio
                </label>


                <input
                    type="text"
                    name="municipio"
                    id="municipio"
                    maxlength="100"
                    value="{{ old(
                        'municipio',
                        $clinica->municipio
                    ) }}"
                    placeholder="Ej. Mixco"
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


            {{-- DIRECCIÓN EXACTA --}}
            <div class="md:col-span-2">

                <label
                    for="direccion"
                    class="block
                           text-sm
                           font-semibold
                           text-slate-700
                           mb-2"
                >
                    Dirección exacta
                </label>


                <textarea
                    name="direccion"
                    id="direccion"
                    rows="3"
                    maxlength="255"
                    placeholder="Ej. 7a Avenida 8-40, Zona 3, Colonia Monserrat..."
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
                    'direccion',
                    $clinica->direccion
                ) }}</textarea>

            </div>

        </div>


        {{-- ================================================= --}}
        {{-- INFORMACIÓN ADICIONAL --}}
        {{-- ================================================= --}}

        <div class="mt-8
                    bg-slate-50
                    border border-slate-200
                    rounded-xl
                    px-5 py-4">

            <div class="flex
                        flex-col
                        sm:flex-row
                        sm:items-center
                        sm:justify-between
                        gap-3">

                <div>

                    <p class="text-sm
                              font-semibold
                              text-slate-700">
                        Odontólogos asociados
                    </p>

                    <p class="text-xs
                              text-slate-500
                              mt-1">
                        Los odontólogos asociados no se eliminan
                        al modificar la clínica.
                    </p>

                </div>


                <div class="inline-flex
                            items-center
                            justify-center
                            bg-violet-100
                            text-violet-700
                            rounded-xl
                            px-5 py-2">

                    <span class="text-2xl font-bold">
                        {{ $clinica->odontologos()->count() }}
                    </span>

                </div>

            </div>

        </div>


        {{-- ================================================= --}}
        {{-- ESTADO ACTUAL --}}
        {{-- ================================================= --}}

        <div class="mt-5
                    border border-slate-200
                    rounded-xl
                    px-5 py-4">

            <p class="text-xs
                      uppercase
                      font-semibold
                      text-slate-500">
                Estado actual
            </p>


            <div class="mt-2">

                @if($clinica->estado)

                    <span class="inline-flex
                                 bg-emerald-100
                                 text-emerald-700
                                 px-3 py-1
                                 rounded-full
                                 text-xs
                                 font-semibold">
                        Activa
                    </span>

                @else

                    <span class="inline-flex
                                 bg-red-100
                                 text-red-700
                                 px-3 py-1
                                 rounded-full
                                 text-xs
                                 font-semibold">
                        Inactiva
                    </span>

                @endif

            </div>


            <p class="text-xs
                      text-slate-400
                      mt-2">
                El estado se administra desde el listado de clínicas.
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
                    'administracion.clinicas.index'
                ) }}"
                class="inline-flex
                       items-center
                       justify-center
                       px-5 py-2.5
                       border border-slate-300
                       rounded-lg
                       text-slate-600
                       hover:bg-slate-50
                       transition"
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
                       shadow-sm
                       transition"
            >
                Guardar Cambios
            </button>

        </div>

    </form>

</div>

@endsection