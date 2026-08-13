@extends('layouts.app')

@section('title', 'Dashboard | Laboratorio Dental')

@section('content')

    {{-- ENCABEZADO --}}
    <div class="mb-8">

        <h1 class="text-3xl font-bold text-slate-900">
            Dashboard
        </h1>

        <p class="text-slate-500 mt-1">
            Resumen general del laboratorio dental
        </p>

    </div>


    {{-- TARJETAS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

        {{-- TRABAJOS --}}
        <div class="bg-white border-l-4 border-emerald-600 rounded-lg shadow-sm p-5 flex items-center gap-4">

            <div class="w-12 h-12 bg-emerald-50 text-emerald-700 rounded-xl flex items-center justify-center text-xl">
                ▣
            </div>

            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase">
                    Trabajos esta semana
                </p>

                <p class="text-3xl font-bold mt-1">
                    0
                </p>
            </div>

        </div>


        {{-- PENDIENTES --}}
        <div class="bg-white border-l-4 border-blue-700 rounded-lg shadow-sm p-5 flex items-center gap-4">

            <div class="w-12 h-12 bg-blue-50 text-blue-700 rounded-xl flex items-center justify-center text-xl">
                ⚑
            </div>

            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase">
                    Órdenes pendientes
                </p>

                <p class="text-3xl font-bold mt-1">
                    0
                </p>
            </div>

        </div>


        {{-- STOCK BAJO --}}
        <div class="bg-white border-l-4 border-red-600 rounded-lg shadow-sm p-5 flex items-center gap-4">

            <div class="w-12 h-12 bg-red-50 text-red-700 rounded-xl flex items-center justify-center text-xl">
                ⚠
            </div>

            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase">
                    Materiales stock bajo
                </p>

                <p class="text-3xl font-bold mt-1">
                    0
                </p>
            </div>

        </div>


        {{-- DEVOLUCIONES --}}
        <div class="bg-white border-l-4 border-amber-700 rounded-lg shadow-sm p-5 flex items-center gap-4">

            <div class="w-12 h-12 bg-amber-50 text-amber-700 rounded-xl flex items-center justify-center text-xl">
                ↻
            </div>

            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase">
                    Devoluciones semana
                </p>

                <p class="text-3xl font-bold mt-1">
                    0
                </p>
            </div>

        </div>

    </div>


    {{-- ALERTA --}}
    <div class="mb-8 bg-red-50 border border-red-200 text-red-700 rounded-lg px-5 py-4">

        <p class="text-sm">
            ⚠ Alerta de inventario:
            no hay materiales por debajo del stock mínimo.
        </p>

    </div>


    {{-- AGENDA --}}
    <section class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">

        {{-- CABECERA --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 px-6 py-5 border-b border-slate-200">

            <div>
                <h2 class="text-xl font-bold">
                    Agenda del Día
                </h2>

                <p class="text-xs text-slate-400 uppercase tracking-widest mt-1">
                    Resumen de producción diaria
                </p>
            </div>

            <a
                href="#"
                class="inline-flex items-center justify-center gap-2 bg-blue-800 hover:bg-blue-900 text-white px-5 py-3 rounded-md font-semibold text-sm"
            >
                + Nueva Orden
            </a>

        </div>


        {{-- TABLA --}}
        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50 text-slate-500 uppercase text-xs">

                    <tr>
                        <th class="text-left px-6 py-4">N° Orden</th>
                        <th class="text-left px-6 py-4">Paciente</th>
                        <th class="text-left px-6 py-4">Odontólogo</th>
                        <th class="text-left px-6 py-4">Tipo de Prótesis</th>
                        <th class="text-left px-6 py-4">Técnico</th>
                        <th class="text-left px-6 py-4">Estado</th>
                        <th class="text-left px-6 py-4">Garantía</th>
                    </tr>

                </thead>

                <tbody class="divide-y divide-slate-100">

                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            No hay órdenes registradas para mostrar.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>


        {{-- PIE --}}
        <div class="px-6 py-4 border-t border-slate-200 text-sm text-slate-500">

            Mostrando 0 registros

        </div>

    </section>

@endsection