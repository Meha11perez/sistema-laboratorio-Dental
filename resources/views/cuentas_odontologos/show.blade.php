@extends('layouts.app')

@section('title', 'Detalle de Cuenta | Laboratorio Dental')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col md:flex-row
                md:items-center md:justify-between
                gap-4 mb-8">

        <div>

            <a
                href="{{ route('cuentas-odontologos.index') }}"
                class="text-sm font-semibold
                       text-blue-700 hover:underline"
            >
                ← Volver a Cuentas de Odontólogos
            </a>

            <h1 class="text-3xl font-bold text-slate-900 mt-3">
                Cuenta del Odontólogo
            </h1>

            <p class="text-slate-500 mt-1">
                {{ $odontologo->nombre }}
            </p>

        </div>


        <a
            href="{{ route(
                'cuentas-odontologos.edit',
                $odontologo
            ) }}"
            class="px-5 py-3
                   border border-blue-700
                   text-blue-700
                   hover:bg-blue-50
                   rounded-lg font-semibold"
        >
            Configurar cuenta
        </a>

    </div>


    {{-- INFORMACIÓN GENERAL --}}
    <section class="bg-white border border-slate-200
                    rounded-xl shadow-sm overflow-hidden mb-8">

        <div class="px-6 py-5 border-b border-slate-200">

            <h2 class="text-lg font-bold text-slate-900">
                Resumen de Crédito
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Resumen financiero general del odontólogo.
            </p>

        </div>


        <div class="p-6 grid grid-cols-1
                    md:grid-cols-2 lg:grid-cols-4 gap-5">

            {{-- MODALIDAD --}}
            <div>

                <p class="text-xs uppercase
                          font-semibold text-slate-400">
                    Modalidad
                </p>

                <p class="font-bold text-slate-900 mt-2">
                    {{ $cuenta->modalidad_pago }}
                </p>

            </div>


            {{-- LÍMITE --}}
            <div>

                <p class="text-xs uppercase
                          font-semibold text-slate-400">
                    Límite de crédito
                </p>

                <p class="font-bold text-slate-900 mt-2">

                    @if($cuenta->modalidad_pago === 'Crédito')

                        Q {{ number_format(
                            $cuenta->limite_credito,
                            2
                        ) }}

                    @else

                        —

                    @endif

                </p>

            </div>


            {{-- SALDO --}}
            <div>

                <p class="text-xs uppercase
                          font-semibold text-slate-400">
                    Saldo pendiente
                </p>

                <p class="font-bold text-amber-700 mt-2">
                    Q {{ number_format(
                        $saldoPendiente,
                        2
                    ) }}
                </p>

            </div>


            {{-- DISPONIBLE --}}
            <div>

                <p class="text-xs uppercase
                          font-semibold text-slate-400">
                    Crédito disponible
                </p>

                <p class="font-bold text-blue-800 mt-2">

                    @if($cuenta->modalidad_pago === 'Crédito')

                        @if($cuenta->limite_superado)

                            Crédito excedido

                        @else

                            Q {{ number_format(
                                $cuenta->credito_disponible,
                                2
                            ) }}

                        @endif

                    @else

                        —

                    @endif

                </p>

            </div>

        </div>

    </section>


    {{-- TOTALES --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">

        <div class="bg-white border-l-4 border-blue-700
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase font-semibold text-slate-500">
                Total facturado
            </p>

            <p class="text-3xl font-bold text-slate-900 mt-2">
                Q {{ number_format($montoTotal, 2) }}
            </p>

        </div>


        <div class="bg-white border-l-4 border-emerald-600
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase font-semibold text-slate-500">
                Total pagado
            </p>

            <p class="text-3xl font-bold text-emerald-700 mt-2">
                Q {{ number_format($montoPagado, 2) }}
            </p>

        </div>


        <div class="bg-white border-l-4 border-amber-600
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase font-semibold text-slate-500">
                Total pendiente
            </p>

            <p class="text-3xl font-bold text-amber-700 mt-2">
                Q {{ number_format($saldoPendiente, 2) }}
            </p>

        </div>

    </div>


    {{-- ÓRDENES --}}
    <section class="bg-white border border-slate-200
                    rounded-xl shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-200">

            <h2 class="text-lg font-bold text-slate-900">
                Órdenes Asociadas
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Detalle de trabajos y estado de sus pagos.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50
                              text-slate-500 uppercase text-xs">

                    <tr>

                        <th class="text-left px-6 py-4">
                            Orden
                        </th>

                        <th class="text-left px-6 py-4">
                            Paciente
                        </th>

                        <th class="text-right px-6 py-4">
                            Total
                        </th>

                        <th class="text-right px-6 py-4">
                            Pagado
                        </th>

                        <th class="text-right px-6 py-4">
                            Saldo
                        </th>

                        <th class="text-center px-6 py-4">
                            Estado
                        </th>

                        <th class="text-right px-6 py-4">
                            Acción
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($pagos as $pago)

                        <tr class="hover:bg-slate-50">

                            {{-- ORDEN --}}
                            <td class="px-6 py-4
                                       font-semibold text-blue-900">

                                {{ $pago->ordenTrabajo?->codigo ?? '—' }}

                            </td>


                            {{-- PACIENTE --}}
                            <td class="px-6 py-4">

                                {{ $pago->ordenTrabajo?->paciente?->nombre ?? '—' }}

                                {{ $pago->ordenTrabajo?->paciente?->apellido ?? '' }}

                            </td>


                            {{-- TOTAL --}}
                            <td class="px-6 py-4 text-right">

                                @if((float) $pago->monto_total > 0)

                                    Q {{ number_format(
                                        $pago->monto_total,
                                        2
                                    ) }}

                                @else

                                    <span class="text-slate-400">
                                        Por definir
                                    </span>

                                @endif

                            </td>


                            {{-- PAGADO --}}
                            <td class="px-6 py-4 text-right
                                       font-semibold text-emerald-700">

                                Q {{ number_format(
                                    $pago->monto_pagado,
                                    2
                                ) }}

                            </td>


                            {{-- SALDO --}}
                            <td class="px-6 py-4 text-right font-semibold">

                                Q {{ number_format(
                                    $pago->saldo_pendiente,
                                    2
                                ) }}

                            </td>


                            {{-- ESTADO --}}
                            <td class="px-6 py-4 text-center">

                                @if((float) $pago->monto_total <= 0)

                                    <span class="inline-flex px-3 py-1
                                                 rounded-full text-xs font-semibold
                                                 bg-slate-100 text-slate-600">
                                        Por configurar
                                    </span>

                                @elseif($pago->estado_pago === 'Pagado')

                                    <span class="inline-flex px-3 py-1
                                                 rounded-full text-xs font-semibold
                                                 bg-emerald-100 text-emerald-700">
                                        Pagado
                                    </span>

                                @elseif($pago->estado_pago === 'Parcial')

                                    <span class="inline-flex px-3 py-1
                                                 rounded-full text-xs font-semibold
                                                 bg-amber-100 text-amber-700">
                                        Parcial
                                    </span>

                                @else

                                    <span class="inline-flex px-3 py-1
                                                 rounded-full text-xs font-semibold
                                                 bg-red-100 text-red-700">
                                        Pendiente
                                    </span>

                                @endif

                            </td>


                            {{-- ACCIÓN --}}
                            <td class="px-6 py-4 text-right">

                                @if($pago->ordenTrabajo)

                                    <a
                                        href="{{ route(
                                            'pagos.show',
                                            [
                                                'orden' => $pago->ordenTrabajo,
                                                'origen' => 'pagos',
                                            ]
                                        ) }}"
                                        class="text-blue-700
                                               font-semibold
                                               hover:underline"
                                    >
                                        Ver pago
                                    </a>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="px-6 py-12
                                       text-center text-slate-400"
                            >
                                Este odontólogo todavía no tiene
                                órdenes financieras registradas.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>

</div>

@endsection