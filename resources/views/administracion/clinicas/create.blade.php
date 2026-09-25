@extends('layouts.app')

@section('title', 'Nueva Clínica | Laboratorio Dental')

@section('content')

<div class="max-w-5xl mx-auto">

    {{-- ENCABEZADO --}}
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
            Nueva Clínica
        </h1>

        <p class="text-slate-500 mt-1">
            Registre la información de la clínica.
        </p>

    </div>


    {{-- ERRORES --}}
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


    {{-- FORMULARIO --}}
    <form
        method="POST"
        action="{{ route('administracion.clinicas.store') }}"
        class="bg-white
               border border-slate-200
               rounded-xl
               shadow-sm
               p-6"
    >

        @csrf

        {{-- NOMBRE --}}
            <div>

                <label
                    for="nombre"
                    class="block text-sm font-semibold
                        text-slate-700 mb-2"
                >
                    Nombre de la clínica
                    <span class="text-red-500">*</span>
                </label>

                <input
                    type="text"
                    name="nombre"
                    id="nombre"
                    value="{{ old('nombre') }}"
                    maxlength="150"
                    required
                    placeholder="Ej. Clínica Dental Sonrisas"
                    class="w-full border border-slate-300
                        rounded-lg px-4 py-2.5"
                >

            </div>


            {{-- NIT --}}
            <div>

                <label
                    for="nit"
                    class="block text-sm font-semibold
                        text-slate-700 mb-2"
                >
                    NIT
                </label>

                <input
                    type="text"
                    name="nit"
                    id="nit"
                    value="{{ old('nit') }}"
                    maxlength="30"
                    placeholder="Ej. 1234567-8"
                    class="w-full border border-slate-300
                        rounded-lg px-4 py-2.5"
                >

            </div>


            {{-- ASISTENTE --}}
            <div>

                <label
                    for="asistente_secretaria"
                    class="block text-sm font-semibold
                        text-slate-700 mb-2"
                >
                    Asistente o secretaria
                </label>

                <input
                    type="text"
                    name="asistente_secretaria"
                    id="asistente_secretaria"
                    value="{{ old('asistente_secretaria') }}"
                    maxlength="150"
                    placeholder="Ej. María López"
                    class="w-full border border-slate-300
                        rounded-lg px-4 py-2.5"
                >

            </div>


            {{-- TELÉFONO --}}
            <div>

                <label
                    for="telefono"
                    class="block text-sm font-semibold
                        text-slate-700 mb-2"
                >
                    Teléfono de clínica
                </label>

                <input
                    type="text"
                    name="telefono"
                    id="telefono"
                    value="{{ old('telefono') }}"
                    maxlength="20"
                    placeholder="Ej. 5555-5555"
                    class="w-full border border-slate-300
                        rounded-lg px-4 py-2.5"
                >

            </div>


            {{-- CORREO --}}
            <div>

                <label
                    for="correo"
                    class="block text-sm font-semibold
                        text-slate-700 mb-2"
                >
                    Correo electrónico
                </label>

                <input
                    type="email"
                    name="correo"
                    id="correo"
                    value="{{ old('correo') }}"
                    maxlength="150"
                    placeholder="Ej. contacto@clinica.com"
                    class="w-full border border-slate-300
                        rounded-lg px-4 py-2.5"
                >

            </div>


            {{-- DEPARTAMENTO --}}
            <div>

                <label
                    for="departamento"
                    class="block text-sm font-semibold
                        text-slate-700 mb-2"
                >
                    Departamento
                </label>

                <input
                    type="text"
                    name="departamento"
                    id="departamento"
                    value="{{ old('departamento') }}"
                    maxlength="100"
                    placeholder="Ej. Guatemala"
                    class="w-full border border-slate-300
                        rounded-lg px-4 py-2.5"
                >

            </div>


            {{-- MUNICIPIO --}}
            <div>

                <label
                    for="municipio"
                    class="block text-sm font-semibold
                        text-slate-700 mb-2"
                >
                    Municipio
                </label>

                <input
                    type="text"
                    name="municipio"
                    id="municipio"
                    value="{{ old('municipio') }}"
                    maxlength="100"
                    placeholder="Ej. Mixco"
                    class="w-full border border-slate-300
                        rounded-lg px-4 py-2.5"
                >

            </div>


            {{-- DIRECCIÓN --}}
            <div class="md:col-span-2">

                <label
                    for="direccion"
                    class="block text-sm font-semibold
                        text-slate-700 mb-2"
                >
                    Dirección exacta
                </label>

                <textarea
                    name="direccion"
                    id="direccion"
                    rows="3"
                    maxlength="255"
                    placeholder="Ej. 7a Avenida 8-40, Zona 3..."
                    class="w-full border border-slate-300
                        rounded-lg px-4 py-2.5"
                >{{ old('direccion') }}</textarea>

            </div>
        {{-- ACCIONES --}}
        <div class="flex justify-end gap-3 mt-8 pt-5 border-t border-slate-200">
            
            <a
                href="{{ route('administracion.clinicas.index') }}"
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
                Guardar Clínica
            </button>

        </div>

    </form>

</div>

@endsection