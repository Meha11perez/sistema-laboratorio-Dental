@extends('layouts.app')

@section('title', 'Cuentas de Odontólogos')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- ENCABEZADO --}}
    <div class="mb-8">

        <h1 class="text-2xl font-bold text-slate-900">
            Cuentas de Odontólogos
        </h1>

        <p class="text-sm text-slate-500 mt-1">
            Control de modalidades de pago, saldos y límites de crédito.
        </p>

    </div>


    {{-- MENSAJE DE ÉXITO --}}
    @if(session('success'))

        <div class="mb-6 bg-green-50 border border-green-200
                    text-green-700 rounded-xl px-5 py-4">

            {{ session('success') }}

        </div>

    @endif


    {{-- TABLA --}}
    <section
        class="bg-white border border-slate-200
               rounded-xl shadow-sm overflow-hidden"
    >

        {{-- ENCABEZADO DE TABLA --}}
        <div class="px-6 py-5 border-b border-slate-200">

            <h2 class="text-lg font-bold text-slate-900">
                Estado de cuentas
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Información financiera configurada para cada odontólogo.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead
                    class="bg-slate-50 text-slate-500
                           uppercase text-xs"
                >

                    <tr>

                        <th class="text-left px-6 py-4">
                            Odontólogo
                        </th>

                        <th class="text-left px-6 py-4">
                            Modalidad
                        </th>

                        <th class="text-right px-6 py-4">
                            Límite
                        </th>

                        <th class="text-right px-6 py-4">
                            Saldo
                        </th>

                        <th class="text-right px-6 py-4">
                            Disponible
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

                    @forelse($odontologos as $odontologo)

                        @php
                            $cuenta = $odontologo->cuenta;
                        @endphp

                        <tr class="hover:bg-slate-50 transition">

                            {{-- ODONTÓLOGO --}}
                            <td class="px-6 py-4">

                                <p class="font-semibold text-slate-900">
                                    {{ $odontologo->nombre }}
                                </p>

                                @if($odontologo->clinica)

                                    <p class="text-xs text-slate-500 mt-1">
                                        {{ $odontologo->clinica?->nombre }}
                                    </p>

                                @endif

                            </td>


                            {{-- MODALIDAD --}}
                            <td class="px-6 py-4">

                                @if(!$cuenta)

                                    <span class="inline-flex px-3 py-1
                                                rounded-full text-xs font-semibold
                                                bg-slate-100 text-slate-500">
                                        Sin configurar
                                    </span>

                                @elseif($cuenta->modalidad_pago === 'Crédito')

                                    <span class="inline-flex px-3 py-1
                                                rounded-full text-xs font-semibold
                                                bg-blue-50 text-blue-700">
                                        Crédito
                                    </span>

                                @elseif($cuenta->modalidad_pago === 'Semanal')

                                    <span class="inline-flex px-3 py-1
                                                rounded-full text-xs font-semibold
                                                bg-amber-50 text-amber-700">
                                        Semanal
                                    </span>

                                @else

                                    <span class="inline-flex px-3 py-1
                                                rounded-full text-xs font-semibold
                                                bg-slate-100 text-slate-700">
                                        Contado
                                    </span>

                                @endif

                            </td>


                            {{-- LÍMITE --}}
                            <td class="px-6 py-4 text-right font-semibold text-slate-700">

                                @if(
                                    $cuenta &&
                                    $cuenta->modalidad_pago === 'Crédito'
                                )

                                    Q {{ number_format(
                                        $cuenta->limite_credito,
                                        2
                                    ) }}

                                @else

                                    —

                                @endif

                            </td>


                            {{-- SALDO --}}
                            <td class="px-6 py-4 text-right">

                                @if($cuenta)

                                    <span
                                        class="font-bold
                                        {{ (float) $cuenta->saldo_pendiente > 0
                                            ? 'text-amber-700'
                                            : 'text-green-700' }}"
                                    >

                                        Q {{ number_format(
                                            $cuenta->saldo_pendiente,
                                            2
                                        ) }}

                                    </span>

                                @else

                                    <span class="text-slate-400">
                                        —
                                    </span>

                                @endif

                            </td>


                            {{-- DISPONIBLE --}}
                            <td class="px-6 py-4 text-right">

                                @if(
                                    $cuenta &&
                                    $cuenta->modalidad_pago === 'Crédito'
                                )

                                    @if($cuenta->limite_superado)

                                        <span class="font-bold text-red-700">
                                            Excedido
                                        </span>

                                    @else

                                        <span class="font-semibold text-slate-700">

                                            Q {{ number_format(
                                                $cuenta->credito_disponible,
                                                2
                                            ) }}

                                        </span>

                                    @endif

                                @else

                                    —

                                @endif

                            </td>


                            {{-- ESTADO --}}
                            <td class="px-6 py-4 text-center">

                                @if(!$cuenta)

                                    <span class="inline-flex px-3 py-1
                                                rounded-full text-xs font-semibold
                                                bg-slate-100 text-slate-500">
                                        Sin configurar
                                    </span>

                                @elseif($cuenta->estado)

                                    <span class="inline-flex px-3 py-1
                                                rounded-full text-xs font-semibold
                                                bg-green-50 text-green-700">
                                        Activa
                                    </span>

                                @else

                                    <span class="inline-flex px-3 py-1
                                                rounded-full text-xs font-semibold
                                                bg-red-50 text-red-700">
                                        Inactiva
                                    </span>

                                @endif

                            </td>


                            {{-- ACCIÓN --}}
                            <td class="px-6 py-4 text-right">

                                <a
                                    href="{{ route(
                                        'cuentas-odontologos.edit',
                                        $odontologo
                                    ) }}"
                                    class="inline-flex items-center justify-center
                                        px-4 py-2
                                        border border-blue-700
                                        text-blue-700
                                        hover:bg-blue-50
                                        rounded-lg font-semibold text-xs
                                        transition"
                                >
                                    {{ $cuenta ? 'Configurar' : 'Crear cuenta' }}
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="px-6 py-12 text-center text-slate-400"
                            >
                                No hay odontólogos registrados.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- PAGINACIÓN --}}
        @if($odontologos->hasPages())

            <div class="px-6 py-4 border-t border-slate-200">
                {{ $odontologos->links() }}
            </div>

        @endif  

    </section>

</div>

@endsection