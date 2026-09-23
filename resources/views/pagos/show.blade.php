@extends('layouts.app')

@section('title', 'Detalle de Pago | Laboratorio Dental')

@section('content')

<div class="max-w-6xl mx-auto">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

        <div>

        <a
            href="{{ request('origen') === 'pagos'
                ? route('pagos.index')
                : route('ordenes.show', $orden) }}"
            class="text-sm font-semibold text-blue-700 hover:underline"
        >
            ← {{ request('origen') === 'pagos'
                ? 'Volver a Pagos y Créditos'
                : 'Volver a la orden' }}
        </a>

            <h1 class="text-3xl font-bold text-slate-900 mt-3">
                Pago de la Orden
            </h1>

            <p class="text-slate-500 mt-1">
                {{ $orden->codigo }}
            </p>

        </div>

        <div>

            @if($pago->estado_pago === 'Pagado')

                <span class="inline-flex px-4 py-2 rounded-full
                             bg-emerald-100 text-emerald-700
                             font-bold text-sm">
                    ✓ Pagado
                </span>

            @elseif($pago->estado_pago === 'Parcial')

                <span class="inline-flex px-4 py-2 rounded-full
                             bg-amber-100 text-amber-700
                             font-bold text-sm">
                    Pago parcial
                </span>

            @else

                <span class="inline-flex px-4 py-2 rounded-full
                             bg-red-100 text-red-700
                             font-bold text-sm">
                    Pendiente
                </span>

            @endif

        </div>

    </div>


        {{-- MENSAJES --}}
        @if(session('success'))

            <div class="mb-6 bg-emerald-50 border border-emerald-200
                        text-emerald-700 rounded-xl px-5 py-4">
                {{ session('success') }}
            </div>

        @endif
        {{-- ==========================================================
            MODAL DE ERRORES
        ========================================================== --}}
        @if($errors->any())

            <div
                id="modalErrores"
                class="fixed inset-0 z-50 flex items-center justify-center
                    bg-black/40 px-4"
            >

                <div
                    class="bg-white w-full max-w-md rounded-2xl
                        shadow-2xl overflow-hidden"
                >

                    {{-- ENCABEZADO --}}
                    <div class="px-6 py-5 border-b border-slate-200">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-10 h-10 rounded-full bg-red-100
                                    flex items-center justify-center
                                    text-red-600 font-bold text-xl"
                            >
                                !
                            </div>

                            <div>

                                <h3 class="text-lg font-bold text-slate-900">
                                    No se pudo completar la operación
                                </h3>

                                <p class="text-sm text-slate-500">
                                    Revise la información ingresada.
                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- ERRORES --}}
                    <div class="px-6 py-5">

                        <ul class="space-y-3">

                            @foreach($errors->all() as $error)

                                <li class="flex gap-3 text-sm text-red-700">

                                    <span class="font-bold">
                                        •
                                    </span>

                                    <span>
                                        {{ $error }}
                                    </span>

                                </li>

                            @endforeach

                        </ul>

                    </div>


                    {{-- BOTÓN --}}
                    <div
                        class="px-6 py-4 bg-slate-50
                            border-t border-slate-200
                            flex justify-end"
                    >

                        <button
                            type="button"
                            onclick="cerrarModalErrores()"
                            class="px-5 py-2.5 bg-blue-800
                                hover:bg-blue-900
                                text-white rounded-lg font-semibold"
                        >
                            Entendido
                        </button>

                    </div>

                </div>

            </div>


            <script>
                function cerrarModalErrores() {

                    const modal = document.getElementById('modalErrores');

                    if (modal) {
                        modal.remove();
                    }
                }
            </script>

        @endif

        {{-- INFORMACIÓN DE LA ORDEN --}}
        <section class="bg-white border border-slate-200
                        rounded-xl shadow-sm overflow-hidden mb-8">

            <div class="px-6 py-5 border-b border-slate-200">

                <h2 class="text-lg font-bold text-slate-900">
                    Información de la Orden
                </h2>

            </div>


            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <div>

                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Orden
                    </p>

                    <p class="font-bold text-slate-900 mt-1">
                        {{ $orden->codigo }}
                    </p>

                </div>


                <div>

                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Paciente
                    </p>

                    <p class="font-semibold text-slate-900 mt-1">
                        {{ $orden->paciente?->nombre ?? '—' }}
                        {{ $orden->paciente?->apellido ?? '' }}
                    </p>

                </div>


                <div>

                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Odontólogo
                    </p>

                    <p class="font-semibold text-slate-900 mt-1">
                        {{ $orden->odontologo?->nombre ?? '—' }}
                    </p>

                </div>


                <div>

                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Modalidad de pago
                    </p>

                    <p class="font-semibold text-slate-900 mt-1">
                        {{ $pago->cuentaOdontologo?->modalidad_pago ?? 'Sin configurar' }}
                    </p>

                </div>

            </div>

        </section>

            {{-- =========================================================
                ALERTA DE LÍMITE DE CRÉDITO
            ========================================================== --}}
            @if(
                $pago->cuentaOdontologo &&
                $pago->cuentaOdontologo->modalidad_pago === 'Crédito'
            )

                @php
                    $cuenta = $pago->cuentaOdontologo;
                @endphp


                {{-- LÍMITE SUPERADO --}}
                @if($cuenta->limite_superado)

                    <div class="mb-8 bg-red-50 border border-red-200
                                rounded-xl p-5">

                        <p class="font-bold text-red-800">
                            Límite de crédito superado
                        </p>

                        <p class="text-sm text-red-700 mt-1">
                            El saldo pendiente del odontólogo supera
                            el límite de crédito configurado.
                        </p>


                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">

                            <div>

                                <p class="text-xs uppercase font-semibold text-red-500">
                                    Límite
                                </p>

                                <p class="font-bold text-red-800">
                                    Q {{ number_format(
                                        $cuenta->limite_credito,
                                        2
                                    ) }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs uppercase font-semibold text-red-500">
                                    Saldo actual
                                </p>

                                <p class="font-bold text-red-800">
                                    Q {{ number_format(
                                        $cuenta->saldo_pendiente,
                                        2
                                    ) }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs uppercase font-semibold text-red-500">
                                    Excedente
                                </p>

                                <p class="font-bold text-red-800">

                                    Q {{ number_format(
                                        (float) $cuenta->saldo_pendiente
                                        - (float) $cuenta->limite_credito,
                                        2
                                    ) }}

                                </p>

                            </div>

                        </div>

                    </div>


                {{-- CERCA DEL LÍMITE --}}
                @elseif(
                    (float) $cuenta->limite_credito > 0 &&
                    (float) $cuenta->credito_disponible
                        <= ((float) $cuenta->limite_credito * 0.20)
                )

                    <div class="mb-8 bg-amber-50 border border-amber-200
                                rounded-xl p-5">

                        <p class="font-bold text-amber-800">
                            ⚠ Crédito próximo al límite
                        </p>

                        <p class="text-sm text-amber-700 mt-1">

                            El odontólogo dispone únicamente de

                            <strong>
                                Q {{ number_format(
                                    $cuenta->credito_disponible,
                                    2
                                ) }}
                            </strong>

                            de crédito disponible.

                        </p>

                    </div>

                @endif

            @endif
            
    {{-- ==========================================================
     DEFINIR / MODIFICAR MONTO DEL TRABAJO
    ========================================================== --}}
    <section class="bg-white border border-slate-200
                    rounded-xl shadow-sm overflow-hidden mb-8">

        <div class="px-6 py-5 border-b border-slate-200">

            <h2 class="text-lg font-bold text-slate-900">
                Monto del Trabajo
            </h2>

            <p class="text-sm text-slate-500 mt-1">

                @if((float) $pago->monto_total <= 0)

                    Defina el precio que se cobrará por esta orden.

                @else

                    Puede modificar el precio del trabajo si es necesario.

                @endif

            </p>

        </div>


        <form
            method="POST"
            action="{{ route('pagos.monto.update', $pago) }}"
        >

            @csrf
            @method('PUT')


            <div class="p-6">

                <label
                    class="block text-sm font-semibold
                        text-slate-700 mb-2"
                >
                    Precio del trabajo *
                </label>


                <div class="flex flex-col md:flex-row gap-4">

                    <div class="relative flex-1">

                        <span
                            class="absolute left-4 top-3
                                text-slate-500"
                        >
                            Q
                        </span>

                        <input
                            type="number"
                            name="monto_total"
                            value="{{ old(
                                'monto_total',
                                (float) $pago->monto_total > 0
                                    ? $pago->monto_total
                                    : ''
                            ) }}"
                            min="0.01"
                            step="0.01"
                            required
                            placeholder="0.00"
                            class="w-full border border-slate-300
                                rounded-lg pl-9 pr-4 py-3"
                        >

                    </div>


                    <button
                        type="submit"
                        class="px-6 py-3 bg-blue-800
                            hover:bg-blue-900
                            text-white rounded-lg font-semibold"
                    >
                        {{ (float) $pago->monto_total > 0
                            ? 'Actualizar monto'
                            : 'Definir monto'
                        }}
                    </button>

                </div>


                @error('monto_total')

                    <p class="text-sm text-red-600 mt-2">
                        {{ $message }}
                    </p>

                @enderror


                @if((float) $pago->monto_pagado > 0)

                    <p class="text-xs text-amber-600 mt-3">
                        Esta orden ya tiene Q
                        {{ number_format($pago->monto_pagado, 2) }}
                        registrados en abonos. El nuevo monto no puede
                        ser menor a esa cantidad.
                    </p>

                @endif

            </div>

        </form>

    </section>
    {{-- RESUMEN DEL PAGO --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">

        {{-- TOTAL --}}
        <div class="bg-white border-l-4 border-blue-700
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase font-semibold text-slate-500">
                Monto total
            </p>

            <p class="text-3xl font-bold text-slate-900 mt-2">
                Q {{ number_format($pago->monto_total, 2) }}
            </p>

        </div>


        {{-- PAGADO --}}
        <div class="bg-white border-l-4 border-emerald-600
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase font-semibold text-slate-500">
                Monto pagado
            </p>

            <p class="text-3xl font-bold text-emerald-700 mt-2">
                Q {{ number_format($pago->monto_pagado, 2) }}
            </p>

        </div>


        {{-- SALDO --}}
        <div class="bg-white border-l-4 border-amber-600
                    rounded-xl shadow-sm p-5">

            <p class="text-xs uppercase font-semibold text-slate-500">
                Saldo pendiente
            </p>

            <p class="text-3xl font-bold text-amber-700 mt-2">
                Q {{ number_format($pago->saldo_pendiente, 2) }}
            </p>

        </div>

    </div>

        {{-- FORMULARIO DE ABONO --}}
        @if((float) $pago->monto_total <= 0)

            <div class="mb-8 bg-blue-50 border border-blue-200
                        rounded-xl px-6 py-5">

                <p class="font-bold text-blue-800">
                    Monto del trabajo pendiente de configurar.
                </p>

                <p class="text-sm text-blue-700 mt-1">
                    Defina primero el precio del trabajo para poder
                    registrar abonos.
                </p>

            </div>

        @elseif($pago->estado_pago !== 'Pagado')

            <section class="bg-white border border-slate-200
                            rounded-xl shadow-sm overflow-hidden mb-8">

                <div class="px-6 py-5 border-b border-slate-200">

                    <h2 class="text-lg font-bold text-slate-900">
                        Registrar Abono
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Registre un pago parcial o total sobre esta orden.
                    </p>

                </div>


                <form
                    method="POST"
                    action="{{ route('abonos.store', $pago) }}"
                >

                    @csrf


                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- MONTO --}}
                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Monto del abono *
                            </label>

                            <div class="relative">

                                <span class="absolute left-4 top-3 text-slate-500">
                                    Q
                                </span>

                                <input
                                    type="number"
                                    name="monto"
                                    value="{{ old('monto') }}"
                                    min="0.01"
                                    max="{{ $pago->saldo_pendiente }}"
                                    step="0.01"
                                    required
                                    class="w-full border border-slate-300
                                        rounded-lg pl-9 pr-4 py-3"
                                >

                            </div>

                            <p class="text-xs text-slate-400 mt-2">
                                Máximo disponible:
                                Q {{ number_format($pago->saldo_pendiente, 2) }}
                            </p>

                        </div>


                        {{-- MÉTODO --}}
                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Método de pago *
                            </label>

                            <select
                                name="metodo_pago"
                                required
                                class="w-full border border-slate-300
                                    rounded-lg px-4 py-3 bg-white"
                            >

                                <option value="Efectivo">
                                    Efectivo
                                </option>

                                <option value="Transferencia">
                                    Transferencia
                                </option>

                                <option value="Depósito">
                                    Depósito
                                </option>

                                <option value="Otro">
                                    Otro
                                </option>

                            </select>

                        </div>

                        {{-- FECHA --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Fecha del abono *
                            </label>

                            <input
                                type="date"
                                name="fecha_abono"
                                value="{{ old('fecha_abono', now()->toDateString()) }}"
                                required
                                class="w-full border border-slate-300
                                    rounded-lg px-4 py-3"
                            >

                        </div>

                        {{-- REFERENCIA --}}
                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                    Referencia / No. de boleta
                            </label>

                            <input
                                type="text"
                                name="referencia"
                                value="{{ old('referencia') }}"
                                placeholder="No. de transferencia"
                                class="w-full border border-slate-300
                                    rounded-lg px-4 py-3"
                            >

                        </div>


                        {{-- OBSERVACIONES --}}
                        <div class="md:col-span-2">

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Observaciones
                            </label>

                            <textarea
                                name="observaciones"
                                rows="3"
                                class="w-full border border-slate-300
                                    rounded-lg px-4 py-3 resize-none"
                                placeholder="Información adicional..."
                            >{{ old('observaciones') }}</textarea>

                        </div>

                    </div>


                    <div class="px-6 py-5 bg-slate-50
                                border-t border-slate-200
                                flex justify-end">

                        <button
                            type="submit"
                            class="px-6 py-3 bg-emerald-700
                                hover:bg-emerald-800
                                text-white rounded-lg font-semibold"
                        >
                            Registrar Abono
                        </button>

                    </div>

                </form>

            </section>

        @else

            <div class="mb-8 bg-emerald-50 border border-emerald-200
                        rounded-xl px-6 py-5">

                <p class="font-bold text-emerald-700">
                    ✓ Esta orden se encuentra completamente pagada.
                </p>

            </div>

        @endif
       
        {{-- HISTORIAL DE ABONOS --}}
    <section class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-200">

            <h2 class="text-lg font-bold text-slate-900">
                Historial de Abonos
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                    Pagos registrados sobre esta orden.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50 text-slate-500 uppercase text-xs">

                    <tr>
                        <th class="text-left px-6 py-4">Fecha</th>
                        <th class="text-left px-6 py-4">Método</th>
                        <th class="text-left px-6 py-4">Referencia</th>
                        <th class="text-left px-6 py-4">Monto</th>
                        <th class="text-left px-6 py-4">Registrado por</th>
                        <th class="text-left px-6 py-4">Observaciones</th>
                    </tr>

                </thead>

                <tbody class="divide-y divide-slate-100">

                        @forelse($pago->abonos as $abono)

                            <tr>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $abono->fecha_abono?->format('d/m/Y H:i') ?? '—' }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $abono->metodo_pago }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $abono->referencia ?? '—' }}
                                </td>

                                <td class="px-6 py-4 font-bold text-emerald-700">
                                    Q {{ number_format($abono->monto, 2) }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $abono->usuarioRegistro?->name ?? '—' }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ $abono->observaciones ?? '—' }}
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="px-6 py-10 text-center text-slate-400"
                                >
                                    Todavía no hay abonos registrados.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>

    </div>

@endsection