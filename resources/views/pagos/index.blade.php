@extends('layouts.app')

@section('title', 'Pagos y Créditos')

@section('content')

<div class="max-w-7xl mx-auto">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

        <div>
            <p class="text-sm font-semibold text-blue-800 uppercase">
                Finanzas
            </p>

            <h1 class="text-3xl font-bold text-slate-900">
                Pagos y Créditos
            </h1>

            <p class="text-slate-500 mt-1">
                Control de pagos, abonos y cuentas pendientes.
            </p>
        </div>

        <button class="bg-blue-800 text-white px-5 py-3 rounded-lg font-semibold">
            + Registrar Pago
        </button>

    </div>


    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">

        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
            <p class="text-xs uppercase text-slate-500 font-semibold">
                Ingresos
            </p>

            <p class="text-3xl font-bold text-emerald-700 mt-2">
                Q 0.00
            </p>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
            <p class="text-xs uppercase text-slate-500 font-semibold">
                Créditos pendientes
            </p>

            <p class="text-3xl font-bold text-amber-600 mt-2">
                Q 0.00
            </p>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
            <p class="text-xs uppercase text-slate-500 font-semibold">
                Pagos pendientes
            </p>

            <p class="text-3xl font-bold text-red-600 mt-2">
                0
            </p>
        </div>

    </div>


    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-200">
            <h2 class="font-bold text-lg">
                Historial de Pagos
            </h2>
        </div>

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="text-left px-6 py-4">Orden</th>
                        <th class="text-left px-6 py-4">Odontólogo</th>
                        <th class="text-left px-6 py-4">Fecha</th>
                        <th class="text-left px-6 py-4">Modalidad</th>
                        <th class="text-left px-6 py-4">Monto</th>
                        <th class="text-left px-6 py-4">Saldo</th>
                        <th class="text-left px-6 py-4">Estado</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td colspan="7"
                            class="px-6 py-12 text-center text-slate-400">
                            No hay pagos registrados.
                        </td>
                    </tr>
                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection