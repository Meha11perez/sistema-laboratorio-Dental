@extends('layouts.app')

@section('title', 'Órdenes de Trabajo')

@section('content')

<div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">

    <div class="min-w-0">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
            Órdenes de Trabajo
        </h1>

        <p class="text-sm text-slate-500 mt-1">
            Gestión y seguimiento de los trabajos del laboratorio.
        </p>
    </div>

    @if(in_array(
        auth()->user()->role?->nombre,
        ['Administrador', 'Recepcion']
    ))
        <a
            href="{{ route('ordenes.create') }}"
            class="inline-flex w-full sm:w-auto shrink-0 items-center justify-center gap-2 px-5 py-3 rounded-lg bg-[#315875] text-white font-semibold text-sm hover:bg-[#182d47] shadow-sm transition-colors"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-4 h-4"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                aria-hidden="true"
            >
                <path d="M12 5v14M5 12h14" />
            </svg>

            Nueva Orden
        </a>
    @endif

</div>
{{-- BUSCADOR --}}
<div class="mb-5 bg-white border border-slate-200 rounded-xl p-4 shadow-sm">

    <form
        method="GET"
        action="{{ route('ordenes.index') }}"
        class="flex flex-col gap-3 sm:flex-row sm:items-end"
    >
        <div class="flex-1 min-w-0">
            <label
                for="buscar-ordenes"
                class="block mb-2 text-sm font-semibold text-slate-700"
            >
                Buscar órdenes
            </label>

            <div class="relative">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 pointer-events-none"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                    aria-hidden="true"
                >
                    <circle cx="10.5" cy="10.5" r="6.5" />
                    <path d="m16 16 5 5" />
                </svg>

                <input
                    id="buscar-ordenes"
                    type="search"
                    name="buscar"
                    value="{{ $buscar ?? '' }}"
                    maxlength="120"
                    placeholder="Código, paciente u odontólogo..."
                    class="w-full min-w-0 rounded-lg border border-slate-200 bg-white pl-10 pr-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#315875] focus:border-[#315875]"
                >
            </div>
        </div>

        <button
            type="submit"
            class="inline-flex items-center justify-center rounded-lg bg-[#315875] px-5 py-3 text-sm font-semibold text-white hover:bg-[#182d47] transition-colors">
            Buscar
        </button>

        @if (($buscar ?? '') !== '')
            <a
                href="{{ route('ordenes.index') }}"
                class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-[#315875] hover:bg-[#e7eef8] transition-colors">
                Limpiar
            </a>
        @endif
    </form>

    @error('buscar')
        <p class="mt-2 text-sm text-red-600" role="alert">
            {{ $message }}
        </p>
    @enderror

    @if (($buscar ?? '') !== '')
        <p class="mt-3 text-sm text-slate-500">
            Resultados para
            <span class="font-semibold text-slate-700">
                “{{ $buscar }}”
            </span>:
            {{ number_format($ordenes->total(), 0, '.', ',') }} órdenes.
        </p>
    @endif

</div>

<div class="w-full min-w-0 bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">

<div class="w-full overflow-x-auto" tabindex="0" role="region" aria-label="Listado de órdenes de trabajo">
        <table class="w-full text-sm">

            <thead class="bg-slate-50 text-slate-500 uppercase text-xs">

                <tr>
                <th class="text-left px-6 py-4">Código</th>
                <th class="text-left px-6 py-4">Área / Código</th>
                <th class="text-left px-6 py-4">Paciente</th>
                <th class="text-left px-6 py-4">Odontólogo</th>
                <th class="text-left px-6 py-4">Prótesis</th>
                <th class="text-left px-6 py-4">Etapa actual</th>
                <th class="text-left px-6 py-4">Técnico actual</th>
                <th class="text-left px-6 py-4">Tipo</th>
                <th class="text-left px-6 py-4">Estado</th>
                <th class="text-left px-6 py-4">Entrega</th>
                <th class="text-left px-6 py-4">Acciones</th>
            </tr>
                
            </thead>

            <tbody class="divide-y divide-slate-100">

                @forelse ($ordenes as $orden)

            <tr class="hover:bg-slate-50">

                {{-- CÓDIGO --}}
                <td class="px-6 py-4 whitespace-nowrap font-semibold text-[#315875]">
                    {{ $orden->codigo }}
                </td>

                {{-- ÁREA / CÓDIGO --}}
                <td class="px-6 py-4">

                    @php
                        $nombreArea = match($orden->area_trabajo) {
                            'removible' => 'Removibles',
                            'fija' => 'Fijas',
                            'cromo_cobalto' => 'Cromo Cobalto',
                            'ortodoncia' => 'Ortodoncia',
                            default => 'Sin área',
                        };
                    @endphp

                    <p class="font-semibold text-slate-800">
                        {{ $nombreArea }}
                    </p>

                    <p class="text-xs text-blue-700 font-semibold mt-1">
                        {{ $orden->codigo_area ?? '—' }}
                    </p>

                </td>

                {{-- PACIENTE --}}
                <td class="px-6 py-4">
                    {{ $orden->paciente?->nombre }}
                </td>

                {{-- ODONTÓLOGO --}}
                <td class="px-6 py-4">
                    {{ $orden->odontologo?->nombre }}
                </td>

                {{-- PRÓTESIS --}}
                <td class="px-6 py-4">
                    {{ $orden->tipoProtesis?->nombre }}
                </td>

                {{-- ETAPA ACTUAL --}}
                <td class="px-6 py-4">
                    {{ $orden->etapaActual?->nombre ?? 'Sin asignar' }}
                </td>

                {{-- TÉCNICO ACTUAL --}}
                <td class="px-6 py-4">
                    {{ $orden->tecnicoActual?->user?->name ?? 'Sin asignar' }}
                </td>

                {{-- TIPO DE ORDEN --}}
                <td class="px-6 py-4">

                    @if($orden->tipo_orden === 'Repeticion')

                        <span class="inline-flex items-center px-3 py-1
                                    rounded-full text-xs font-bold
                                    bg-amber-100 text-amber-700">
                            Repetición
                        </span>

                    @else

                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700">
                            Nueva
                        </span>

                    @endif

                </td>

                {{-- ESTADO --}}
                <td class="px-6 py-4">
                    <span class="{{ $orden->estadoOrden?->nombre === 'Entregado' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-50 text-blue-800' }}
                                inline-flex whitespace-nowrap px-3 py-1 rounded-full
                                text-xs font-semibold">
                        {{ $orden->estadoOrden?->nombre }}
                    </span>
                </td>

                {{-- FECHA DE ENTREGA --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($orden->estadoOrden?->nombre === 'Entregado')
                        <p class="font-semibold text-emerald-700">Entrega real</p>
                        <p>{{ $orden->fecha_entrega_real?->format('d/m/Y') ?? 'Fecha no registrada' }}</p>
                    @else
                        <p class="text-xs text-slate-500">Estimada</p>
                        <p>{{ $orden->fecha_entrega_estimada?->format('d/m/Y') ?? '—' }}</p>
                    @endif
                </td>

                {{-- ACCIONES --}}
                <td class="px-6 py-4 whitespace-nowrap">

                    <a
                        href="{{ route('ordenes.show', $orden) }}"
                        class="text-blue-700 font-semibold hover:underline"
                    >
                        Ver
                    </a>

                    @if(in_array(
                        auth()->user()->role?->nombre,
                        ['Administrador', 'Recepcion']
                    ))
                        <a
                            href="{{ route('ordenes.edit', $orden) }}"
                            class="ml-3 text-amber-600 font-semibold hover:underline"
                        >
                            Editar
                        </a>
                    @endif

                </td>
            </tr>

        @empty

          <tr>
            <td
                colspan="11"
                class="px-6 py-12 text-center text-slate-400">

                @if (($buscar ?? '') !== '')
                    No se encontraron órdenes que coincidan con la búsqueda.
                @else
                    No hay órdenes registradas.
                @endif
            </td>
        </tr>

        @endforelse

            </tbody>

        </table>

    </div>

        @if ($ordenes->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $ordenes->withQueryString()->onEachSide(1)->links() }}
            </div>
        @endif

    </div>

        @if (session('success'))

            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-xl">
                {{ session('success') }}
            </div>

        @endif
    @endsection