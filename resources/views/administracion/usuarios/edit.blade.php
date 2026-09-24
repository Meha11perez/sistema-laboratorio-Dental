@extends('layouts.app')

@section('title', 'Editar Usuario | Laboratorio Dental')

@section('content')

<div class="max-w-5xl mx-auto">

    {{-- =====================================================
         ENCABEZADO
    ====================================================== --}}
    <div class="mb-8">

        <a
            href="{{ route('administracion.usuarios.index') }}"
            class="text-sm font-semibold text-blue-700 hover:underline"
        >
            ← Volver a Usuarios
        </a>

        <p class="text-sm font-semibold
                  text-blue-800 uppercase mt-5">
            Administración
        </p>

        <h1 class="text-3xl font-bold text-slate-900">
            Editar Usuario
        </h1>

        <p class="text-slate-500 mt-1">
            Actualice los datos y permisos de acceso del usuario.
        </p>

    </div>


    {{-- =====================================================
         FORMULARIO
    ====================================================== --}}
    <form
        method="POST"
        action="{{ route(
            'administracion.usuarios.update',
            $user
        ) }}"
        class="bg-white
               border border-slate-200
               rounded-xl
               shadow-sm
               p-6"
    >

        @csrf
        @method('PUT')


        {{-- =================================================
             NOMBRE
        ================================================== --}}
        <div class="mb-5">

            <label
                for="name"
                class="block text-sm
                       font-semibold
                       text-slate-700 mb-2"
            >
                Nombre *
            </label>

            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name', $user->name) }}"
                class="w-full
                       border border-slate-300
                       rounded-lg
                       px-4 py-2.5
                       focus:outline-none
                       focus:ring-2
                       focus:ring-blue-200
                       focus:border-blue-500"
                required
            >

            @error('name')

                <p class="text-red-600 text-sm mt-1">
                    {{ $message }}
                </p>

            @enderror

        </div>


        {{-- =================================================
             CORREO
        ================================================== --}}
        <div class="mb-5">

            <label
                for="email"
                class="block text-sm
                       font-semibold
                       text-slate-700 mb-2"
            >
                Correo electrónico *
            </label>

            <input
                type="email"
                name="email"
                id="email"
                value="{{ old('email', $user->email) }}"
                class="w-full
                       border border-slate-300
                       rounded-lg
                       px-4 py-2.5
                       focus:outline-none
                       focus:ring-2
                       focus:ring-blue-200
                       focus:border-blue-500"
                required
            >

            @error('email')

                <p class="text-red-600 text-sm mt-1">
                    {{ $message }}
                </p>

            @enderror

        </div>


        {{-- =================================================
             ROL
        ================================================== --}}
        <div class="mb-6">

            <label
                for="role_id"
                class="block text-sm
                       font-semibold
                       text-slate-700 mb-2"
            >
                Rol *
            </label>

            <select
                name="role_id"
                id="role_id"
                class="w-full
                       border border-slate-300
                       rounded-lg
                       px-4 py-2.5
                       bg-white
                       focus:outline-none
                       focus:ring-2
                       focus:ring-blue-200
                       focus:border-blue-500"
                required
            >

                @foreach($roles as $role)

                    <option
                        value="{{ $role->id }}"
                        @selected(
                            old(
                                'role_id',
                                $user->role_id
                            ) == $role->id
                        )
                    >
                        {{ $role->nombre }}
                    </option>

                @endforeach

            </select>

            @error('role_id')

                <p class="text-red-600 text-sm mt-1">
                    {{ $message }}
                </p>

            @enderror

        </div>


        {{-- =================================================
             CAMBIAR CONTRASEÑA
        ================================================== --}}
        <div class="border-t border-slate-200 pt-6">

            <h2 class="font-bold text-slate-900">
                Cambiar contraseña
            </h2>

            <p class="text-sm text-slate-500 mt-1 mb-5">
                Deje ambos campos vacíos si no desea cambiar la contraseña.
            </p>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- NUEVA CONTRASEÑA --}}
                <div>

                    <label
                        for="password"
                        class="block text-sm
                               font-semibold
                               text-slate-700 mb-2"
                    >
                        Nueva contraseña
                    </label>

                    <div class="relative">

                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="w-full
                                   border border-slate-300
                                   rounded-lg
                                   px-4 py-2.5
                                   pr-20
                                   focus:outline-none
                                   focus:ring-2
                                   focus:ring-blue-200
                                   focus:border-blue-500"
                            autocomplete="new-password"
                        >

                        <button
                            type="button"
                            onclick="togglePassword(
                                'password',
                                this
                            )"
                            class="absolute
                                   right-3
                                   top-1/2
                                   -translate-y-1/2
                                   text-sm
                                   font-semibold
                                   text-blue-700
                                   hover:text-blue-900"
                        >
                            Ver
                        </button>

                    </div>

                    @error('password')

                        <p class="text-red-600 text-sm mt-1">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- CONFIRMAR CONTRASEÑA --}}
                <div>

                    <label
                        for="password_confirmation"
                        class="block text-sm
                               font-semibold
                               text-slate-700 mb-2"
                    >
                        Confirmar contraseña
                    </label>

                    <div class="relative">

                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            class="w-full
                                   border border-slate-300
                                   rounded-lg
                                   px-4 py-2.5
                                   pr-20
                                   focus:outline-none
                                   focus:ring-2
                                   focus:ring-blue-200
                                   focus:border-blue-500"
                            autocomplete="new-password"
                        >

                        <button
                            type="button"
                            onclick="togglePassword(
                                'password_confirmation',
                                this
                            )"
                            class="absolute
                                   right-3
                                   top-1/2
                                   -translate-y-1/2
                                   text-sm
                                   font-semibold
                                   text-blue-700
                                   hover:text-blue-900"
                        >
                            Ver
                        </button>

                    </div>

                </div>

            </div>

        </div>


        {{-- =================================================
             ACCIONES
        ================================================== --}}
        <div class="flex
                    justify-end
                    gap-3
                    mt-8
                    pt-5
                    border-t border-slate-200">

            <a
                href="{{ route('administracion.usuarios.index') }}"
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


<script>
    function togglePassword(id, button)
    {
        const input = document.getElementById(id);

        if (input.type === 'password') {

            input.type = 'text';

            button.textContent = 'Ocultar';

        } else {

            input.type = 'password';

            button.textContent = 'Ver';
        }
    }
</script>

@endsection