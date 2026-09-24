@extends('layouts.app')

@section('title', 'Nuevo Odontólogo | Laboratorio Dental')

@section('content')

<div class="max-w-5xl mx-auto">

    {{-- ===================================================== --}}
    {{-- ENCABEZADO --}}
    {{-- ===================================================== --}}

    <div class="mb-8">

        <a
            href="{{ route('administracion.odontologos.index') }}"
            class="text-sm font-semibold
                   text-blue-700
                   hover:underline"
        >
            ← Volver a Odontólogos
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
            Nuevo Odontólogo
        </h1>


        <p class="text-slate-500 mt-1">
            Registre la información del odontólogo
            y su clínica asociada.
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
        action="{{ route('administracion.odontologos.store') }}"
        class="bg-white
               border border-slate-200
               rounded-xl
               shadow-sm
               p-6"
    >

        @csrf


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
                    Nombre
                    <span class="text-red-500">*</span>
                </label>

                <input
                    type="text"
                    name="nombre"
                    id="nombre"
                    value="{{ old('nombre') }}"
                    maxlength="150"
                    required
                    placeholder="Ej. Dr. Carlos Pérez"
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


            {{-- CLÍNICA --}}
            <div>

                <label
                    for="clinica_id"
                    class="block
                           text-sm
                           font-semibold
                           text-slate-700
                           mb-2"
                >
                    Clínica
                </label>

                <select
                    name="clinica_id"
                    id="clinica_id"
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
                        Sin clínica asociada
                    </option>

                    @foreach($clinicas as $clinica)

                        <option
                            value="{{ $clinica->id }}"
                            @selected(
                                old('clinica_id')
                                == $clinica->id
                            )
                        >
                            {{ $clinica->nombre }}
                        </option>

                    @endforeach

                </select>

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
                    value="{{ old('telefono') }}"
                    maxlength="20"
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
                    value="{{ old('correo') }}"
                    maxlength="150"
                    placeholder="Ej. doctor@clinica.com"
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


            {{-- NÚMERO COLEGIADO --}}
            <div>

                <label
                    for="numero_colegiado"
                    class="block
                           text-sm
                           font-semibold
                           text-slate-700
                           mb-2"
                >
                    Número de colegiado
                </label>

                <input
                    type="text"
                    name="numero_colegiado"
                    id="numero_colegiado"
                    value="{{ old('numero_colegiado') }}"
                    maxlength="50"
                    placeholder="Ej. 12345"
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
        {{-- ACCIONES --}}
        {{-- ================================================= --}}

        <div class="flex
                    justify-end
                    gap-3
                    mt-8
                    pt-5
                    border-t border-slate-200">

            <a
                href="{{ route('administracion.odontologos.index') }}"
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
                Guardar Odontólogo
            </button>

        </div>

    </form>

</div>

@endsection