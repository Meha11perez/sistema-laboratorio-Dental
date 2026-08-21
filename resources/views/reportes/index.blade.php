@extends('layouts.app')

@section('title', 'Reportes')

@section('content')

<div class="max-w-7xl mx-auto">

    <div class="mb-8">

        <p class="text-sm font-semibold text-blue-800 uppercase">
            Análisis
        </p>

        <h1 class="text-3xl font-bold text-slate-900">
            Reportes
        </h1>

        <p class="text-slate-500 mt-1">
            Consulta y generación de información del laboratorio.
        </p>

    </div>


    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">

        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
            <div class="text-2xl mb-4">📋</div>

            <h2 class="font-bold text-lg">
                Producción
            </h2>

            <p class="text-sm text-slate-500 mt-2">
                Producción por período, tipo de prótesis y estado.
            </p>

            <button class="text-blue-800 font-semibold text-sm mt-5">
                Generar reporte →
            </button>
        </div>


        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
            <div class="text-2xl mb-4">👨‍🔧</div>

            <h2 class="font-bold text-lg">
                Producción por Técnico
            </h2>

            <p class="text-sm text-slate-500 mt-2">
                Cantidad de trabajos y etapas realizadas por técnico.
            </p>

            <button class="text-blue-800 font-semibold text-sm mt-5">
                Generar reporte →
            </button>
        </div>


        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
            <div class="text-2xl mb-4">📦</div>

            <h2 class="font-bold text-lg">
                Inventario
            </h2>

            <p class="text-sm text-slate-500 mt-2">
                Existencias, movimientos y materiales con stock bajo.
            </p>

            <button class="text-blue-800 font-semibold text-sm mt-5">
                Generar reporte →
            </button>
        </div>


        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
            <div class="text-2xl mb-4">↩️</div>

            <h2 class="font-bold text-lg">
                Garantías y Devoluciones
            </h2>

            <p class="text-sm text-slate-500 mt-2">
                Motivos, repeticiones y trabajos devueltos.
            </p>

            <button class="text-blue-800 font-semibold text-sm mt-5">
                Generar reporte →
            </button>
        </div>


        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
            <div class="text-2xl mb-4">💰</div>

            <h2 class="font-bold text-lg">
                Pagos
            </h2>

            <p class="text-sm text-slate-500 mt-2">
                Ingresos, créditos y saldos pendientes.
            </p>

            <button class="text-blue-800 font-semibold text-sm mt-5">
                Generar reporte →
            </button>
        </div>


        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
            <div class="text-2xl mb-4">🚚</div>

            <h2 class="font-bold text-lg">
                Mensajería
            </h2>

            <p class="text-sm text-slate-500 mt-2">
                Entregas y recolecciones realizadas.
            </p>

            <button class="text-blue-800 font-semibold text-sm mt-5">
                Generar reporte →
            </button>
        </div>

    </div>

</div>

@endsection