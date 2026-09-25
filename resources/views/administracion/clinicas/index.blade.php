@extends('layouts.app')

@section('title', 'Clínicas | Laboratorio Dental')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- ===================================================== --}}
    {{-- ENCABEZADO --}}
    {{-- ===================================================== --}}

    <div class="flex flex-col
                md:flex-row
                md:items-center
                md:justify-between
                gap-4
                mb-8">

        <div>

            <p class="text-sm
                      font-semibold
                      text-blue-800
                      uppercase">
                Administración
            </p>

            <h1 class="text-3xl
                       font-bold
                       text-slate-900">
                Clínicas
            </h1>

            <p class="text-slate-500 mt-1">
                Gestión de clínicas asociadas al laboratorio.
            </p>

        </div>


        <a
            href="{{ route('administracion.clinicas.create') }}"
            class="inline-flex
                   items-center
                   justify-center
                   bg-blue-800
                   hover:bg-blue-900
                   text-white
                   px-5 py-2.5
                   rounded-lg
                   font-semibold
                   shadow-sm
                   transition"
        >
            + Nueva Clínica
        </a>

    </div>


    {{-- ===================================================== --}}
    {{-- MENSAJES --}}
    {{-- ===================================================== --}}

    @if(session('success'))

        <div class="mb-6
                    bg-emerald-50
                    border border-emerald-200
                    text-emerald-700
                    rounded-lg
                    px-4 py-3">

            {{ session('success') }}

        </div>

    @endif


    @if(session('error'))

        <div class="mb-6
                    bg-red-50
                    border border-red-200
                    text-red-700
                    rounded-lg
                    px-4 py-3">

            {{ session('error') }}

        </div>

    @endif


    {{-- ===================================================== --}}
    {{-- INDICADORES --}}
    {{-- ===================================================== --}}

    <div class="grid
                grid-cols-1
                sm:grid-cols-2
                xl:grid-cols-4
                gap-5
                mb-8">


        {{-- TOTAL --}}
        <div class="bg-white
                    border border-slate-200
                    border-l-4 border-l-blue-600
                    rounded-xl
                    shadow-sm
                    p-5">

            <p class="text-xs
                      uppercase
                      font-semibold
                      text-slate-500">
                Total clínicas
            </p>

            <p class="text-3xl
                      font-bold
                      text-blue-700
                      mt-2">
                {{ $totalClinicas }}
            </p>

        </div>


        {{-- ACTIVAS --}}
        <div class="bg-white
                    border border-slate-200
                    border-l-4 border-l-emerald-500
                    rounded-xl
                    shadow-sm
                    p-5">

            <p class="text-xs
                      uppercase
                      font-semibold
                      text-slate-500">
                Activas
            </p>

            <p class="text-3xl
                      font-bold
                      text-emerald-600
                      mt-2">
                {{ $clinicasActivas }}
            </p>

        </div>


        {{-- INACTIVAS --}}
        <div class="bg-white
                    border border-slate-200
                    border-l-4 border-l-red-500
                    rounded-xl
                    shadow-sm
                    p-5">

            <p class="text-xs
                      uppercase
                      font-semibold
                      text-slate-500">
                Inactivas
            </p>

            <p class="text-3xl
                      font-bold
                      text-red-600
                      mt-2">
                {{ $clinicasInactivas }}
            </p>

        </div>


        {{-- CON ODONTÓLOGOS --}}
        <div class="bg-white
                    border border-slate-200
                    border-l-4 border-l-violet-500
                    rounded-xl
                    shadow-sm
                    p-5">

            <p class="text-xs
                      uppercase
                      font-semibold
                      text-slate-500">
                Con odontólogos
            </p>

            <p class="text-3xl
                      font-bold
                      text-violet-600
                      mt-2">
                {{ $clinicasConOdontologos }}
            </p>

        </div>

    </div>


    {{-- ===================================================== --}}
    {{-- FILTROS --}}
    {{-- ===================================================== --}}

    <form
        method="GET"
        action="{{ route('administracion.clinicas.index') }}"
        class="bg-white
               border border-slate-200
               rounded-xl
               shadow-sm
               p-5
               mb-8"
    >

        <div class="grid
                    grid-cols-1
                    md:grid-cols-2
                    xl:grid-cols-4
                    gap-4">


            {{-- BUSCAR --}}
            <div>

                <label
                    for="buscar"
                    class="block
                           text-xs
                           font-semibold
                           text-slate-600
                           mb-2"
                >
                    Buscar
                </label>

                <input
                    type="text"
                    name="buscar"
                    id="buscar"
                    value="{{ request('buscar') }}"
                    placeholder="Clínica, NIT, asistente..."
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
                           text-xs
                           font-semibold
                           text-slate-600
                           mb-2"
                >
                    Departamento
                </label>

                <select
                    name="departamento"
                    id="departamento"
                    class="w-full
                           border border-slate-300
                           rounded-lg
                           px-4 py-2.5
                           bg-white"
                >

                    <option value="">
                        Todos
                    </option>

                    @foreach($departamentos as $departamento)

                        <option
                            value="{{ $departamento }}"
                            @selected(
                                request('departamento') === $departamento
                            )
                        >
                            {{ $departamento }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- MUNICIPIO --}}
            <div>

                <label
                    for="municipio"
                    class="block
                           text-xs
                           font-semibold
                           text-slate-600
                           mb-2"
                >
                    Municipio
                </label>

                <select
                    name="municipio"
                    id="municipio"
                    class="w-full
                           border border-slate-300
                           rounded-lg
                           px-4 py-2.5
                           bg-white"
                >

                    <option value="">
                        Todos
                    </option>

                    @foreach($municipios as $municipio)

                        <option
                            value="{{ $municipio }}"
                            @selected(
                                request('municipio') === $municipio
                            )
                        >
                            {{ $municipio }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- ESTADO --}}
            <div>

                <label
                    for="estado"
                    class="block
                           text-xs
                           font-semibold
                           text-slate-600
                           mb-2"
                >
                    Estado
                </label>

                <select
                    name="estado"
                    id="estado"
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
                        Activas
                    </option>

                    <option
                        value="inactivo"
                        @selected(
                            request('estado') === 'inactivo'
                        )
                    >
                        Inactivas
                    </option>

                </select>

            </div>

        </div>


        {{-- BOTONES FILTRO --}}
        <div class="flex
                    flex-col-reverse
                    sm:flex-row
                    sm:justify-end
                    gap-3
                    mt-5">

            <a
                href="{{ route('administracion.clinicas.index') }}"
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
                Limpiar
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
                       transition"
            >
                Filtrar
            </button>

        </div>

    </form>


    {{-- ===================================================== --}}
    {{-- TABLA --}}
    {{-- ===================================================== --}}

    <div class="bg-white
                border border-slate-200
                rounded-xl
                shadow-sm
                overflow-hidden">

        {{-- CABECERA --}}
        <div class="px-6 py-5
                    border-b border-slate-200">

            <h2 class="font-bold text-slate-900">
                Clínicas registradas
            </h2>

            <p class="text-sm
                      text-slate-500
                      mt-1">
                Información general, ubicación, contacto y odontólogos asociados.
            </p>

        </div>


        {{-- TABLA CON SCROLL HORIZONTAL --}}
        <div class="overflow-x-auto">

            <table class="w-full
                          min-w-[1150px]
                          text-sm">

                {{-- ========================================= --}}
                {{-- ENCABEZADOS --}}
                {{-- ========================================= --}}

                <thead class="bg-slate-50
                              text-xs
                              uppercase
                              text-slate-500">

                    <tr>

                        <th class="text-left
                                   px-5 py-4
                                   whitespace-nowrap">
                            Clínica
                        </th>

                        <th class="text-left
                                   px-5 py-4
                                   whitespace-nowrap">
                            Ubicación
                        </th>

                        <th class="text-left
                                   px-5 py-4
                                   whitespace-nowrap">
                            Contacto
                        </th>

                        <th class="text-left
                                   px-5 py-4
                                   whitespace-nowrap">
                            NIT / Asistente
                        </th>

                        <th class="text-center
                                   px-5 py-4
                                   whitespace-nowrap">
                            Odontólogos
                        </th>

                        <th class="text-center
                                   px-5 py-4
                                   whitespace-nowrap">
                            Estado
                        </th>

                        <th class="text-right
                                   px-5 py-4
                                   whitespace-nowrap">
                            Acciones
                        </th>

                    </tr>

                </thead>


                {{-- ========================================= --}}
                {{-- REGISTROS --}}
                {{-- ========================================= --}}

                <tbody class="divide-y divide-slate-100">

                    @forelse($clinicas as $clinica)

                        <tr class="hover:bg-slate-50
                                   transition">


                            {{-- ============================= --}}
                            {{-- CLÍNICA --}}
                            {{-- ============================= --}}

                            <td class="px-5 py-4
                                       align-top">

                                <p class="font-semibold
                                          text-slate-900">
                                    {{ $clinica->nombre }}
                                </p>


                                <p class="text-xs
                                          text-slate-400
                                          mt-1">
                                    {{ $clinica->correo
                                        ?: 'Sin correo registrado' }}
                                </p>

                            </td>


                            {{-- ============================= --}}
                            {{-- UBICACIÓN --}}
                            {{-- ============================= --}}

                            <td class="px-5 py-4
                                       align-top">

                                <p class="font-semibold
                                          text-slate-700">

                                    {{ $clinica->municipio
                                        ?: 'Sin municipio' }}

                                </p>


                                <p class="text-xs
                                          text-slate-500
                                          mt-1">

                                    {{ $clinica->departamento
                                        ?: 'Sin departamento' }}

                                </p>


                                @if($clinica->direccion)

                                    <p
                                        class="text-xs
                                               text-slate-400
                                               mt-1
                                               max-w-[220px]"
                                        title="{{ $clinica->direccion }}"
                                    >
                                        {{ \Illuminate\Support\Str::limit(
                                            $clinica->direccion,
                                            55
                                        ) }}
                                    </p>

                                @else

                                    <p class="text-xs
                                              text-slate-400
                                              mt-1">
                                        Sin dirección
                                    </p>

                                @endif

                            </td>


                            {{-- ============================= --}}
                            {{-- CONTACTO --}}
                            {{-- ============================= --}}

                            <td class="px-5 py-4
                                       align-top">

                                <p class="font-medium
                                          text-slate-700">

                                    {{ $clinica->telefono
                                        ?: 'Sin teléfono' }}

                                </p>


                                @if($clinica->correo)

                                    <p class="text-xs
                                              text-slate-400
                                              mt-1
                                              max-w-[190px]
                                              break-all">

                                        {{ $clinica->correo }}

                                    </p>

                                @endif

                            </td>


                            {{-- ============================= --}}
                            {{-- NIT / ASISTENTE --}}
                            {{-- ============================= --}}

                            <td class="px-5 py-4
                                       align-top">

                                <p class="font-medium
                                          text-slate-700">

                                    <span class="text-slate-400">
                                        NIT:
                                    </span>

                                    {{ $clinica->nit
                                        ?: 'No registrado' }}

                                </p>


                                <p class="text-xs
                                          text-slate-500
                                          mt-2">

                                    <span class="font-medium">
                                        Asistente:
                                    </span>

                                    {{ $clinica->asistente_secretaria
                                        ?: 'No registrada' }}

                                </p>

                            </td>


                            {{-- ============================= --}}
                            {{-- ODONTÓLOGOS --}}
                            {{-- ============================= --}}

                            <td class="px-5 py-4
                                       text-center
                                       align-top">

                                <span class="inline-flex
                                             items-center
                                             justify-center
                                             min-w-8
                                             bg-violet-50
                                             text-violet-700
                                             px-3 py-1
                                             rounded-full
                                             text-xs
                                             font-semibold">

                                    {{ $clinica->odontologos_count }}

                                </span>

                            </td>


                            {{-- ============================= --}}
                            {{-- ESTADO --}}
                            {{-- ============================= --}}

                            <td class="px-5 py-4
                                       text-center
                                       align-top">

                                @if($clinica->estado)

                                    <span class="inline-flex
                                                 items-center
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
                                                 items-center
                                                 bg-red-100
                                                 text-red-700
                                                 px-3 py-1
                                                 rounded-full
                                                 text-xs
                                                 font-semibold">
                                        Inactiva
                                    </span>

                                @endif

                            </td>


                            {{-- ============================= --}}
                            {{-- ACCIONES --}}
                            {{-- ============================= --}}

                            <td class="px-5 py-4
                                       text-right
                                       align-top">

                                <div class="flex
                                            items-center
                                            justify-end
                                            gap-3
                                            whitespace-nowrap">

                                    {{-- EDITAR --}}
                                    <a
                                        href="{{ route(
                                            'administracion.clinicas.edit',
                                            $clinica
                                        ) }}"
                                        class="text-blue-700
                                               font-semibold
                                               hover:text-blue-900
                                               hover:underline"
                                    >
                                        Editar
                                    </a>


                                    {{-- ESTADO --}}
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'administracion.clinicas.estado',
                                            $clinica
                                        ) }}"
                                    >

                                        @csrf
                                        @method('PATCH')


                                        <button
                                            type="submit"
                                            onclick="return confirm(
                                                '{{ $clinica->estado
                                                    ? '¿Está seguro de desactivar esta clínica?'
                                                    : '¿Está seguro de activar esta clínica?'
                                                }}'
                                            )"
                                            class="font-semibold
                                                   hover:underline
                                                   {{ $clinica->estado
                                                       ? 'text-red-600 hover:text-red-800'
                                                       : 'text-emerald-600 hover:text-emerald-800'
                                                   }}"
                                        >

                                            {{ $clinica->estado
                                                ? 'Desactivar'
                                                : 'Activar'
                                            }}

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="px-6 py-12
                                       text-center
                                       text-slate-400"
                            >

                                <div class="flex
                                            flex-col
                                            items-center
                                            gap-2">

                                    <span class="text-3xl">
                                        🏥
                                    </span>

                                    <p class="font-medium
                                              text-slate-500">
                                        No se encontraron clínicas.
                                    </p>

                                    <p class="text-xs">
                                        Intente modificar los filtros
                                        de búsqueda.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- ================================================= --}}
        {{-- PAGINACIÓN --}}
        {{-- ================================================= --}}

        @if($clinicas->hasPages())

            <div class="px-6 py-4
                        border-t border-slate-200">

                {{ $clinicas->links() }}

            </div>

        @endif

    </div>

</div>

@endsection