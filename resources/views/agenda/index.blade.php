@extends('layouts.app')

@section('title', 'Agenda de Producción')

@section('content')

<div class="w-full min-w-0">

    {{-- =========================================================
         ENCABEZADO
    ========================================================== --}}
    <div class="mb-8">

        <p class="text-sm font-semibold text-[#315875]">
            PRODUCCIÓN
        </p>

        <h1 class="text-3xl font-bold text-slate-900 mt-1">
            Agenda de Producción
        </h1>

        <p class="text-slate-500 mt-1">
            Organización diaria de trabajos programados.
        </p>

    </div>


    {{-- =========================================================
         FILTROS
    ========================================================== --}}
    <form
        method="GET"
        action="{{ route('agenda.index') }}"
        class="bg-white border border-slate-200 rounded-xl p-5 mb-8 shadow-sm"
    >

        @if(!($esTecnico ?? false))

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">

                {{-- FECHA --}}
                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Fecha
                    </label>

                    <input
                        type="date"
                        name="fecha"
                        value="{{ $fecha }}"
                        class="w-full min-w-0 border border-slate-200 rounded-lg px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#315875] focus:border-[#315875]"
                    >

                </div>


                {{-- ODONTÓLOGO --}}
                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Odontólogo
                    </label>

                    <select
                        name="odontologo"
                        class="w-full min-w-0 border border-slate-200 rounded-lg px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#315875] focus:border-[#315875] bg-white"
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
                        class="w-full min-w-0 border border-slate-200 rounded-lg px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#315875] focus:border-[#315875] bg-white"
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
                        class="w-full min-w-0 border border-slate-200 rounded-lg px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#315875] focus:border-[#315875] bg-white"
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


                {{-- ÁREA DE TRABAJO --}}
                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Área
                    </label>

                    <select
                        name="area"
                        class="w-full min-w-0 border border-slate-200 rounded-lg px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#315875] focus:border-[#315875] bg-white"
                    >

                        <option value="">
                            Todas
                        </option>

                        <option
                            value="removible"
                            @selected(request('area') === 'removible')
                        >
                            Prótesis Removibles
                        </option>

                        <option
                            value="fija"
                            @selected(request('area') === 'fija')
                        >
                            Prótesis Fijas
                        </option>

                        <option
                            value="cromo_cobalto"
                            @selected(request('area') === 'cromo_cobalto')
                        >
                            Cromo Cobalto
                        </option>

                        <option
                            value="ortodoncia"
                            @selected(request('area') === 'ortodoncia')
                        >
                            Aparatos de Ortodoncia
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
                        class="w-full min-w-0 border border-slate-200 rounded-lg px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#315875] focus:border-[#315875] bg-white"
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

        @else

            {{-- FILTRO PARA TÉCNICO --}}
            <div class="grid grid-cols-1 gap-4 max-w-md">

                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Prioridad
                    </label>

                    <select
                        name="prioridad"
                        class="w-full min-w-0 border border-slate-200 rounded-lg px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#315875] focus:border-[#315875] bg-white"
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

        @endif


    {{-- BOTONES --}}

<div class="flex flex-col sm:flex-row sm:justify-end gap-3 mt-5">

    <a
        href="{{ route('agenda.index') }}"
        class="inline-flex items-center justify-center px-5 py-2.5 border border-slate-200 rounded-lg text-[#315875] text-sm font-semibold hover:bg-[#e7eef8] transition-colors"
    >
        Limpiar
    </a>

    <button
        type="submit"
        class="inline-flex items-center justify-center px-6 py-2.5 bg-[#315875] hover:bg-[#182d47] text-white rounded-lg text-sm font-semibold transition-colors"
    >
        Filtrar
    </button>

</div>

    </form>


    {{-- =========================================================
         FECHA Y NAVEGACIÓN
    ========================================================== --}}
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


        {{-- NAVEGACIÓN SOLO ADMIN / RECEPCIÓN --}}
        @if(!($esTecnico ?? false))

            <div class="flex flex-wrap items-center gap-2">

                {{-- ANTERIOR --}}
                <a
                    href="{{ route(
                        'agenda.index',
                        array_merge(
                            request()->except('fecha'),
                            [
                                'fecha' => \Carbon\Carbon::parse($fecha)
                                    ->subDay()
                                    ->toDateString()
                            ]
                        )
                    ) }}"
                    class="px-4 py-2 border border-slate-300
                           rounded-lg text-sm font-semibold
                           text-slate-600 hover:bg-slate-50 transition"
                >
                    ← Anterior
                </a>


                {{-- HOY --}}
                <a
                    href="{{ route(
                        'agenda.index',
                        array_merge(
                            request()->except('fecha'),
                            [
                                'fecha' => \Carbon\Carbon::today(
                                    'America/Guatemala'
                                )->toDateString()
                            ]
                        )
                    ) }}"
                    class="px-4 py-2 bg-[#e7eef8] text-[#315875]
                           rounded-lg text-sm font-semibold
                           hover:bg-[#dce7f3] transition"
                >
                    Hoy
                </a>


                {{-- SIGUIENTE --}}
                <a
                    href="{{ route(
                        'agenda.index',
                        array_merge(
                            request()->except('fecha'),
                            [
                                'fecha' => \Carbon\Carbon::parse($fecha)
                                    ->addDay()
                                    ->toDateString()
                            ]
                        )
                    ) }}"
                    class="px-4 py-2 border border-slate-300
                           rounded-lg text-sm font-semibold
                           text-slate-600 hover:bg-slate-50 transition"
                >
                    Siguiente →
                </a>

            </div>

        @else

            <div class="px-4 py-2 bg-[#e7eef8] text-[#315875]
                        rounded-lg text-sm font-semibold">
                Mis trabajos asignados
            </div>

        @endif

    </div>


    {{-- =========================================================
         PRÓTESIS REMOVIBLES
    ========================================================== --}}
    <section class="mb-8">

        <div class="bg-[#315875] text-white px-6 py-4 rounded-t-xl">

            <div class="flex items-center justify-between">

                <h2 class="font-bold text-lg">
                    PRÓTESIS REMOVIBLES
                </h2>

                <span class="text-xs font-semibold bg-white/20 px-3 py-1 rounded-full">
                    PR
                </span>

            </div>

        </div>


        @forelse($removibles as $nombreEtapa => $grupo)

            <div class="bg-white border-x border-b border-slate-200">

                <div class="bg-[#eef3ff] px-4 sm:px-6 py-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                    <h3 class="font-bold text-slate-700 uppercase text-sm">
                        {{ $nombreEtapa }}
                    </h3>

                    <span class="text-xs font-semibold text-slate-500">
                        {{ $grupo->count() }}
                        {{ $grupo->count() === 1 ? 'trabajo' : 'trabajos' }}
                    </span>

                </div>

                @include('agenda.partials.tabla', [
                    'ordenesGrupo' => $grupo
                ])

            </div>

        @empty

            <div class="bg-white border border-slate-200
                        rounded-b-xl px-6 py-8
                        text-center text-slate-400">
                Sin trabajos removibles programados.
            </div>

        @endforelse

    </section>


    {{-- =========================================================
         PRÓTESIS FIJAS
    ========================================================== --}}
    <section class="mb-8">

        <div class="bg-[#315875] text-white px-6 py-4 rounded-t-xl">

            <div class="flex items-center justify-between">

                <h2 class="font-bold text-lg">
                    PRÓTESIS FIJAS
                </h2>

                <span class="text-xs font-semibold bg-white/20 px-3 py-1 rounded-full">
                    PF
                </span>

            </div>

        </div>


        @forelse($fijas as $nombreEtapa => $grupo)

            <div class="bg-white border-x border-b border-slate-200">

                <div class="bg-[#eef3ff] px-4 sm:px-6 py-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                    <h3 class="font-bold text-slate-700 uppercase text-sm">
                        {{ $nombreEtapa }}
                    </h3>

                    <span class="text-xs font-semibold text-slate-500">
                        {{ $grupo->count() }}
                        {{ $grupo->count() === 1 ? 'trabajo' : 'trabajos' }}
                    </span>

                </div>

                @include('agenda.partials.tabla', [
                    'ordenesGrupo' => $grupo
                ])

            </div>

        @empty

            <div class="bg-white border border-slate-200
                        rounded-b-xl px-6 py-8
                        text-center text-slate-400">
                Sin trabajos fijos programados.
            </div>

        @endforelse

    </section>


    {{-- =========================================================
         CROMO COBALTO
    ========================================================== --}}
    <section class="mb-8">

        <div class="bg-[#315875] text-white px-6 py-4 rounded-t-xl">

            <div class="flex items-center justify-between">

                <h2 class="font-bold text-lg">
                    CROMO COBALTO
                </h2>

                <span class="text-xs font-semibold bg-white/20 px-3 py-1 rounded-full">
                    CC
                </span>

            </div>

        </div>


        @forelse($cromoCobalto as $nombreEtapa => $grupo)

            <div class="bg-white border-x border-b border-slate-200">

                <div class="bg-[#eef3ff] px-4 sm:px-6 py-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                    <h3 class="font-bold text-slate-700 uppercase text-sm">
                        {{ $nombreEtapa }}
                    </h3>

                    <span class="text-xs font-semibold text-slate-500">
                        {{ $grupo->count() }}
                        {{ $grupo->count() === 1 ? 'trabajo' : 'trabajos' }}
                    </span>

                </div>

                @include('agenda.partials.tabla', [
                    'ordenesGrupo' => $grupo
                ])

            </div>

        @empty

            <div class="bg-white border border-slate-200
                        rounded-b-xl px-6 py-8
                        text-center text-slate-400">
                Sin trabajos de Cromo Cobalto programados.
            </div>

        @endforelse

    </section>

    {{-- =========================================================
         APARATOS DE ORTODONCIA
    ========================================================== --}}
    <section class="mb-8">

        <div class="bg-[#315875] text-white px-6 py-4 rounded-t-xl">

            <div class="flex items-center justify-between">

                <h2 class="font-bold text-lg">
                    APARATOS DE ORTODONCIA
                </h2>

                <span class="text-xs font-semibold bg-white/20 px-3 py-1 rounded-full">
                    AO
                </span>

            </div>

        </div>


        @forelse($ortodoncia as $nombreEtapa => $grupo)

            <div class="bg-white border-x border-b border-slate-200">

                <div class="bg-[#eef3ff] px-4 sm:px-6 py-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                    <h3 class="font-bold text-slate-700 uppercase text-sm">
                        {{ $nombreEtapa }}
                    </h3>

                    <span class="text-xs font-semibold text-slate-500">
                        {{ $grupo->count() }}
                        {{ $grupo->count() === 1 ? 'trabajo' : 'trabajos' }}
                    </span>

                </div>

                @include('agenda.partials.tabla', [
                    'ordenesGrupo' => $grupo
                ])

            </div>

        @empty

            <div class="bg-white border border-slate-200
                        rounded-b-xl px-6 py-8
                        text-center text-slate-400">
                Sin trabajos de ortodoncia programados.
            </div>

        @endforelse

    </section>

</div>

@endsection