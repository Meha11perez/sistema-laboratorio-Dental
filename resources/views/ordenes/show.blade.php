@extends('layouts.app')

@section('title', 'Detalle de Orden | Laboratorio Dental')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- =========================================================
         ENCABEZADO
    ========================================================== --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 mb-8">

        <div>

            <a
                href="{{ route('ordenes.index') }}"
                class="text-sm font-semibold text-blue-700 hover:underline"
            >
                ← Volver a órdenes
            </a>

            <div class="flex flex-wrap items-center gap-3 mt-3">

                <h1 class="text-3xl font-bold text-slate-900">
                    {{ $orden->codigo }}
                </h1>

                {{-- TIPO DE ORDEN --}}
                @if($orden->tipo_orden === 'Repeticion')

                    <span class="inline-flex items-center px-3 py-1
                                 rounded-full bg-amber-100 text-amber-700
                                 text-xs font-bold">
                        ↻ Repetición
                    </span>

                @else

                    <span class="inline-flex items-center px-3 py-1
                                 rounded-full bg-blue-50 text-blue-700
                                 text-xs font-bold">
                        Nueva
                    </span>

                @endif

                {{-- PRIORIDAD --}}
                @if($orden->prioridad === 'Urgente')

                    <span class="inline-flex items-center px-3 py-1
                                 rounded-full bg-red-100 text-red-700
                                 text-xs font-bold">
                        ⚠ Urgente
                    </span>

                @else

                    <span class="inline-flex items-center px-3 py-1
                                 rounded-full bg-emerald-50 text-emerald-700
                                 text-xs font-bold">
                        Normal
                    </span>

                @endif

            </div>

            <p class="text-slate-500 mt-2">
                Detalle y seguimiento de la orden de trabajo.
            </p>

        </div>


        {{-- BOTONES --}}
        <div class="flex flex-wrap gap-3">
            @if($orden->estadoOrden?->nombre !== 'Cancelado')

                <a
                    href="{{ route('devoluciones.create', $orden) }}"
                    class="inline-flex items-center justify-center
                        px-5 py-3 border border-purple-300
                        text-purple-700 hover:bg-purple-50
                        rounded-lg font-semibold text-sm"
                > 
                    Registrar Devolución
                </a>

            @endif

            @if($orden->estadoOrden?->nombre !== 'Cancelado')

                <a
                    href="{{ route('ordenes.edit', $orden) }}"
                    class="inline-flex items-center justify-center
                           px-5 py-3 bg-blue-800 hover:bg-blue-900
                           text-white rounded-lg font-semibold text-sm"
                >
                    Editar Orden
                </a>

                <a
                    href="{{ route('ordenes.repetir', $orden) }}"
                    class="inline-flex items-center justify-center
                           px-5 py-3 border border-amber-500
                           text-amber-700 hover:bg-amber-50
                           rounded-lg font-semibold text-sm"
                >
                    ↻ Crear Repetición
                </a>

            @endif

            <a
                href="{{ route('ordenes.rotulo', $orden) }}"
                target="_blank"
                class="inline-flex items-center justify-center
                       px-5 py-3 border border-slate-300
                       text-slate-700 hover:bg-slate-50
                       rounded-lg font-semibold text-sm"
            >
                🖨 Imprimir Rótulo
            </a>

            <a
                href="{{ route('pagos.show', $orden) }}"
                class="inline-flex items-center justify-center
                    px-5 py-3 border border-emerald-600
                    text-emerald-700 hover:bg-emerald-50
                    rounded-lg font-semibold text-sm"
            >
                💰 Ver Pago
            </a>

            @if($orden->estadoOrden?->nombre !== 'Cancelado')

                <a
                    href="{{ route('ordenes.cancelar.confirmar', $orden) }}"
                    class="inline-flex items-center justify-center
                           px-5 py-3 border border-red-300
                           text-red-700 hover:bg-red-50
                           rounded-lg font-semibold text-sm"
                >
                    Cancelar Orden
                </a>

            @endif

        </div>

    </div>


    {{-- =========================================================
         MENSAJES
    ========================================================== --}}
    @if(session('success'))

        <div class="mb-6 bg-emerald-50 border border-emerald-200
                    text-emerald-700 rounded-xl px-5 py-4">
            {{ session('success') }}
        </div>

    @endif


    @if(session('error'))

        <div class="mb-6 bg-red-50 border border-red-200
                    text-red-700 rounded-xl px-5 py-4">
            {{ session('error') }}
        </div>

    @endif


    {{-- =========================================================
         ESTADO ACTUAL
    ========================================================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

        {{-- ESTADO --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">

            <p class="text-xs font-semibold text-slate-400 uppercase">
                Estado
            </p>

            <p class="text-xl font-bold text-slate-900 mt-2">
                {{ $orden->estadoOrden?->nombre ?? 'Sin estado' }}
            </p>

        </div>


        {{-- ETAPA --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">

            <p class="text-xs font-semibold text-slate-400 uppercase">
                Etapa actual
            </p>

            <p class="text-xl font-bold text-slate-900 mt-2">
                {{ $orden->etapaActual?->nombre ?? 'Sin asignar' }}
            </p>

        </div>


        {{-- TÉCNICO --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">

            <p class="text-xs font-semibold text-slate-400 uppercase">
                Técnico actual
            </p>

            <p class="text-xl font-bold text-slate-900 mt-2">
                {{ $orden->tecnicoActual?->user?->name ?? 'Sin asignar' }}
            </p>

        </div>


        {{-- FECHA ENTREGA --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">

            <p class="text-xs font-semibold text-slate-400 uppercase">
                Entrega estimada
            </p>

            <p class="text-xl font-bold text-slate-900 mt-2">
                {{ $orden->fecha_entrega_estimada?->format('d/m/Y') ?? 'Sin fecha' }}
            </p>

        </div>

    </div>


    {{-- =========================================================
         INFORMACIÓN GENERAL
    ========================================================== --}}
    <section class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-8">

        <div class="px-6 py-5 border-b border-slate-200">

            <h2 class="text-lg font-bold text-slate-900">
                Información General
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Datos principales relacionados con la orden.
            </p>

        </div>


        <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            {{-- CÓDIGO --}}
            <div>

                <p class="text-xs font-semibold text-slate-400 uppercase">
                    Número de orden
                </p>

                <p class="font-semibold text-slate-900 mt-1">
                    {{ $orden->codigo }}
                </p>

            </div>


            {{-- CAJA --}}
            <div>

                <p class="text-xs font-semibold text-slate-400 uppercase">
                    Código de caja
                </p>

                <p class="font-semibold text-slate-900 mt-1">
                    {{ $orden->codigo_caja ?? '—' }}
                </p>

            </div>


            {{-- TIPO --}}
            <div>

                <p class="text-xs font-semibold text-slate-400 uppercase">
                    Tipo de orden
                </p>

                <p class="font-semibold mt-1
                    {{ $orden->tipo_orden === 'Repeticion'
                        ? 'text-amber-700'
                        : 'text-blue-700' }}">
                    {{ $orden->tipo_orden === 'Repeticion'
                        ? '↻ Repetición'
                        : 'Nueva' }}
                </p>

            </div>


            {{-- ODONTÓLOGO --}}
            <div>

                <p class="text-xs font-semibold text-slate-400 uppercase">
                    Odontólogo
                </p>

                <p class="font-semibold text-slate-900 mt-1">
                    {{ $orden->odontologo?->nombre ?? '—' }}
                </p>

                @if($orden->odontologo?->clinica)

                    <p class="text-sm text-slate-500 mt-1">
                        {{ $orden->odontologo->clinica->nombre }}
                    </p>

                @endif

            </div>


            {{-- PACIENTE --}}
            <div>

                <p class="text-xs font-semibold text-slate-400 uppercase">
                    Paciente
                </p>

                <p class="font-semibold text-slate-900 mt-1">
                    {{ $orden->paciente?->nombre ?? '—' }}
                    {{ $orden->paciente?->apellido ?? '' }}
                </p>

            </div>


            {{-- PRÓTESIS --}}
            <div>

                <p class="text-xs font-semibold text-slate-400 uppercase">
                    Tipo de prótesis
                </p>

                <p class="font-semibold text-slate-900 mt-1">
                    {{ $orden->tipoProtesis?->nombre ?? '—' }}
                </p>

                @if($orden->tipoProtesis?->categoria)

                    <p class="text-sm text-slate-500 mt-1 capitalize">
                        {{ $orden->tipoProtesis->categoria }}
                    </p>

                @endif

            </div>


            {{-- FECHA INGRESO --}}
            <div>

                <p class="text-xs font-semibold text-slate-400 uppercase">
                    Fecha de ingreso
                </p>

                <p class="font-semibold text-slate-900 mt-1">
                    {{ $orden->fecha_ingreso?->format('d/m/Y') ?? '—' }}
                </p>

            </div>


            {{-- ENTREGA ESTIMADA --}}
            <div>

                <p class="text-xs font-semibold text-slate-400 uppercase">
                    Fecha de entrega estimada
                </p>

                <p class="font-semibold text-slate-900 mt-1">
                    {{ $orden->fecha_entrega_estimada?->format('d/m/Y') ?? '—' }}
                </p>

            </div>


            {{-- ENTREGA REAL --}}
            <div>

                <p class="text-xs font-semibold text-slate-400 uppercase">
                    Fecha de entrega real
                </p>

                <p class="font-semibold text-slate-900 mt-1">
                    {{ $orden->fecha_entrega_real?->format('d/m/Y') ?? '—' }}
                </p>

            </div>


            {{-- CANTIDAD --}}
            <div>

                <p class="text-xs font-semibold text-slate-400 uppercase">
                    Cantidad
                </p>

                <p class="font-semibold text-slate-900 mt-1">
                    {{ $orden->cantidad }}
                </p>

            </div>


            {{-- COLOR --}}
            <div>

                <p class="text-xs font-semibold text-slate-400 uppercase">
                    Color
                </p>

                <p class="font-semibold text-slate-900 mt-1">
                    {{ $orden->color ?? '—' }}
                </p>

            </div>


            {{-- TOTAL --}}
            <div>

                <p class="text-xs font-semibold text-slate-400 uppercase">
                    Monto total
                </p>

                <p class="font-semibold text-slate-900 mt-1">
                    Q {{ number_format($orden->total ?? 0, 2) }}
                </p>

            </div>


            {{-- REGISTRADO POR --}}
            <div>

                <p class="text-xs font-semibold text-slate-400 uppercase">
                    Registrado por
                </p>

                <p class="font-semibold text-slate-900 mt-1">
                    {{ $orden->usuarioRegistro?->name ?? '—' }}
                </p>

            </div>

        </div>

    </section>


    {{-- =========================================================
         ESPECIFICACIONES Y OBSERVACIONES
    ========================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        <section class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">

            <h2 class="font-bold text-slate-900">
                Especificaciones
            </h2>

            <p class="text-slate-600 mt-3 whitespace-pre-line">
                {{ $orden->especificaciones ?? 'Sin especificaciones registradas.' }}
            </p>

        </section>


        <section class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">

            <h2 class="font-bold text-slate-900">
                Observaciones
            </h2>

            <p class="text-slate-600 mt-3 whitespace-pre-line">
                {{ $orden->observaciones ?? 'Sin observaciones registradas.' }}
            </p>

        </section>

    </div>


    {{-- =========================================================
         RELACIÓN DE ORDEN / REPETICIONES
    ========================================================== --}}
    <section class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-8">

        <div class="px-6 py-5 border-b border-slate-200">

            <h2 class="text-lg font-bold text-slate-900">
                Relación de la Orden
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Orden original y repeticiones relacionadas.
            </p>

        </div>


        <div class="p-6">

            {{-- ESTA ORDEN ES REPETICIÓN --}}
            @if($orden->tipo_orden === 'Repeticion')

                <div class="bg-amber-50 border border-amber-200
                            rounded-xl p-5">

                    <div class="flex flex-col sm:flex-row
                                sm:items-center sm:justify-between gap-4">

                        <div>

                            <span class="inline-flex items-center
                                         bg-amber-100 text-amber-700
                                         px-3 py-1 rounded-full
                                         text-xs font-bold">
                                ↻ Repetición
                            </span>

                            <p class="text-sm text-slate-500 mt-3">
                                Esta orden fue creada como repetición de:
                            </p>

                            @if($orden->ordenOrigen)

                                <p class="text-xl font-bold text-slate-900 mt-1">
                                    {{ $orden->ordenOrigen->codigo }}
                                </p>

                            @else

                                <p class="text-red-600 mt-1">
                                    No se encontró la orden original.
                                </p>

                            @endif

                        </div>


                        @if($orden->ordenOrigen)

                            <a
                                href="{{ route('ordenes.show', $orden->ordenOrigen) }}"
                                class="inline-flex items-center justify-center
                                       px-4 py-2.5 bg-white
                                       border border-amber-300
                                       text-amber-700 rounded-lg
                                       font-semibold text-sm
                                       hover:bg-amber-100"
                            >
                                Ver orden original
                            </a>

                        @endif

                    </div>

                </div>

            @endif


            {{-- REPETICIONES ASOCIADAS --}}
            @if($orden->repeticiones->isNotEmpty())

                <div class="{{ $orden->tipo_orden === 'Repeticion' ? 'mt-6' : '' }}">

                    <div class="mb-4">

                        <p class="font-bold text-slate-900">
                            Repeticiones asociadas
                        </p>

                        <p class="text-sm text-slate-500 mt-1">
                            Total:
                            {{ $orden->repeticiones->count() }}
                        </p>

                    </div>


                    <div class="space-y-3">

                        @foreach($orden->repeticiones as $repeticion)

                            <div class="flex flex-col sm:flex-row
                                        sm:items-center sm:justify-between
                                        gap-4 border border-slate-200
                                        rounded-xl px-5 py-4">

                                <div>

                                    <div class="flex items-center gap-3">

                                        <p class="font-bold text-slate-900">
                                            {{ $repeticion->codigo }}
                                        </p>

                                        <span class="bg-amber-100
                                                     text-amber-700
                                                     px-2.5 py-1
                                                     rounded-full
                                                     text-xs font-semibold">
                                            ↻ Repetición
                                        </span>

                                    </div>

                                    <p class="text-sm text-slate-500 mt-2">
                                        {{ $repeticion->created_at?->format('d/m/Y H:i') }}
                                    </p>

                                </div>


                                <a
                                    href="{{ route('ordenes.show', $repeticion) }}"
                                    class="text-blue-700 font-semibold
                                           text-sm hover:underline"
                                >
                                    Ver repetición →
                                </a>

                            </div>

                        @endforeach

                    </div>

                </div>

            @endif


            {{-- SIN REPETICIONES --}}
            @if(
                $orden->tipo_orden !== 'Repeticion'
                && $orden->repeticiones->isEmpty()
            )

                <div class="text-center py-5">

                    <p class="text-slate-400">
                        Esta orden no tiene repeticiones registradas.
                    </p>

                </div>

            @endif

        </div>

    </section>
        {{-- =========================================================
                GARANTÍA
            ========================================================== --}}
            <section class="bg-white border border-slate-200
                            rounded-xl shadow-sm overflow-hidden mb-8">

                <div class="px-6 py-5 border-b border-slate-200">

                    <h2 class="text-lg font-bold text-slate-900">
                        Garantía
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Información de garantía asociada a esta orden.
                    </p>

                </div>


                <div class="p-6">

                    @if($orden->garantia)

                        @php
                            $hoy = now()->startOfDay();
                            $vencimiento = $orden->garantia->fecha_vencimiento?->startOfDay();

                            if (!$vencimiento) {
                                $estadoGarantia = 'Sin fecha';
                            } elseif ($vencimiento->lt($hoy)) {
                                $estadoGarantia = 'Vencida';
                            } elseif ($vencimiento->diffInDays($hoy) <= 15) {
                                $estadoGarantia = 'Por vencer';
                            } else {
                                $estadoGarantia = 'Vigente';
                            }
                        @endphp

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase">
                                    Inicio
                                </p>

                                <p class="font-semibold text-slate-900 mt-1">
                                    {{ $orden->garantia->fecha_inicio?->format('d/m/Y') ?? '—' }}
                                </p>
                            </div>


                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase">
                                    Vencimiento
                                </p>

                                <p class="font-semibold text-slate-900 mt-1">
                                    {{ $orden->garantia->fecha_vencimiento?->format('d/m/Y') ?? '—' }}
                                </p>
                            </div>


                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase">
                                    Estado
                                </p>

                                <div class="mt-2">

                                    @if($estadoGarantia === 'Vigente')

                                        <span class="bg-emerald-100 text-emerald-700
                                                    px-3 py-1 rounded-full text-xs font-bold">
                                            Vigente
                                        </span>

                                    @elseif($estadoGarantia === 'Por vencer')

                                        <span class="bg-amber-100 text-amber-700
                                                    px-3 py-1 rounded-full text-xs font-bold">
                                            Por vencer
                                        </span>

                                    @elseif($estadoGarantia === 'Vencida')

                                        <span class="bg-red-100 text-red-700
                                                    px-3 py-1 rounded-full text-xs font-bold">
                                            Vencida
                                        </span>

                                    @else

                                        <span class="bg-slate-100 text-slate-600
                                                    px-3 py-1 rounded-full text-xs font-bold">
                                            Sin fecha
                                        </span>

                                    @endif

                                </div>
                            </div>

                        </div>


                        @if($orden->garantia->observaciones)

                            <div class="mt-6 pt-5 border-t border-slate-100">

                                <p class="text-xs font-semibold text-slate-400 uppercase">
                                    Observaciones
                                </p>

                                <p class="text-slate-600 mt-2">
                                    {{ $orden->garantia->observaciones }}
                                </p>

                            </div>

                        @endif

                    @else

                        <div class="text-center py-6">

                            <p class="text-slate-400">
                                Esta orden no tiene una garantía registrada.
                            </p>

                        </div>

                    @endif

                </div>

            </section>

    {{-- =========================================================
     DEVOLUCIONES Y GARANTÍAS
    ========================================================== --}}
    <section class="bg-white border border-slate-200
                    rounded-xl shadow-sm overflow-hidden mb-8">

        <div class="flex flex-col md:flex-row md:items-center
                    md:justify-between gap-4
                    px-6 py-5 border-b border-slate-200">

            <div>
                <h2 class="text-lg font-bold text-slate-900">
                    Devoluciones y Garantías
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Historial de incidencias registradas para esta orden.
                </p>
            </div>

            @if($orden->estadoOrden?->nombre !== 'Cancelado')

                <a
                    href="{{ route('devoluciones.create', $orden) }}"
                    class="inline-flex items-center justify-center
                        px-4 py-2.5 border border-purple-300
                        text-purple-700 hover:bg-purple-50
                        rounded-lg font-semibold text-sm"
                >
                    + Registrar Devolución
                </a>

            @endif

        </div>


        <div class="p-6">

            @forelse($orden->devoluciones as $devolucion)

                <div class="border border-slate-200 rounded-xl p-5
                            {{ !$loop->last ? 'mb-4' : '' }}">

                    <div class="flex flex-col lg:flex-row
                                lg:items-start lg:justify-between gap-5">

                        {{-- INFORMACIÓN --}}
                        <div class="flex-1">

                            <div class="flex flex-wrap items-center gap-2 mb-4">

                                @if($devolucion->tipo === 'Garantia')

                                    <span class="bg-emerald-100 text-emerald-700
                                                px-3 py-1 rounded-full
                                                text-xs font-bold">
                                        Garantía
                                    </span>

                                @else

                                    <span class="bg-purple-100 text-purple-700
                                                px-3 py-1 rounded-full
                                                text-xs font-bold">
                                        Devolución
                                    </span>

                                @endif


                                <span class="bg-slate-100 text-slate-700
                                            px-3 py-1 rounded-full
                                            text-xs font-semibold">
                                    {{ $devolucion->estado }}
                                </span>


                                @if($devolucion->requiere_repeticion)

                                    <span class="bg-amber-100 text-amber-700
                                                px-3 py-1 rounded-full
                                                text-xs font-bold">
                                        ↻ Requiere repetición
                                    </span>

                                @endif

                            </div>


                            <div class="grid grid-cols-1 md:grid-cols-2
                                        xl:grid-cols-3 gap-5">

                                {{-- FECHA --}}
                                <div>
                                    <p class="text-xs uppercase font-semibold text-slate-400">
                                        Fecha
                                    </p>

                                    <p class="font-semibold text-slate-900 mt-1">
                                        {{ $devolucion->fecha_devolucion?->format('d/m/Y') ?? '—' }}
                                    </p>
                                </div>


                                {{-- MOTIVO --}}
                                <div>
                                    <p class="text-xs uppercase font-semibold text-slate-400">
                                        Motivo
                                    </p>

                                    <p class="font-semibold text-slate-900 mt-1">
                                        {{ $devolucion->motivo }}
                                    </p>
                                </div>


                                {{-- TÉCNICO --}}
                                <div>
                                    <p class="text-xs uppercase font-semibold text-slate-400">
                                        Técnico responsable
                                    </p>

                                    <p class="font-semibold text-slate-900 mt-1">
                                        {{ $devolucion->tecnicoResponsable?->user?->name ?? 'Sin asignar' }}
                                    </p>
                                </div>


                                {{-- PÉRDIDA --}}
                                <div>
                                    <p class="text-xs uppercase font-semibold text-slate-400">
                                        Pérdida estimada
                                    </p>

                                    <p class="font-semibold text-slate-900 mt-1">
                                        Q {{ number_format($devolucion->perdida_estimada ?? 0, 2) }}
                                    </p>
                                </div>


                                {{-- REGISTRADO POR --}}
                                <div>
                                    <p class="text-xs uppercase font-semibold text-slate-400">
                                        Registrado por
                                    </p>

                                    <p class="font-semibold text-slate-900 mt-1">
                                        {{ $devolucion->usuarioRegistro?->name ?? '—' }}
                                    </p>
                                </div>


                                {{-- GARANTÍA --}}
                                <div>
                                    <p class="text-xs uppercase font-semibold text-slate-400">
                                        Garantía asociada
                                    </p>

                                    <p class="font-semibold text-slate-900 mt-1">
                                        {{ $devolucion->garantia
                                            ? 'Sí'
                                            : 'No' }}
                                    </p>
                                </div>

                            </div>


                            @if($devolucion->observaciones)

                                <div class="mt-5 pt-4 border-t border-slate-100">

                                    <p class="text-xs uppercase font-semibold text-slate-400">
                                        Observaciones
                                    </p>

                                    <p class="text-slate-600 mt-2">
                                        {{ $devolucion->observaciones }}
                                    </p>

                                </div>

                            @endif

                        </div>


                        {{-- ACCIÓN --}}
                        @if($devolucion->requiere_repeticion)

                            <div class="lg:shrink-0">

                                @if($devolucion->repeticion)

                                    {{-- YA EXISTE UNA REPETICIÓN --}}
                                    <div class="flex flex-col gap-2">

                                        <span
                                            class="inline-flex items-center justify-center
                                                px-4 py-2
                                                bg-emerald-50 text-emerald-700
                                                border border-emerald-200
                                                rounded-lg font-semibold text-sm"
                                        >
                                            ✓ Repetición creada
                                        </span>

                                        <a
                                            href="{{ route(
                                                'ordenes.show',
                                                $devolucion->repeticion
                                            ) }}"
                                            class="text-center text-blue-700
                                                hover:underline font-semibold text-sm"
                                        >
                                            Ver {{ $devolucion->repeticion->codigo }} →
                                        </a>

                                    </div>

                                @elseif($orden->estadoOrden?->nombre !== 'Cancelado')

                                    {{-- TODAVÍA NO EXISTE REPETICIÓN --}}
                                    <a
                                        href="{{ route('ordenes.repetir', [
                                            'orden' => $orden,
                                            'devolucion' => $devolucion->id
                                        ]) }}"
                                        class="inline-flex items-center justify-center
                                            px-4 py-2.5 bg-amber-600
                                            hover:bg-amber-700 text-white
                                            rounded-lg font-semibold text-sm"
                                    >
                                        ↻ Crear Repetición
                                    </a>

                                @endif

                            </div>

                        @endif
                    </div>

                </div>

            @empty

                <div class="text-center py-8">

                    <p class="text-slate-400">
                        No hay devoluciones o garantías registradas.
                    </p>

                </div>

            @endforelse

        </div>

    </section>

    {{-- =========================================================
         HISTORIAL DE PRODUCCIÓN
    ========================================================== --}}
    <section class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-8">

        <div class="px-6 py-5 border-b border-slate-200">

            <h2 class="text-lg font-bold text-slate-900">
                Historial de Producción
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Etapas y técnicos involucrados en la producción.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50 text-slate-500 uppercase text-xs">

                    <tr>
                        <th class="text-left px-6 py-4">Etapa</th>
                        <th class="text-left px-6 py-4">Técnico</th>
                        <th class="text-left px-6 py-4">Inicio</th>
                        <th class="text-left px-6 py-4">Fin</th>
                        <th class="text-left px-6 py-4">Estado</th>
                        <th class="text-left px-6 py-4">Observaciones</th>
                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($orden->historialProduccion as $historial)

                        <tr>

                            <td class="px-6 py-4 font-semibold">
                                {{ $historial->etapaProduccion?->nombre ?? '—' }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $historial->tecnico?->user?->name ?? 'Sin asignar' }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $historial->fecha_inicio?->format('d/m/Y H:i') ?? '—' }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $historial->fecha_fin?->format('d/m/Y H:i') ?? '—' }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $historial->estado }}
                            </td>

                            <td class="px-6 py-4 text-slate-500">
                                {{ $historial->observaciones ?? '—' }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="px-6 py-10 text-center text-slate-400"
                            >
                                No hay historial de producción registrado.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>


    {{-- =========================================================
         HISTORIAL DE ESTADOS
    ========================================================== --}}
    <section class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-200">

            <h2 class="text-lg font-bold text-slate-900">
                Historial de Estados
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Cambios realizados sobre el estado de la orden.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50 text-slate-500 uppercase text-xs">

                    <tr>
                        <th class="text-left px-6 py-4">Estado</th>
                        <th class="text-left px-6 py-4">Fecha</th>
                        <th class="text-left px-6 py-4">Usuario</th>
                        <th class="text-left px-6 py-4">Motivo</th>
                        <th class="text-left px-6 py-4">Observaciones</th>
                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($orden->historialEstados as $historial)

                        <tr>

                            <td class="px-6 py-4 font-semibold">
                                {{ $historial->estadoOrden?->nombre ?? '—' }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $historial->fecha?->format('d/m/Y H:i') ?? '—' }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $historial->usuarioRegistro?->name ?? '—' }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $historial->motivo ?? '—' }}
                            </td>

                            <td class="px-6 py-4 text-slate-500">
                                {{ $historial->observaciones ?? '—' }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="px-6 py-10 text-center text-slate-400"
                            >
                                No hay cambios de estado registrados.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>
</div>
@endsection