@extends('layouts.app')

@section('title', 'Nuevo Técnico | Laboratorio Dental')

@section('content')

<div class="max-w-5xl mx-auto">

    {{-- ENCABEZADO --}}
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
            Nuevo Técnico
        </h1>

        <p class="text-slate-500 mt-1">
            Asocie un usuario técnico y configure
            su información de producción.
        </p>

    </div>


    {{-- ERRORES --}}
    @if($errors->any())

        <div class="mb-6
                    bg-red-50
                    border border-red-200
                    rounded-lg
                    px-5 py-4">

            <p class="font-semibold text-red-700 mb-2">
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


    <form
        method="POST"
        action="{{ route(
            'administracion.tecnicos.store'
        ) }}"
        class="bg-white
               border border-slate-200
               rounded-xl
               shadow-sm
               p-6"
    >

        @csrf


        {{-- USUARIO --}}
        <div class="mb-6">

            <label
                for="user_id"
                class="block
                       text-sm
                       font-semibold
                       text-slate-700
                       mb-2"
            >
                Usuario técnico *
            </label>

            <select
                name="user_id"
                id="user_id"
                required
                class="w-full
                       border border-slate-300
                       rounded-lg
                       px-4 py-2.5
                       bg-white"
            >

                <option value="">
                    Seleccione un usuario
                </option>

                @foreach($usuarios as $usuario)

                    <option
                        value="{{ $usuario->id }}"
                        @selected(
                            old('user_id')
                            == $usuario->id
                        )
                    >
                        {{ $usuario->name }}
                        — {{ $usuario->email }}
                    </option>

                @endforeach

            </select>


            @if($usuarios->isEmpty())

                <p class="text-sm
                          text-amber-600
                          mt-2">
                    No hay usuarios con rol Técnico
                    disponibles para asociar.
                </p>

            @endif

        </div>


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
                    value="{{ old('especialidad') }}"
                    placeholder="Ej. Ortodoncia"
                    maxlength="150"
                    class="w-full
                           border border-slate-300
                           rounded-lg
                           px-4 py-2.5"
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
                    value="{{ old('telefono') }}"
                    placeholder="Ej. 5555-5555"
                    maxlength="20"
                    class="w-full
                           border border-slate-300
                           rounded-lg
                           px-4 py-2.5"
                >

            </div>


            {{-- FECHA --}}
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
                    value="{{ old('fecha_ingreso') }}"
                    class="w-full
                           border border-slate-300
                           rounded-lg
                           px-4 py-2.5"
                >

            </div>

        </div>


        {{-- ETAPAS --}}
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
                      mt-1 mb-5">
                Seleccione las etapas que este técnico
                puede realizar.
            </p>


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
                                    old(
                                        'etapas',
                                        []
                                    )
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


        {{-- ACCIONES --}}
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
                @disabled($usuarios->isEmpty())
                class="px-5
                       py-2.5
                       bg-blue-800
                       hover:bg-blue-900
                       disabled:bg-slate-300
                       disabled:cursor-not-allowed
                       text-white
                       rounded-lg
                       font-semibold"
            >
                Guardar Técnico
            </button>

        </div>

    </form>

</div>

@endsection