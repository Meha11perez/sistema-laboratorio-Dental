@extends('layouts.app')

@section('title', 'Detalle de Orden | Laboratorio Dental')

@section('content')

    <div class="max-w-6xl mx-auto">

        {{-- ENCABEZADO --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

            <div>
                <p class="text-sm font-semibold text-blue-800">
                    ORDEN DE TRABAJO
                </p>

                <h1 class="text-3xl font-bold text-slate-900">
                    {{ $orden->codigo }}
                </h1>

                <p class="text-slate-500 mt-1">
                    Consulta general del trabajo registrado.
                </p>
            </div>

            <div class="flex gap-3">

                <a
                    href="{{ route('ordenes.index') }}"
                    class="px-5 py-3 border border-slate-300 rounded-lg
                        text-slate-700 font-semibold hover:bg-white"
                >
                    ← Volver
                </a>

                @if($orden->estadoOrden?->nombre !== 'Cancelado')

                    <a
                        href="{{ route('ordenes.edit', $orden) }}"
                        class="px-5 py-3 bg-blue-800 hover:bg-blue-900
                            text-white rounded-lg font-semibold"
                    >
                        Editar Orden
                    </a>

                @endif

            </div>

        </div>


        {{-- MENSAJE DE ÉXITO --}}
        @if (session('success'))

            <div class="mb-6 bg-emerald-50 border border-emerald-200
                        text-emerald-700 px-5 py-4 rounded-xl">
                {{ session('success') }}
            </div>

        @endif


        {{-- RESUMEN --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">

            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">

                <p class="text-xs uppercase text-slate-400 font-semibold">
                    Caja
                </p>

                <p class="text-xl font-bold mt-2">
                    {{ $orden->codigo_caja ?? '—' }}
                </p>

            </div>


            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">

                <p class="text-xs uppercase text-slate-400 font-semibold">
                    Estado
                </p>

                <p class="mt-2">

                    <span class="inline-flex px-3 py-1 rounded-full
                                bg-blue-50 text-blue-800 text-sm font-semibold">

                        {{ $orden->estadoOrden?->nombre ?? 'Sin estado' }}

                    </span>

                </p>

            </div>


            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">

                <p class="text-xs uppercase text-slate-400 font-semibold">
                    Prioridad
                </p>

                <p class="mt-2">

                    @if($orden->prioridad === 'Urgente')

                        <span class="inline-flex px-3 py-1 rounded-full
                                    bg-red-50 text-red-700 text-sm font-semibold">
                            Urgente
                        </span>

                    @else

                        <span class="inline-flex px-3 py-1 rounded-full
                                    bg-emerald-50 text-emerald-700 text-sm font-semibold">
                            Normal
                        </span>

                    @endif

                </p>

            </div>


            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">

                <p class="text-xs uppercase text-slate-400 font-semibold">
                    Total
                </p>

                <p class="text-xl font-bold mt-2">
                    Q {{ number_format($orden->total ?? 0, 2) }}
                </p>

            </div>

        </div>


        {{-- INFORMACIÓN --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- DATOS DEL CLIENTE --}}
            <section class="lg:col-span-2 bg-white border border-slate-200 rounded-xl shadow-sm">

                <div class="px-6 py-5 border-b border-slate-200">

                    <h2 class="text-lg font-bold">
                        Información de la orden
                    </h2>

                </div>


                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <p class="text-xs uppercase text-slate-400 font-semibold">
                            Odontólogo
                        </p>

                        <p class="mt-1 font-semibold text-slate-800">
                            {{ $orden->odontologo?->nombre ?? '—' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs uppercase text-slate-400 font-semibold">
                            Paciente
                        </p>

                        <p class="mt-1 font-semibold text-slate-800">
                            {{ $orden->paciente?->nombre }}
                            {{ $orden->paciente?->apellido }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs uppercase text-slate-400 font-semibold">
                            Tipo de prótesis
                        </p>

                        <p class="mt-1 font-semibold text-slate-800">
                            {{ $orden->tipoProtesis?->nombre ?? '—' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs uppercase text-slate-400 font-semibold">
                            Cantidad
                        </p>

                        <p class="mt-1 font-semibold text-slate-800">
                            {{ $orden->cantidad }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs uppercase text-slate-400 font-semibold">
                            Color
                        </p>

                        <p class="mt-1 font-semibold text-slate-800">
                            {{ $orden->color ?? '—' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs uppercase text-slate-400 font-semibold">
                            Técnico actual
                        </p>

                        <p class="mt-1 font-semibold text-slate-800">
                            {{ $orden->tecnicoActual?->user?->name ?? 'Sin asignar' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs uppercase text-slate-400 font-semibold">
                            Etapa actual
                        </p>

                        <p class="mt-1 font-semibold text-slate-800">
                            {{ $orden->etapaActual?->nombre ?? 'Sin asignar' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs uppercase text-slate-400 font-semibold">
                            Registrado por
                        </p>

                        <p class="mt-1 font-semibold text-slate-800">
                            {{ $orden->usuarioRegistro?->name ?? '—' }}
                        </p>
                    </div>

                </div>


                {{-- ESPECIFICACIONES --}}
                <div class="px-6 pb-6">

                    <p class="text-xs uppercase text-slate-400 font-semibold">
                        Especificaciones
                    </p>

                    <div class="mt-2 bg-slate-50 border border-slate-200 rounded-lg p-4 text-slate-700">
                        {{ $orden->especificaciones }}
                    </div>

                </div>


                {{-- OBSERVACIONES --}}
                <div class="px-6 pb-6">

                    <p class="text-xs uppercase text-slate-400 font-semibold">
                        Observaciones
                    </p>

                    <div class="mt-2 bg-slate-50 border border-slate-200 rounded-lg p-4 text-slate-700">
                        {{ $orden->observaciones ?? 'Sin observaciones.' }}
                    </div>

                </div>

            </section>


            {{-- FECHAS --}}
            <aside class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 h-fit">

                <h2 class="text-lg font-bold mb-6">
                    Fechas
                </h2>


                <div class="space-y-6">

                    <div>
                        <p class="text-xs uppercase text-slate-400 font-semibold">
                            Fecha de ingreso
                        </p>

                        <p class="mt-1 font-semibold">
                            {{ $orden->fecha_ingreso?->format('d/m/Y') ?? '—' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs uppercase text-slate-400 font-semibold">
                            Entrega estimada
                        </p>

                        <p class="mt-1 font-semibold">
                            {{ $orden->fecha_entrega_estimada?->format('d/m/Y') ?? '—' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs uppercase text-slate-400 font-semibold">
                            Entrega real
                        </p>

                        <p class="mt-1 font-semibold">
                            {{ $orden->fecha_entrega_real?->format('d/m/Y') ?? 'Pendiente' }}
                        </p>
                    </div>


                    <div class="pt-5 border-t border-slate-200">

                        <p class="text-xs uppercase text-slate-400 font-semibold">
                            Creada
                        </p>

                        <p class="mt-1 text-sm text-slate-600">
                            {{ $orden->created_at?->format('d/m/Y H:i') }}
                        </p>

                    </div>

                </div>

            </aside>

        </div>

    </div>

    {{-- HISTORIAL DE PRODUCCIÓN --}}
    <section class="mt-8 bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-200">

            <h2 class="text-lg font-bold text-slate-900">
                Historial de Producción
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Trazabilidad de etapas y técnicos asignados.
            </p>

        </div>


        <div class="p-6">

            @forelse ($orden->historialProduccion as $historial)

                <div class="relative pl-8 pb-8 last:pb-0">

                    {{-- LÍNEA --}}
                    @if (!$loop->last)
                        <div class="absolute left-[10px] top-5 bottom-0 w-px bg-slate-200"></div>
                    @endif


                    {{-- PUNTO --}}
                    <div
                        class="absolute left-0 top-1 w-5 h-5 rounded-full
                            bg-blue-800 border-4 border-blue-100"
                    >
                    </div>


                    {{-- CONTENIDO --}}
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">

                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">

                            <div>

                                <p class="font-bold text-slate-900">
                                    {{ $historial->etapaProduccion?->nombre ?? 'Etapa no disponible' }}
                                </p>

                                <p class="text-sm text-slate-500 mt-1">
                                    Técnico:
                                    <span class="font-medium text-slate-700">
                                        {{ $historial->tecnico?->user?->name ?? 'Sin asignar' }}
                                    </span>
                                </p>

                            </div>


                            <span
                                class="inline-flex w-fit px-3 py-1 rounded-full
                                    text-xs font-semibold
                                    {{ $historial->estado === 'Completado'
                                            ? 'bg-emerald-50 text-emerald-700'
                                            : 'bg-blue-50 text-blue-700' }}"
                            >
                                {{ $historial->estado }}
                            </span>

                        </div>


                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-5 text-sm">

                            <div>

                                <p class="text-xs uppercase text-slate-400 font-semibold">
                                    Inicio
                                </p>

                                <p class="mt-1 text-slate-700">
                                    {{ $historial->fecha_inicio?->format('d/m/Y H:i') }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs uppercase text-slate-400 font-semibold">
                                    Fin
                                </p>

                                <p class="mt-1 text-slate-700">
                                    {{ $historial->fecha_fin
                                        ? $historial->fecha_fin->format('d/m/Y H:i')
                                        : 'En proceso' }}
                                </p>

                            </div>

                        </div>


                        @if ($historial->observaciones)

                            <div class="mt-4 pt-4 border-t border-slate-200">

                                <p class="text-xs uppercase text-slate-400 font-semibold">
                                    Observaciones
                                </p>

                                <p class="text-sm text-slate-600 mt-1">
                                    {{ $historial->observaciones }}
                                </p>

                            </div>

                        @endif

                    </div>

                </div>

            @empty

                <div class="text-center py-10 text-slate-400">

                    No existen movimientos de producción registrados.

                </div>

            @endforelse

        </div>

            </section>
            {{-- HISTORIAL DE ESTADOS --}}
        <section class="mt-8 bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-200">

                <h2 class="text-lg font-bold text-slate-900">
                    Historial de Estados
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Registro de cambios realizados sobre la orden.
                </p>

            </div>


            <div class="p-6">

                @forelse ($orden->historialEstados as $historial)

                    <div class="flex gap-4 pb-6 last:pb-0">

                        <div class="flex flex-col items-center">

                            <div class="w-4 h-4 bg-blue-800 rounded-full"></div>

                            @if(!$loop->last)
                                <div class="w-px flex-1 bg-slate-200 mt-2"></div>
                            @endif

                        </div>


                        <div class="flex-1">

                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">

                                <div class="flex flex-col md:flex-row md:justify-between gap-2">

                                    <div>

                                        <p class="font-bold text-slate-900">
                                            {{ $historial->estadoOrden?->nombre }}
                                        </p>

                                        <p class="text-sm text-slate-500 mt-1">
                                            Registrado por:
                                            {{ $historial->usuarioRegistro?->name ?? '—' }}
                                        </p>

                                    </div>


                                    <p class="text-sm text-slate-500">
                                        {{ $historial->fecha?->format('d/m/Y H:i') }}
                                    </p>

                                </div>


                                @if($historial->motivo)

                                    <div class="mt-4">

                                        <p class="text-xs uppercase text-slate-400 font-semibold">
                                            Motivo
                                        </p>

                                        <p class="text-sm text-slate-700 mt-1">
                                            {{ $historial->motivo }}
                                        </p>

                                    </div>

                                @endif


                                @if($historial->observaciones)

                                    <div class="mt-4">

                                        <p class="text-xs uppercase text-slate-400 font-semibold">
                                            Observaciones
                                        </p>

                                        <p class="text-sm text-slate-700 mt-1">
                                            {{ $historial->observaciones }}
                                        </p>

                                    </div>

                                @endif

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="text-center py-8 text-slate-400">
                        No hay cambios de estado registrados.
                    </div>

                @endforelse

            </div>

        </section>


@endsection