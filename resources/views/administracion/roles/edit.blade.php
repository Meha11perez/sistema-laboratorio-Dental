@extends('layouts.app')

@section('title', 'Editar Rol | Laboratorio Dental')

@section('content')

<div class="max-w-5xl mx-auto">

    <div class="mb-8">

        <a
            href="{{ route('administracion.roles.index') }}"
            class="text-sm font-semibold text-blue-700 hover:underline"
        >
            ← Volver a Roles
        </a>

        <p class="text-sm font-semibold
                  text-blue-800 uppercase mt-5">
            Administración
        </p>

        <h1 class="text-3xl font-bold text-slate-900">
            Editar Rol
        </h1>

        <p class="text-slate-500 mt-1">
            Modifique la información descriptiva del rol.
        </p>

    </div>


    <form
        method="POST"
        action="{{ route(
            'administracion.roles.update',
            $role
        ) }}"
        class="bg-white
               border border-slate-200
               rounded-xl
               shadow-sm p-6"
    >

        @csrf
        @method('PUT')


        {{-- NOMBRE --}}
        <div class="mb-5">

            <label class="block text-sm
                          font-semibold
                          text-slate-700 mb-2">
                Nombre del rol
            </label>

            <input
                type="text"
                value="{{ $role->nombre }}"
                class="w-full
                       border border-slate-200
                       bg-slate-100
                       text-slate-500
                       rounded-lg
                       px-4 py-2.5"
                disabled
            >

            <p class="text-xs text-slate-400 mt-2">
                El nombre del rol está protegido porque es utilizado
                por las reglas de acceso del sistema.
            </p>

        </div>


        {{-- DESCRIPCIÓN --}}
        <div class="mb-5">

            <label
                for="descripcion"
                class="block text-sm
                       font-semibold
                       text-slate-700 mb-2"
            >
                Descripción
            </label>

            <textarea
                name="descripcion"
                id="descripcion"
                rows="4"
                maxlength="255"
                class="w-full
                       border border-slate-300
                       rounded-lg
                       px-4 py-2.5
                       focus:outline-none
                       focus:ring-2
                       focus:ring-blue-200
                       focus:border-blue-500"
                placeholder="Descripción del rol..."
            >{{ old('descripcion', $role->descripcion) }}</textarea>

            @error('descripcion')
                <p class="text-red-600 text-sm mt-1">
                    {{ $message }}
                </p>
            @enderror

        </div>


        {{-- ESTADO ACTUAL --}}
        <div class="bg-slate-50
                    border border-slate-200
                    rounded-lg
                    p-4">

            <p class="text-xs uppercase
                      font-semibold text-slate-500">
                Estado actual
            </p>

            <div class="mt-2">

                @if($role->estado)

                    <span class="inline-flex
                                 bg-emerald-100
                                 text-emerald-700
                                 px-3 py-1
                                 rounded-full
                                 text-xs font-semibold">
                        Activo
                    </span>

                @else

                    <span class="inline-flex
                                 bg-red-100
                                 text-red-700
                                 px-3 py-1
                                 rounded-full
                                 text-xs font-semibold">
                        Inactivo
                    </span>

                @endif

            </div>

        </div>


        {{-- ACCIONES --}}
        <div class="flex justify-end
                    gap-3 mt-8 pt-5
                    border-t border-slate-200">

            <a
                href="{{ route('administracion.roles.index') }}"
                class="px-5 py-2.5
                       border border-slate-300
                       rounded-lg
                       text-slate-600
                       hover:bg-slate-50"
            >
                Cancelar
            </a>

            <button
                type="submit"
                class="px-5 py-2.5
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