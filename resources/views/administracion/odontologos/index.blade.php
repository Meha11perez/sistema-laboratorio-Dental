@extends('layouts.app')

@section('title', 'Odontólogos | Laboratorio Dental')

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
                Odontólogos
            </h1>

            <p class="text-slate-500 mt-1">
                Gestión de odontólogos asociados al laboratorio.
            </p>

        </div>


        <a href="{{ route('administracion.odontologos.create') }}"
            class="inline-flex items-center justify-center
                bg-blue-800
                hover:bg-blue-900
                text-white px-5 py-2.5 rounded-lg font-semibold shadow-sm transition">
            + Nuevo Odontólogo
        </a>

    </div>

    {{-- MENSAJES --}}

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
                md:grid-cols-2
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
                Total odontólogos
            </p>

            <p class="text-3xl
                      font-bold
                      text-blue-700
                      mt-2">
                {{ $totalOdontologos }}
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
                {{ $odontologosActivos }}
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
                {{ $odontologosInactivos }}
            </p>

        </div>


        {{-- CON CLÍNICA --}}
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
                Con clínica
            </p>

            <p class="text-3xl
                      font-bold
                      text-violet-600
                      mt-2">
                {{ $odontologosConClinica }}
            </p>

        </div>

    </div>


    {{-- ===================================================== --}}
    {{-- FILTROS --}}
    {{-- ===================================================== --}}

    <form
        method="GET"
        action="{{ route('administracion.odontologos.index') }}"
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
                    placeholder="Nombre, correo, colegiado..."
                    class="w-full
                           border border-slate-300
                           rounded-lg
                           px-4 py-2.5"
                >

            </div>


            {{-- CLÍNICA --}}
            <div>

                <label
                    for="clinica_id"
                    class="block
                           text-xs
                           font-semibold
                           text-slate-600
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
                           bg-white"
                >

                    <option value="">
                        Todas
                    </option>

                    @foreach($clinicas as $clinica)

                        <option
                            value="{{ $clinica->id }}"
                            @selected(
                                request('clinica_id')
                                == $clinica->id
                            )
                        >
                            {{ $clinica->nombre }}
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
                    justify-end
                    gap-3
                    mt-5">

            <a
                href="{{ route(
                    'administracion.odontologos.index'
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
                Odontólogos registrados
            </h2>

            <p class="text-sm
                      text-slate-500
                      mt-1">
                Información de contacto y clínica asociada.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50
                              text-xs
                              uppercase
                              text-slate-500">

                    <tr>

                        <th class="text-left px-6 py-4">
                            Odontólogo
                        </th>

                        <th class="text-left px-6 py-4">
                            Clínica
                        </th>

                        <th class="text-left px-6 py-4">
                            Contacto
                        </th>

                        <th class="text-left px-6 py-4">
                            No. colegiado
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

                    @forelse($odontologos as $odontologo)

                        <tr class="hover:bg-slate-50">


                            {{-- ODONTÓLOGO --}}
                            <td class="px-6 py-4">

                                <p class="font-semibold
                                          text-slate-900">

                                    {{ $odontologo->nombre }}

                                </p>

                                <p class="text-xs
                                          text-slate-400
                                          mt-1">

                                    {{ $odontologo->correo
                                        ?: 'Sin correo' }}

                                </p>

                            </td>


                            {{-- CLÍNICA --}}
                            <td class="px-6 py-4
                                       text-slate-600">

                                {{ $odontologo->clinica?->nombre
                                    ?? 'Sin clínica' }}

                            </td>


                            {{-- CONTACTO --}}
                            <td class="px-6 py-4
                                       text-slate-600">

                                {{ $odontologo->telefono
                                    ?: '—' }}

                            </td>


                            {{-- COLEGIADO --}}
                            <td class="px-6 py-4
                                       text-slate-600">

                                {{ $odontologo->numero_colegiado
                                    ?: '—' }}

                            </td>


                            {{-- ESTADO --}}
                            <td class="px-6 py-4">

                                @if($odontologo->estado)

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
                                            items-center
                                            justify-end
                                            gap-4">

                                    {{-- EDITAR --}}
                                    <a
                                        href="{{ route(
                                            'administracion.odontologos.edit',
                                            $odontologo
                                        ) }}"
                                        class="text-blue-700
                                            font-semibold
                                            hover:underline"
                                    >
                                        Editar
                                    </a>


                                    {{-- ACTIVAR / DESACTIVAR --}}
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'administracion.odontologos.estado',
                                            $odontologo
                                        ) }}"
                                    >

                                        @csrf
                                        @method('PATCH')


                                        <button
                                            type="submit"
                                            onclick="return confirm(
                                                '{{ $odontologo->estado
                                                    ? '¿Desea desactivar este odontólogo?'
                                                    : '¿Desea activar este odontólogo?'
                                                }}'
                                            )"
                                            class="font-semibold
                                                hover:underline
                                                {{ $odontologo->estado
                                                        ? 'text-red-600'
                                                        : 'text-emerald-600'
                                                }}"
                                        >

                                            {{ $odontologo->estado
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
                                colspan="6"
                                class="px-6 py-12
                                       text-center
                                       text-slate-400"
                            >
                                No se encontraron odontólogos.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($odontologos->hasPages())

            <div class="px-6 py-4
                        border-t border-slate-200">

                {{ $odontologos->links() }}

            </div>

        @endif

    </div>

</div>

@endsection