@extends('layouts.app')

@section('title', 'Mensajería')

@section('content')

<div class="max-w-7xl mx-auto">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

        <div>
            <p class="text-sm font-semibold text-blue-800 uppercase">
                Logística
            </p>

            <h1 class="text-3xl font-bold text-slate-900">
                Mensajería
            </h1>

            <p class="text-slate-500 mt-1">
                Control de entregas y recolecciones del laboratorio.
            </p>
        </div>

        <button class="bg-blue-800 text-white px-5 py-3 rounded-lg font-semibold">
            + Registrar Servicio
        </button>

    </div>


    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <p class="text-xs uppercase font-semibold text-slate-500">
                Entregas de hoy
            </p>

            <p class="text-3xl font-bold text-blue-800 mt-2">
                0
            </p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <p class="text-xs uppercase font-semibold text-slate-500">
                Recolecciones
            </p>

            <p class="text-3xl font-bold text-emerald-700 mt-2">
                0
            </p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <p class="text-xs uppercase font-semibold text-slate-500">
                Pendientes
            </p>

            <p class="text-3xl font-bold text-amber-600 mt-2">
                0
            </p>
        </div>

    </div>


    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <section class="bg-white border border-slate-200 rounded-xl shadow-sm">

            <div class="px-6 py-5 border-b">
                <h2 class="text-lg font-bold">
                    Entregas
                </h2>
            </div>

            <div class="px-6 py-12 text-center text-slate-400">
                No hay entregas programadas.
            </div>

        </section>


        <section class="bg-white border border-slate-200 rounded-xl shadow-sm">

            <div class="px-6 py-5 border-b">
                <h2 class="text-lg font-bold">
                    Recolecciones
                </h2>
            </div>

            <div class="px-6 py-12 text-center text-slate-400">
                No hay recolecciones programadas.
            </div>

        </section>

    </div>

</div>

@endsection