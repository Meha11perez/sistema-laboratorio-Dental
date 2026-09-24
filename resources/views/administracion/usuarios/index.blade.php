@extends('layouts.app')

@section('title', 'Usuarios | Laboratorio Dental')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- =====================================================
         ENCABEZADO
    ====================================================== --}}
    <div class="flex flex-col md:flex-row
                md:items-center md:justify-between
                gap-4 mb-8">

        <div>

            <p class="text-sm font-semibold
                      text-blue-800 uppercase">
                Administración
            </p>

            <h1 class="text-3xl font-bold text-slate-900">
                Usuarios
            </h1>

            <p class="text-slate-500 mt-1">
                Gestión de cuentas y accesos al sistema.
            </p>

        </div>
        
        <a
            href="{{ route('administracion.usuarios.create') }}"
            class="bg-blue-800
                hover:bg-blue-900
                text-white
                px-5 py-2.5
                rounded-lg
                font-semibold"
        >
            + Nuevo Usuario
        </a>

    </div>

    @if(session('success'))

    <div class="mb-5
                bg-emerald-50
                border border-emerald-200
                text-emerald-700
                rounded-lg
                px-4 py-3">

        {{ session('success') }}

    </div>

        @endif

        @if(session('error'))

            <div class="mb-5
                        bg-red-50
                        border border-red-200
                        text-red-700
                        rounded-lg
                        px-4 py-3">

                {{ session('error') }}

            </div>

        @endif
    {{-- =====================================================
         INDICADORES
    ====================================================== --}}
    <div class="grid grid-cols-1
                md:grid-cols-2
                xl:grid-cols-4
                gap-5 mb-8">

        <div class="bg-white
                    border border-slate-200
                    border-l-4 border-l-blue-600
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase
                      font-semibold text-slate-500">
                Total usuarios
            </p>

            <p class="text-3xl font-bold
                      text-blue-700 mt-2">
                {{ $totalUsuarios }}
            </p>

        </div>


        <div class="bg-white
                    border border-slate-200
                    border-l-4 border-l-emerald-500
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase
                      font-semibold text-slate-500">
                Activos
            </p>

            <p class="text-3xl font-bold
                      text-emerald-600 mt-2">
                {{ $usuariosActivos }}
            </p>

        </div>


        <div class="bg-white
                    border border-slate-200
                    border-l-4 border-l-red-500
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase
                      font-semibold text-slate-500">
                Inactivos
            </p>

            <p class="text-3xl font-bold
                      text-red-600 mt-2">
                {{ $usuariosInactivos }}
            </p>

        </div>


        <div class="bg-white
                    border border-slate-200
                    border-l-4 border-l-violet-500
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase
                      font-semibold text-slate-500">
                Roles activos
            </p>

            <p class="text-3xl font-bold
                      text-violet-600 mt-2">
                {{ $rolesActivos }}
            </p>

        </div>

    </div>


    {{-- =====================================================
         FILTROS
    ====================================================== --}}
    <form
        method="GET"
        action="{{ route('administracion.usuarios.index') }}"
        class="bg-white
               border border-slate-200
               rounded-xl shadow-sm
               p-5 mb-8"
    >

        <div class="grid grid-cols-1
                    md:grid-cols-3 gap-4">

            {{-- BUSCAR --}}
            <div>

                <label class="block text-xs
                              font-semibold
                              text-slate-600 mb-2">
                    Buscar
                </label>

                <input
                    type="text"
                    name="buscar"
                    value="{{ request('buscar') }}"
                    placeholder="Nombre, correo o rol..."
                    class="w-full
                           border border-slate-300
                           rounded-lg
                           px-4 py-2.5"
                >

            </div>


            {{-- ROL --}}
            <div>

                <label class="block text-xs
                              font-semibold
                              text-slate-600 mb-2">
                    Rol
                </label>

                <select
                    name="rol"
                    class="w-full
                           border border-slate-300
                           rounded-lg
                           px-4 py-2.5
                           bg-white"
                >

                    <option value="">
                        Todos
                    </option>

                    @foreach($roles as $role)

                        <option
                            value="{{ $role->id }}"
                            @selected(
                                request('rol') == $role->id
                            )
                        >
                            {{ $role->nombre }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- ESTADO --}}
            <div>

                <label class="block text-xs
                              font-semibold
                              text-slate-600 mb-2">
                    Estado
                </label>

                <select
                    name="estado"
                    class="w-full
                           border border-slate-300
                           rounded-lg
                           px-4 py-2.5
                           bg-white"
                >

                    <option value="">
                        Todos
                    </option>

                    <option
                        value="activo"
                        @selected(
                            request('estado') === 'activo'
                        )
                    >
                        Activos
                    </option>

                    <option
                        value="inactivo"
                        @selected(
                            request('estado') === 'inactivo'
                        )
                    >
                        Inactivos
                    </option>

                </select>

            </div>

        </div>


        <div class="flex justify-end gap-3 mt-5">

            <a
                href="{{ route(
                    'administracion.usuarios.index'
                ) }}"
                class="px-5 py-2.5
                       border border-slate-300
                       rounded-lg
                       text-slate-600
                       hover:bg-slate-50"
            >
                Limpiar
            </a>

            <button
                type="submit"
                class="px-5 py-2.5
                       bg-blue-800
                       text-white
                       rounded-lg
                       font-semibold
                       hover:bg-blue-900"
            >
                Filtrar
            </button>

        </div>

    </form>


    {{-- =====================================================
         TABLA
    ====================================================== --}}
    <div class="bg-white
                border border-slate-200
                rounded-xl
                shadow-sm
                overflow-hidden">

        <div class="px-6 py-5
                    border-b border-slate-200">

            <h2 class="font-bold text-slate-900">
                Cuentas registradas
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Usuarios con acceso al sistema.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50
                              text-xs uppercase
                              text-slate-500">

                    <tr>

                        <th class="text-left px-6 py-4">
                            Usuario
                        </th>

                        <th class="text-left px-6 py-4">
                            Rol
                        </th>

                        <th class="text-left px-6 py-4">
                            Estado
                        </th>

                        <th class="text-right px-6 py-4">
                            Acción
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($usuarios as $user)

                        <tr class="hover:bg-slate-50">

                            {{-- USUARIO --}}
                            <td class="px-6 py-4">

                                <p class="font-semibold
                                          text-slate-900">
                                    {{ $user->name }}
                                </p>

                                <p class="text-xs
                                          text-slate-400
                                          mt-1">
                                    {{ $user->email }}
                                </p>

                            </td>


                            {{-- ROL --}}
                            <td class="px-6 py-4">

                                <span class="inline-flex
                                             bg-blue-50
                                             text-blue-700
                                             px-3 py-1
                                             rounded-full
                                             text-xs
                                             font-semibold">

                                    {{ $user->role?->nombre
                                        ?? 'Sin rol' }}

                                </span>

                            </td>


                            {{-- ESTADO --}}
                            <td class="px-6 py-4">

                                @if($user->estado)

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

                            </td>

                            {{-- ACCIÓN --}}
                            <td class="px-6 py-4">
                            <div class="flex
                                        justify-end
                                        items-center
                                        gap-4">

                                {{-- EDITAR --}}
                                <a
                                    href="{{ route(
                                        'administracion.usuarios.edit',
                                        $user
                                    ) }}"
                                    class="text-blue-700
                                        font-semibold
                                        hover:underline"
                                >
                                    Editar
                                </a>


                                {{-- NO PERMITIR DESACTIVAR TU PROPIA CUENTA --}}
                                @if(auth()->id() !== $user->id)

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'administracion.usuarios.estado',
                                            $user
                                        ) }}"
                                    >

                                        @csrf
                                        @method('PATCH')


                                        <button
                                            type="submit"
                                            onclick="return confirm(
                                                '{{ $user->estado
                                                    ? '¿Desea desactivar este usuario?'
                                                    : '¿Desea activar este usuario?'
                                                }}'
                                            )"
                                            class="
                                                font-semibold
                                                hover:underline

                                                {{ $user->estado
                                                    ? 'text-red-600'
                                                    : 'text-emerald-600'
                                                }}
                                            "
                                        >

                                            {{ $user->estado
                                                ? 'Desactivar'
                                                : 'Activar'
                                            }}

                                        </button>

                                    </form>

                                @else

                                    <span class="text-xs text-slate-400">
                                        Tu cuenta
                                    </span>

                                @endif

                            </div>

                        </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="4"
                                class="px-6 py-12
                                       text-center
                                       text-slate-400"
                            >
                                No se encontraron usuarios.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($usuarios->hasPages())

            <div class="px-6 py-4
                        border-t border-slate-200">

                {{ $usuarios->links() }}

            </div>

        @endif

    </div>

</div>

@endsection