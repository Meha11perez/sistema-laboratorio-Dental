@extends('layouts.app')

@section('title', 'Garantías y Devoluciones')

@section('content')

<div class="max-w-7xl mx-auto">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

        <div>
            <p class="text-sm font-semibold text-blue-800 uppercase">
                Producción
            </p>

            <h1 class="text-3xl font-bold text-slate-900">
                Garantías y Devoluciones
            </h1>

            <p class="text-slate-500 mt-1">
                Control y seguimiento de trabajos devueltos y órdenes en garantía.
            </p>
        </div>

        <button class="bg-blue-800 text-white px-5 py-3 rounded-lg font-semibold">
            + Registrar Devolución
        </button>

    </div>


    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">

        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
            <p class="text-xs uppercase text-slate-500 font-semibold">
                Garantías activas
            </p>

            <p class="text-3xl font-bold text-blue-800 mt-2">0</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
            <p class="text-xs uppercase text-slate-500 font-semibold">
                Devoluciones
            </p>

            <p class="text-3xl font-bold text-amber-600 mt-2">0</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
            <p class="text-xs uppercase text-slate-500 font-semibold">
                Repeticiones
            </p>

            <p class="text-3xl font-bold text-red-600 mt-2">0</p>
        </div>

    </div>


    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-200">
            <h2 class="font-bold text-lg">
                Registro de Garantías y Devoluciones
            </h2>
        </div>

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                    <tr>
                        <th class="text-left px-6 py-4">Orden</th>
                        <th class="text-left px-6 py-4">Paciente</th>
                        <th class="text-left px-6 py-4">Odontólogo</th>
                        <th class="text-left px-6 py-4">Motivo</th>
                        <th class="text-left px-6 py-4">Fecha</th>
                        <th class="text-left px-6 py-4">Garantía</th>
                        <th class="text-left px-6 py-4">Estado</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td colspan="7"
                            class="px-6 py-12 text-center text-slate-400">
                            No hay garantías o devoluciones registradas.
                        </td>
                    </tr>
                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection