@extends('layouts.app')

@section('title', 'Dashboard | Laboratorio Dental')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- =========================================================
         ENCABEZADO
    ========================================================== --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

        <div>

            <p class="text-sm font-semibold text-blue-800 uppercase tracking-wide">
                Panel de Control
            </p>

            <h1 class="text-3xl font-bold text-slate-900 mt-1">
                Dashboard
            </h1>

            <p class="text-slate-500 mt-1">
                Resumen general de las actividades del laboratorio dental.
            </p>

        </div>


        {{-- NUEVA ORDEN --}}
        <a
            href="{{ route('ordenes.create') }}"
            class="inline-flex items-center justify-center gap-2
                   bg-blue-800 hover:bg-blue-900
                   text-white px-5 py-3 rounded-lg
                   font-semibold text-sm transition shadow-sm"
        >
            <span class="text-lg">+</span>
            Nueva Orden
        </a>

    </div>


    {{-- =========================================================
         TARJETAS / INDICADORES
    ========================================================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">


        {{-- TRABAJOS DE HOY --}}
        <div class="bg-white border border-slate-200 border-l-4
                    border-emerald-600 rounded-xl shadow-sm p-5">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                        Trabajos de hoy
                    </p>

                    <p class="text-3xl font-bold text-slate-900 mt-2">
                        {{ $trabajosHoy }}
                    </p>

                    <p class="text-xs text-emerald-700 mt-2">
                        Entregas programadas hoy
                    </p>

                </div>


                <div class="w-12 h-12 bg-emerald-50
                            text-emerald-700 rounded-xl
                            flex items-center justify-center text-xl">

                    📅

                </div>

            </div>

        </div>


        {{-- ÓRDENES PENDIENTES --}}
        <div class="bg-white border border-slate-200 border-l-4
                    border-blue-700 rounded-xl shadow-sm p-5">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                        Órdenes pendientes
                    </p>

                    <p class="text-3xl font-bold text-slate-900 mt-2">
                        {{ $ordenesPendientes }}
                    </p>

                    <p class="text-xs text-blue-700 mt-2">
                        Pendientes de iniciar
                    </p>

                </div>


                <div class="w-12 h-12 bg-blue-50
                            text-blue-700 rounded-xl
                            flex items-center justify-center text-xl">

                    📋

                </div>

            </div>

        </div>


        {{-- EN PROCESO --}}
        <div class="bg-white border border-slate-200 border-l-4
                    border-amber-500 rounded-xl shadow-sm p-5">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                        En producción
                    </p>

                    <p class="text-3xl font-bold text-slate-900 mt-2">
                        {{ $ordenesEnProceso }}
                    </p>

                    <p class="text-xs text-amber-700 mt-2">
                        Trabajos actualmente activos
                    </p>

                </div>


                <div class="w-12 h-12 bg-amber-50
                            text-amber-700 rounded-xl
                            flex items-center justify-center text-xl">

                    ⚙️

                </div>

            </div>

        </div>


        {{-- ENTREGADAS --}}
        <div class="bg-white border border-slate-200 border-l-4
                    border-violet-600 rounded-xl shadow-sm p-5">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                        Órdenes entregadas
                    </p>

                    <p class="text-3xl font-bold text-slate-900 mt-2">
                        {{ $ordenesEntregadas }}
                    </p>

                    <p class="text-xs text-violet-700 mt-2">
                        Trabajos finalizados
                    </p>

                </div>


                <div class="w-12 h-12 bg-violet-50
                            text-violet-700 rounded-xl
                            flex items-center justify-center text-xl">

                    ✓

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         ACCESOS PRINCIPALES
    ========================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">


        {{-- AGENDA --}}
        <a
            href="{{ route('agenda.index') }}"
            class="bg-white border border-slate-200 rounded-xl
                   shadow-sm p-6 hover:shadow-md
                   hover:border-blue-300 transition group"
        >

            <div class="flex items-start justify-between">

                <div>

                    <div class="w-11 h-11 bg-blue-50 text-blue-800
                                rounded-xl flex items-center justify-center mb-4">
                        📅
                    </div>

                    <h2 class="font-bold text-lg text-slate-900">
                        Agenda de Producción
                    </h2>

                    <p class="text-sm text-slate-500 mt-2">
                        Consulte los trabajos programados por fecha,
                        categoría y etapa.
                    </p>

                </div>

                <span class="text-slate-300 group-hover:text-blue-800 transition">
                    →
                </span>

            </div>

        </a>


        {{-- ÓRDENES --}}
        <a
            href="{{ route('ordenes.index') }}"
            class="bg-white border border-slate-200 rounded-xl
                   shadow-sm p-6 hover:shadow-md
                   hover:border-blue-300 transition group"
        >

            <div class="flex items-start justify-between">

                <div>

                    <div class="w-11 h-11 bg-emerald-50 text-emerald-700
                                rounded-xl flex items-center justify-center mb-4">
                        📋
                    </div>

                    <h2 class="font-bold text-lg text-slate-900">
                        Órdenes de Trabajo
                    </h2>

                    <p class="text-sm text-slate-500 mt-2">
                        Consulte, edite y dé seguimiento a las órdenes
                        registradas.
                    </p>

                </div>

                <span class="text-slate-300 group-hover:text-blue-800 transition">
                    →
                </span>

            </div>

        </a>


        {{-- PRODUCCIÓN --}}
        <div
            class="bg-white border border-slate-200 rounded-xl
                   shadow-sm p-6"
        >

            <div>

                <div class="w-11 h-11 bg-amber-50 text-amber-700
                            rounded-xl flex items-center justify-center mb-4">
                    ⚙️
                </div>

                <h2 class="font-bold text-lg text-slate-900">
                    Producción
                </h2>

                <p class="text-sm text-slate-500 mt-2">
                    Seguimiento de etapas, técnicos y trazabilidad
                    de trabajos.
                </p>

                <p class="text-xs text-slate-400 mt-4">
                    Módulo en construcción
                </p>

            </div>

        </div>

    </div>


    {{-- =========================================================
         RESUMEN DEL DÍA
    ========================================================== --}}
    <section class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">

        {{-- CABECERA --}}
        <div class="flex flex-col md:flex-row md:items-center
                    md:justify-between gap-4
                    px-6 py-5 border-b border-slate-200">

            <div>

                <h2 class="text-xl font-bold text-slate-900">
                    Agenda del Día
                </h2>

                <p class="text-xs text-slate-400 uppercase tracking-widest mt-1">
                    Resumen de producción diaria
                </p>

            </div>


            <div class="flex gap-3">

                <a
                    href="{{ route('agenda.index') }}"
                    class="inline-flex items-center justify-center
                           border border-slate-300
                           text-slate-700 px-4 py-2.5
                           rounded-lg font-semibold text-sm
                           hover:bg-slate-50 transition"
                >
                    Ver Agenda
                </a>


                <a
                    href="{{ route('ordenes.create') }}"
                    class="inline-flex items-center justify-center gap-2
                           bg-blue-800 hover:bg-blue-900
                           text-white px-5 py-2.5 rounded-lg
                           font-semibold text-sm transition"
                >
                    + Nueva Orden
                </a>

            </div>

        </div>


        {{-- CONTENIDO --}}
        <div class="p-8">

            @if($trabajosHoy > 0)

                <div class="flex flex-col md:flex-row md:items-center
                            md:justify-between gap-5
                            bg-blue-50 border border-blue-100
                            rounded-xl p-6">

                    <div>

                        <p class="text-sm text-blue-700 font-semibold">
                            Producción programada
                        </p>

                        <p class="text-2xl font-bold text-slate-900 mt-1">
                            {{ $trabajosHoy }}
                            {{ $trabajosHoy === 1 ? 'trabajo' : 'trabajos' }}
                        </p>

                        <p class="text-sm text-slate-500 mt-1">
                            con fecha de entrega programada para hoy.
                        </p>

                    </div>


                    <a
                        href="{{ route('agenda.index', ['fecha' => now()->toDateString()]) }}"
                        class="inline-flex items-center justify-center
                               bg-blue-800 hover:bg-blue-900
                               text-white px-5 py-3
                               rounded-lg font-semibold text-sm"
                    >
                        Consultar trabajos
                    </a>

                </div>

            @else

                <div class="text-center py-8">

                    <div class="w-14 h-14 mx-auto bg-slate-100
                                rounded-full flex items-center
                                justify-center text-xl mb-4">
                        📅
                    </div>

                    <p class="font-semibold text-slate-700">
                        No hay trabajos programados para hoy
                    </p>

                    <p class="text-sm text-slate-400 mt-1">
                        Puede consultar otras fechas desde la Agenda de Producción.
                    </p>

                    <a
                        href="{{ route('agenda.index') }}"
                        class="inline-block text-blue-800 font-semibold
                               text-sm mt-4 hover:underline"
                    >
                        Ir a la Agenda →
                    </a>

                </div>

            @endif

        </div>

    </section>


    {{-- =========================================================
         INFORMACIÓN DEL USUARIO
    ========================================================== --}}
    <div class="mt-8 flex flex-col sm:flex-row sm:items-center
                sm:justify-between gap-3 text-sm text-slate-400">

        <p>
            Sesión iniciada como
            <span class="font-semibold text-slate-600">
                {{ auth()->user()->name }}
            </span>
        </p>

        <p>
            Rol:
            <span class="font-semibold text-slate-600">
                {{ auth()->user()->role?->nombre ?? 'Sin rol' }}
            </span>
        </p>

    </div>

</div>

@endsection