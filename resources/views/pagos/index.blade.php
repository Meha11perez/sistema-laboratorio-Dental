@extends('layouts.app')

@section('title', 'Pagos y Créditos | Laboratorio Dental')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- ENCABEZADO --}}
    <div class="mb-8">

        <p class="text-sm font-semibold text-blue-800 uppercase">
            Finanzas
        </p>

        <h1 class="text-3xl font-bold text-slate-900">
            Pagos y Créditos
        </h1>

        <p class="text-slate-500 mt-1">
            Control de pagos, saldos pendientes y cuentas por cobrar.
        </p>

    </div>


    {{-- TARJETAS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

        <div class="bg-white border-l-4 border-red-600
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase font-semibold text-slate-500">
                Pendientes
            </p>

            <p class="text-3xl font-bold text-red-700 mt-2">
                {{ $pagosPendientes }}
            </p>

        </div>


        <div class="bg-white border-l-4 border-amber-600
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase font-semibold text-slate-500">
                Pagos parciales
            </p>

            <p class="text-3xl font-bold text-amber-700 mt-2">
                {{ $pagosParciales }}
            </p>

        </div>


        <div class="bg-white border-l-4 border-emerald-600
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase font-semibold text-slate-500">
                Pagados
            </p>

            <p class="text-3xl font-bold text-emerald-700 mt-2">
                {{ $pagosCompletos }}
            </p>

        </div>


        <div class="bg-white border-l-4 border-blue-700
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase font-semibold text-slate-500">
                Saldo pendiente total
            </p>

            <p class="text-3xl font-bold text-blue-800 mt-2">
                Q {{ number_format($saldoPendienteTotal, 2) }}
            </p>

        </div>

    </div>


    {{-- FILTROS --}}
    <form
        method="GET"
        action="{{ route('pagos.index') }}"
        class="bg-white border border-slate-200
               rounded-xl shadow-sm p-5 mb-8"
    >

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- ORDEN --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Orden
                </label>

                <input
                    type="text"
                    name="orden"
                    value="{{ request('orden') }}"
                    placeholder="ORD-2026-..."
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-2.5"
                >

            </div>


            {{-- ODONTÓLOGO --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Odontólogo
                </label>

                <input
                    type="text"
                    name="odontologo"
                    value="{{ request('odontologo') }}"
                    placeholder="Buscar odontólogo..."
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-2.5"
                >

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

                    <option
                        value="Pendiente"
                        @selected(request('estado') === 'Pendiente')
                    >
                        Pendiente
                    </option>

                    <option
                        value="Parcial"
                        @selected(request('estado') === 'Parcial')
                    >
                        Parcial
                    </option>

                    <option
                        value="Pagado"
                        @selected(request('estado') === 'Pagado')
                    >
                        Pagado
                    </option>

                </select>

            </div>

        </div>


        <div class="flex justify-end gap-3 mt-5">

            <a
                href="{{ route('pagos.index') }}"
                class="px-5 py-2.5 border border-slate-300
                       rounded-lg font-semibold text-slate-600"
            >
                Limpiar
            </a>

            <button
                type="submit"
                class="px-6 py-2.5 bg-blue-800
                       hover:bg-blue-900
                       text-white rounded-lg font-semibold"
            >
                Filtrar
            </button>

        </div>

    </form>


    {{-- TABLA --}}
    <section class="bg-white border border-slate-200
                    rounded-xl shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-200">

            <h2 class="text-lg font-bold text-slate-900">
                Registro de Pagos
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Estado financiero de las órdenes registradas.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50 text-slate-500 uppercase text-xs">

                    <tr>
                        <th class="text-left px-6 py-4">Orden</th>
                        <th class="text-left px-6 py-4">Odontólogo</th>
                        <th class="text-left px-6 py-4">Total</th>
                        <th class="text-left px-6 py-4">Pagado</th>
                        <th class="text-left px-6 py-4">Saldo</th>
                        <th class="text-left px-6 py-4">Modalidad</th>
                        <th class="text-left px-6 py-4">Estado</th>
                        <th class="text-left px-6 py-4">Acción</th>
                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($pagos as $pago)

                        <tr class="hover:bg-slate-50">

                            <td class="px-6 py-4 font-semibold text-blue-900">
                                {{ $pago->ordenTrabajo?->codigo ?? '—' }}
                            </td>


                            <td class="px-6 py-4">
                                {{ $pago->odontologo?->nombre ?? '—' }}
                            </td>


                            <td class="px-6 py-4">
                                Q {{ number_format($pago->monto_total, 2) }}
                            </td>


                            <td class="px-6 py-4 text-emerald-700 font-semibold">
                                Q {{ number_format($pago->monto_pagado, 2) }}
                            </td>


                            <td class="px-6 py-4 font-semibold">
                                Q {{ number_format($pago->saldo_pendiente, 2) }}
                            </td>


                            <td class="px-6 py-4">
                                {{ $pago->cuentaOdontologo?->modalidad_pago ?? 'Sin configurar' }}
                            </td>


                            <td class="px-6 py-4">

                                @if($pago->estado_pago === 'Pagado')

                                    <span class="bg-emerald-100 text-emerald-700
                                                 px-3 py-1 rounded-full
                                                 text-xs font-semibold">
                                        Pagado
                                    </span>

                                @elseif($pago->estado_pago === 'Parcial')

                                    <span class="bg-amber-100 text-amber-700
                                                 px-3 py-1 rounded-full
                                                 text-xs font-semibold">
                                        Parcial
                                    </span>

                                @else

                                    <span class="bg-red-100 text-red-700
                                                 px-3 py-1 rounded-full
                                                 text-xs font-semibold">
                                        Pendiente
                                    </span>

                                @endif

                            </td>


                            <td class="px-6 py-4">

                                @if($pago->ordenTrabajo)

                                <a
                                    href="{{ route(
                                        'pagos.show',
                                        $pago->ordenTrabajo
                                    ) }}"
                                    class="text-blue-700 font-semibold hover:underline"
                                >
                                    Ver pago
                                </a>

                            @endif


                            @if($pago->odontologo)

                                <a
                                    href="{{ route(
                                        'cuentas-odontologos.edit',
                                        $pago->odontologo
                                    ) }}"
                                    class="ml-3 text-amber-700
                                        font-semibold hover:underline"
                                >
                                    Configurar cuenta
                                </a>

                            @endif
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="8"
                                class="px-6 py-12 text-center text-slate-400"
                            >
                                No hay pagos registrados.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($pagos->hasPages())

            <div class="px-6 py-4 border-t border-slate-200">
                {{ $pagos->links() }}
            </div>

        @endif

    </section>

</div>

@endsection