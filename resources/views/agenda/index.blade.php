@extends('layouts.app')

@section('title', 'Agenda de Producción')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- =========================
         ENCABEZADO
    ========================== --}}
    <div class="mb-8">

        <p class="text-sm font-semibold text-teal-700">
            PRODUCCIÓN
        </p>

        <h1 class="text-3xl font-bold text-slate-900 mt-1">
            Agenda de Producción
        </h1>

        <p class="text-slate-500 mt-1">
            Organización diaria de trabajos programados.
        </p>

    </div>


    {{-- =========================
         FILTROS
    ========================== --}}
    <form
        method="GET"
        action="{{ route('agenda.index') }}"
        class="bg-white border border-slate-200 rounded-xl
               p-5 mb-8 shadow-sm"
    >

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-4">

            {{-- FECHA --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Fecha
                </label>

                <input
                    type="date"
                    name="fecha"
                    value="{{ $fecha }}"
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-2.5"
                >

            </div>


            {{-- ODONTÓLOGO --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Odontólogo
                </label>

                <select
                    name="odontologo"
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-2.5 bg-white"
                >

                    <option value="">
                        Todos
                    </option>

                    @foreach($odontologos as $odontologo)

                        <option
                            value="{{ $odontologo->id }}"
                            @selected(request('odontologo') == $odontologo->id)
                        >
                            {{ $odontologo->nombre }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- PACIENTE --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Paciente
                </label>

                <select
                    name="paciente"
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-2.5 bg-white"
                >

                    <option value="">
                        Todos
                    </option>

                    @foreach($pacientes as $paciente)

                        <option
                            value="{{ $paciente->id }}"
                            @selected(request('paciente') == $paciente->id)
                        >
                            {{ $paciente->nombre }}
                            {{ $paciente->apellido }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- ESTADO --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Estado
                </label>

                <select
                    name="estado"
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-2.5 bg-white"
                >

                    <option value="">
                        Todos
                    </option>

                    @foreach($estados as $estado)

                        @if($estado->nombre !== 'Cancelado')

                            <option
                                value="{{ $estado->id }}"
                                @selected(request('estado') == $estado->id)
                            >
                                {{ $estado->nombre }}
                            </option>

                        @endif

                    @endforeach

                </select>

            </div>


            {{-- CATEGORÍA --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Categoría
                </label>

                <select
                    name="categoria"
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-2.5 bg-white"
                >

                    <option value="">
                        Todas
                    </option>

                    <option
                        value="removible"
                        @selected(request('categoria') === 'removible')
                    >
                        Removible
                    </option>

                    <option
                        value="fija"
                        @selected(request('categoria') === 'fija')
                    >
                        Fija
                    </option>

                    <option
                        value="ortodoncia"
                        @selected(request('categoria') === 'ortodoncia')
                    >
                        Ortodoncia
                    </option>

                </select>

            </div>


            {{-- PRIORIDAD --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Prioridad
                </label>

                <select
                    name="prioridad"
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-2.5 bg-white"
                >

                    <option value="">
                        Todas
                    </option>

                    <option
                        value="Normal"
                        @selected(request('prioridad') === 'Normal')
                    >
                        Normal
                    </option>

                    <option
                        value="Urgente"
                        @selected(request('prioridad') === 'Urgente')
                    >
                        Urgente
                    </option>

                </select>

            </div>

        </div>


        {{-- BOTONES --}}
        <div class="flex justify-end gap-3 mt-5">

            <a
                href="{{ route('agenda.index') }}"
                class="px-5 py-2.5 border border-slate-300
                       rounded-lg text-slate-600 font-semibold
                       hover:bg-slate-50"
            >
                Limpiar
            </a>

            <button
                type="submit"
                class="px-6 py-2.5 bg-teal-700
                       hover:bg-teal-800 text-white
                       rounded-lg font-semibold"
            >
                Filtrar
            </button>

        </div>

    </form>


    {{-- =========================
         FECHA Y NAVEGACIÓN
    ========================== --}}
    <div class="mb-6 bg-white border border-slate-200
                rounded-xl px-6 py-4 shadow-sm
                flex flex-col md:flex-row
                md:items-center md:justify-between gap-4">

        {{-- FECHA ACTUAL --}}
        <div>

            <p class="text-xs font-semibold text-slate-400 uppercase mb-1">
                Agenda del día
            </p>

            <h2 class="font-bold text-slate-900 capitalize">

                {{ \Carbon\Carbon::parse($fecha)
                    ->locale('es')
                    ->translatedFormat('l d \d\e F \d\e Y') }}

            </h2>

        </div>


        {{-- NAVEGACIÓN --}}
        <div class="flex items-center gap-2">

            {{-- ANTERIOR --}}
            <a
                href="{{ route('agenda.index', [
                    'fecha' => \Carbon\Carbon::parse($fecha)
                        ->subDay()
                        ->toDateString()
                ]) }}"
                class="px-4 py-2 border border-slate-300
                       rounded-lg text-sm font-semibold
                       text-slate-600 hover:bg-slate-50 transition"
            >
                ← Anterior
            </a>


            {{-- HOY --}}
            <a
                href="{{ route('agenda.index', [
                    'fecha' => now()->toDateString()
                ]) }}"
                class="px-4 py-2 bg-teal-50 text-teal-700
                       rounded-lg text-sm font-semibold
                       hover:bg-teal-100 transition"
            >
                Hoy
            </a>


            {{-- SIGUIENTE --}}
            <a
                href="{{ route('agenda.index', [
                    'fecha' => \Carbon\Carbon::parse($fecha)
                        ->addDay()
                        ->toDateString()
                ]) }}"
                class="px-4 py-2 border border-slate-300
                       rounded-lg text-sm font-semibold
                       text-slate-600 hover:bg-slate-50 transition"
            >
                Siguiente →
            </a>

        </div>

    </div>
    
    {{-- ===========================
         PRÓTESIS REMOVIBLES
    ============================ --}}
    <section class="mb-8">

        <div class="bg-teal-800 text-white px-6 py-4 rounded-t-xl">
            <h2 class="font-bold text-lg">
                PRÓTESIS REMOVIBLES
            </h2>
        </div>

        @forelse($removibles as $nombreEtapa => $grupo)

            <div class="bg-white border-x border-b border-slate-200">

                <div class="bg-slate-100 px-6 py-3">
                    <h3 class="font-bold text-slate-700 uppercase text-sm">
                        {{ $nombreEtapa }}
                    </h3>
                </div>

                @include('agenda.partials.tabla', [
                    'ordenesGrupo' => $grupo
                ])

            </div>

        @empty

            <div class="bg-white border border-slate-200 px-6 py-8 text-center text-slate-400">
                Sin trabajos removibles programados.
            </div>

        @endforelse

    </section>

    {{-- ===========================
       PRÓTESIS FIJAS
    ============================ --}}
        <section class="mb-8">

            <div class="bg-blue-900 text-white px-6 py-4 rounded-t-xl">
                <h2 class="font-bold text-lg">
                    PRÓTESIS FIJAS
                </h2>
            </div>

            @forelse($fijas as $nombreEtapa => $grupo)

                <div class="bg-white border-x border-b border-slate-200">

                    <div class="bg-slate-100 px-6 py-3">
                        <h3 class="font-bold text-slate-700 uppercase text-sm">
                            {{ $nombreEtapa }}
                        </h3>
                    </div>

                    @include('agenda.partials.tabla', [
                        'ordenesGrupo' => $grupo
                    ])

                </div>

            @empty

                <div class="bg-white border border-slate-200 px-6 py-8 text-center text-slate-400">
                    Sin trabajos fijos programados.
                </div>

            @endforelse

        </section>

        <section class="mb-8">

        <div class="bg-slate-800 text-white px-6 py-4 rounded-t-xl">
            <h2 class="font-bold text-lg">
                ORTODONCIA
            </h2>
        </div>

        @forelse($ortodoncia as $nombreEtapa => $grupo)

            <div class="bg-white border-x border-b border-slate-200">

                <div class="bg-slate-100 px-6 py-3">
                    <h3 class="font-bold text-slate-700 uppercase text-sm">
                        {{ $nombreEtapa }}
                    </h3>
                </div>

                @include('agenda.partials.tabla', [
                    'ordenesGrupo' => $grupo
                ])

            </div>

        @empty

            <div class="bg-white border border-slate-200 px-6 py-8 text-center text-slate-400">
                Sin trabajos de ortodoncia programados.
            </div>

        @endforelse

    </section>
@endsection