@extends('layouts.app')

@section('title', 'Roles | Laboratorio Dental')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- ENCABEZADO --}}
    <div class="mb-8">

        <p class="text-sm font-semibold
                  text-blue-800 uppercase">
            Administración
        </p>

        <h1 class="text-3xl font-bold text-slate-900">
            Roles
        </h1>

        <p class="text-slate-500 mt-1">
            Consulta los perfiles de acceso utilizados por el sistema.
        </p>
        
    </div>

    {{-- INDICADORES --}}
    <div class="grid grid-cols-1
                md:grid-cols-3
                gap-5 mb-8">

        {{-- TOTAL --}}
        <div class="bg-white
                    border border-slate-200
                    border-l-4 border-l-blue-600
                    rounded-xl
                    shadow-sm
                    p-5">

            <p class="text-xs uppercase
                      font-semibold
                      text-slate-500">
                Total roles
            </p>

            <p class="text-3xl
                      font-bold
                      text-blue-700
                      mt-2">
                {{ $totalRoles }}
            </p>

        </div>


        {{-- ACTIVOS --}}
        <div class="bg-white
                    border border-slate-200
                    border-l-4 border-l-emerald-500
                    rounded-xl
                    shadow-sm
                    p-5">

            <p class="text-xs uppercase
                      font-semibold
                      text-slate-500">
                Activos
            </p>

            <p class="text-3xl
                      font-bold
                      text-emerald-600
                      mt-2">
                {{ $rolesActivos }}
            </p>

        </div>


        {{-- INACTIVOS --}}
        <div class="bg-white
                    border border-slate-200
                    border-l-4 border-l-red-500
                    rounded-xl
                    shadow-sm
                    p-5">

            <p class="text-xs uppercase
                      font-semibold
                      text-slate-500">
                Inactivos
            </p>

            <p class="text-3xl
                      font-bold
                      text-red-600
                      mt-2">
                {{ $rolesInactivos }}
            </p>

        </div>

    </div>


    {{-- FILTROS --}}
    <form
        method="GET"
        action="{{ route('administracion.roles.index') }}"
        class="bg-white
               border border-slate-200
               rounded-xl
               shadow-sm
               p-5 mb-8"
    >

        <div class="grid grid-cols-1
                    md:grid-cols-2 gap-4">

            {{-- BUSCAR --}}
            <div>

                <label class="block
                              text-xs
                              font-semibold
                              text-slate-600
                              mb-2">
                    Buscar
                </label>

                <input
                    type="text"
                    name="buscar"
                    value="{{ request('buscar') }}"
                    placeholder="Nombre o descripción..."
                    class="w-full
                           border border-slate-300
                           rounded-lg
                           px-4 py-2.5"
                >

            </div>


            {{-- ESTADO --}}
            <div>

                <label class="block
                              text-xs
                              font-semibold
                              text-slate-600
                              mb-2">
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


        <div class="flex justify-end
                    gap-3 mt-5">

            <a
                href="{{ route('administracion.roles.index') }}"
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


    {{-- TABLA --}}
    <div class="bg-white
                border border-slate-200
                rounded-xl
                shadow-sm
                overflow-hidden">

        <div class="px-6 py-5
                    border-b border-slate-200">

            <h2 class="font-bold text-slate-900">
                Roles del sistema
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Perfiles utilizados para controlar el acceso
                a las funciones del sistema.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50
                              text-xs uppercase
                              text-slate-500">

                    <tr>

                        <th class="text-left px-6 py-4">
                            Rol
                        </th>

                        <th class="text-left px-6 py-4">
                            Descripción
                        </th>

                        <th class="text-center px-6 py-4">
                            Usuarios
                        </th>

                        <th class="text-left px-6 py-4">
                            Estado
                        </th>
            
                        <th class="text-right px-6 py-4">
                            Acciones
                        </th>
                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($roles as $role)

                        <tr class="hover:bg-slate-50">

                            {{-- ROL --}}
                            <td class="px-6 py-4">

                                <p class="font-semibold
                                          text-slate-900">
                                    {{ $role->nombre }}
                                </p>

                            </td>

                            {{-- DESCRIPCIÓN --}}
                            <td class="px-6 py-4
                                       text-slate-600">

                                {{ $role->descripcion
                                    ?: 'Sin descripción' }}

                            </td>


                            {{-- USUARIOS --}}
                            <td class="px-6 py-4 text-center">

                                <span class="inline-flex
                                             bg-blue-50
                                             text-blue-700
                                             px-3 py-1
                                             rounded-full
                                             text-xs
                                             font-semibold">

                                    {{ $role->users_count }}

                                </span>

                            </td>


                            {{-- ESTADO --}}
                            <td class="px-6 py-4">

                                @if($role->estado)

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
                    
                            {{-- ACCIONES --}}
                            <td class="px-6 py-4">

                                <div class="flex
                                            justify-end
                                            items-center
                                            gap-4">

                                    {{-- EDITAR --}}
                                    <a
                                        href="{{ route(
                                            'administracion.roles.edit',
                                            $role
                                        ) }}"
                                        class="text-blue-700
                                            font-semibold
                                            hover:underline"
                                    >
                                        Editar
                                    </a>


                                    {{-- ADMINISTRADOR SIEMPRE PROTEGIDO --}}
                                    @if($role->nombre === 'Administrador')

                                        <span class="text-xs
                                                    text-slate-400
                                                    font-semibold">
                                            Protegido
                                        </span>


                                    {{-- ROL ACTIVO CON USUARIOS ASIGNADOS --}}
                                    @elseif($role->estado && $role->users_count > 0)

                                        <span
                                            class="text-xs
                                                text-amber-600
                                                font-semibold"
                                            title="Tiene usuarios asignados"
                                        >
                                            En uso
                                        </span>


                                    {{-- SE PUEDE ACTIVAR / DESACTIVAR --}}
                                    @else

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'administracion.roles.estado',
                                                $role
                                            ) }}"
                                        >

                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                onclick="return confirm(
                                                    '{{ $role->estado
                                                        ? '¿Desea desactivar este rol?'
                                                        : '¿Desea activar este rol?'
                                                    }}'
                                                )"
                                                class="
                                                    font-semibold
                                                    hover:underline

                                                    {{ $role->estado
                                                        ? 'text-red-600'
                                                        : 'text-emerald-600'
                                                    }}
                                                "
                                            >

                                                {{ $role->estado
                                                    ? 'Desactivar'
                                                    : 'Activar'
                                                }}

                                            </button>

                                        </form>

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
                                No se encontraron roles.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($roles->hasPages())

            <div class="px-6 py-4
                        border-t border-slate-200">

                {{ $roles->links() }}

            </div>

        @endif

    </div>

</div>  

@endsection