@extends('layouts.app')

@section('title', 'Pacientes | Laboratorio Dental')

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
                Pacientes
            </h1>

            <p class="text-slate-500 mt-1">
                Gestión de pacientes asociados a los odontólogos.
            </p>

        </div>


        <a
            href="{{ route('administracion.pacientes.create') }}"
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
            + Nuevo Paciente
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
                Total pacientes
            </p>

            <p class="text-3xl
                      font-bold
                      text-blue-700
                      mt-2">

                {{ $totalPacientes }}

            </p>

        </div>


        {{-- ACTIVOS --}}
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
                Activos
            </p>

            <p class="text-3xl
                      font-bold
                      text-emerald-600
                      mt-2">

                {{ $pacientesActivos }}

            </p>

        </div>


        {{-- INACTIVOS --}}
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
                Inactivos
            </p>

            <p class="text-3xl
                      font-bold
                      text-red-600
                      mt-2">

                {{ $pacientesInactivos }}

            </p>

        </div>


        {{-- CON ÓRDENES --}}
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
                Con órdenes
            </p>

            <p class="text-3xl
                      font-bold
                      text-violet-600
                      mt-2">

                {{ $pacientesConOrdenes }}

            </p>

        </div>

    </div>


    {{-- ===================================================== --}}
    {{-- FILTROS --}}
    {{-- ===================================================== --}}

    <form
        method="GET"
        action="{{ route('administracion.pacientes.index') }}"
        class="bg-white
               border border-slate-200
               rounded-xl
               shadow-sm
               p-5
               mb-8"
    >

        <div class="grid
                    grid-cols-1
                    md:grid-cols-3
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
                    placeholder="Paciente, teléfono, doctor..."
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


            {{-- ODONTÓLOGO --}}
            <div>

                <label
                    for="odontologo_id"
                    class="block
                           text-xs
                           font-semibold
                           text-slate-600
                           mb-2"
                >
                    Odontólogo
                </label>

                <select
                    name="odontologo_id"
                    id="odontologo_id"
                    class="w-full
                           border border-slate-300
                           rounded-lg
                           px-4 py-2.5
                           bg-white"
                >

                    <option value="">
                        Todos
                    </option>

                    @foreach($odontologos as $odontologo)

                        <option
                            value="{{ $odontologo->id }}"
                            @selected(
                                request('odontologo_id')
                                == $odontologo->id
                            )
                        >
                            {{ $odontologo->codigo_cliente }}
                            -
                            {{ $odontologo->nombre }}
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


        <div class="flex
                    flex-col-reverse
                    sm:flex-row
                    sm:justify-end
                    gap-3
                    mt-5">

            <a
                href="{{ route(
                    'administracion.pacientes.index'
                ) }}"
                class="inline-flex
                       items-center
                       justify-center
                       px-5 py-2.5
                       border border-slate-300
                       rounded-lg
                       text-slate-600
                       hover:bg-slate-50"
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
                       font-semibold"
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

        <div class="px-6 py-5
                    border-b border-slate-200">

            <h2 class="font-bold text-slate-900">
                Pacientes registrados
            </h2>

            <p class="text-sm
                      text-slate-500
                      mt-1">
                Pacientes asociados a odontólogos y órdenes de trabajo.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full
                          min-w-[1050px]
                          text-sm">

                <thead class="bg-slate-50
                              text-xs
                              uppercase
                              text-slate-500">

                    <tr>

                        <th class="text-left px-5 py-4">
                            Paciente
                        </th>

                        <th class="text-left px-5 py-4">
                            Odontólogo
                        </th>

                        <th class="text-left px-5 py-4">
                            Clínica
                        </th>

                        <th class="text-left px-5 py-4">
                            Teléfono
                        </th>

                        <th class="text-center px-5 py-4">
                            Órdenes
                        </th>

                        <th class="text-center px-5 py-4">
                            Estado
                        </th>

                        <th class="text-right px-5 py-4">
                            Acciones
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($pacientes as $paciente)

                        <tr class="hover:bg-slate-50 transition">


                            {{-- PACIENTE --}}
                            <td class="px-5 py-4 align-top">

                                <p class="font-semibold
                                          text-slate-900">

                                    {{ $paciente->nombre }}

                                    {{ $paciente->apellido }}

                                </p>


                                @if($paciente->observaciones)

                                    <p
                                        class="text-xs
                                               text-slate-400
                                               mt-1
                                               max-w-[220px]"
                                        title="{{ $paciente->observaciones }}"
                                    >

                                        {{ \Illuminate\Support\Str::limit(
                                            $paciente->observaciones,
                                            55
                                        ) }}

                                    </p>

                                @else

                                    <p class="text-xs
                                              text-slate-400
                                              mt-1">
                                        Sin observaciones
                                    </p>

                                @endif

                            </td>


                            {{-- ODONTÓLOGO --}}
                            <td class="px-5 py-4 align-top">

                                <p class="font-semibold
                                          text-slate-700">

                                    {{ $paciente->odontologo?->nombre
                                        ?? 'Sin odontólogo' }}

                                </p>


                                <p class="text-xs
                                          text-blue-700
                                          mt-1
                                          font-medium">

                                    {{ $paciente->odontologo?->codigo_cliente
                                        ?? 'Sin código' }}

                                </p>

                            </td>


                            {{-- CLÍNICA --}}
                            <td class="px-5 py-4 align-top">

                                <p class="text-slate-700">

                                    {{ $paciente->odontologo?->clinica?->nombre
                                        ?? 'Sin clínica' }}

                                </p>


                                @if(
                                    $paciente->odontologo?->clinica?->municipio
                                    ||
                                    $paciente->odontologo?->clinica?->departamento
                                )

                                    <p class="text-xs
                                              text-slate-400
                                              mt-1">

                                        {{ $paciente->odontologo?->clinica?->municipio }}

                                        @if(
                                            $paciente->odontologo?->clinica?->municipio
                                            &&
                                            $paciente->odontologo?->clinica?->departamento
                                        )
                                            ,
                                        @endif

                                        {{ $paciente->odontologo?->clinica?->departamento }}

                                    </p>

                                @endif

                            </td>


                            {{-- TELÉFONO --}}
                            <td class="px-5 py-4 align-top">

                                {{ $paciente->telefono
                                    ?: 'Sin teléfono' }}

                            </td>


                            {{-- ÓRDENES --}}
                            <td class="px-5 py-4
                                       text-center
                                       align-top">

                                <span class="inline-flex
                                             items-center
                                             justify-center
                                             bg-violet-50
                                             text-violet-700
                                             px-3 py-1
                                             rounded-full
                                             text-xs
                                             font-semibold">

                                    {{ $paciente->ordenes_trabajo_count }}

                                </span>

                            </td>


                            {{-- ESTADO --}}
                            <td class="px-5 py-4
                                       text-center
                                       align-top">

                                @if($paciente->estado)

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
                            <td class="px-5 py-4
                                       text-right
                                       align-top">

                                <div class="flex
                                            items-center
                                            justify-end
                                            gap-3
                                            whitespace-nowrap">


                                    <a
                                        href="{{ route(
                                            'administracion.pacientes.edit',
                                            $paciente
                                        ) }}"
                                        class="text-blue-700
                                               font-semibold
                                               hover:text-blue-900
                                               hover:underline"
                                    >
                                        Editar
                                    </a>


                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'administracion.pacientes.estado',
                                            $paciente
                                        ) }}"
                                    >

                                        @csrf
                                        @method('PATCH')


                                        <button
                                            type="submit"
                                            onclick="return confirm(
                                                '{{ $paciente->estado
                                                    ? '¿Está seguro de desactivar este paciente?'
                                                    : '¿Está seguro de activar este paciente?'
                                                }}'
                                            )"
                                            class="font-semibold
                                                   hover:underline
                                                   {{ $paciente->estado
                                                       ? 'text-red-600 hover:text-red-800'
                                                       : 'text-emerald-600 hover:text-emerald-800'
                                                   }}"
                                        >

                                            {{ $paciente->estado
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

                                No se encontraron pacientes.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($pacientes->hasPages())

            <div class="px-6 py-4
                        border-t border-slate-200">

                {{ $pacientes->links() }}

            </div>

        @endif

    </div>

</div>

@endsection