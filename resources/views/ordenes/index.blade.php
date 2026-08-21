@extends('layouts.app')

@section('title', 'Órdenes de Trabajo')

@section('content')

<div class="flex items-center justify-between mb-8">

    <div>
        <h1 class="text-3xl font-bold text-slate-900">
            Órdenes de Trabajo
        </h1>

        <p class="text-slate-500 mt-1">
            Gestión y seguimiento de los trabajos del laboratorio.
        </p>
    </div>

    <a
        href="{{ route('ordenes.create') }}"
        class="bg-blue-800 hover:bg-blue-900 text-white px-5 py-3 rounded-lg font-semibold"
    >
        + Nueva Orden
    </a>

</div>


<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">

    <div class="overflow-x-auto">

        <table class="w-full text-sm">

            <thead class="bg-slate-50 text-slate-500 uppercase text-xs">

                <tr>
                    <th class="text-left px-6 py-4">Código</th>
                    <th class="text-left px-6 py-4">Caja</th>
                    <th class="text-left px-6 py-4">Paciente</th>
                    <th class="text-left px-6 py-4">Odontólogo</th>
                    <th class="text-left px-6 py-4">Prótesis</th>

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
                <td class="px-6 py-4 font-semibold text-blue-900">
                    {{ $orden->codigo }}
                </td>

                {{-- CAJA --}}
                <td class="px-6 py-4">
                    {{ $orden->codigo_caja ?? '—' }}
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

                {{-- TIPO DE ORDEN --}}
                <td class="px-6 py-4">

                    @if($orden->tipo_orden === 'Repeticion')

                        <span class="inline-flex items-center px-3 py-1
                                    rounded-full text-xs font-bold
                                    bg-amber-100 text-amber-700">
                            ↻ Repetición
                        </span>

                    @else

                        <span class="inline-flex items-center px-3 py-1
                                    rounded-full text-xs font-bold
                                    bg-blue-50 text-blue-700">
                            Nueva
                        </span>

                    @endif

                </td>

                {{-- ESTADO --}}
                <td class="px-6 py-4">
                    <span class="bg-blue-50 text-blue-800
                                px-3 py-1 rounded-full
                                text-xs font-semibold">
                        {{ $orden->estadoOrden?->nombre }}
                    </span>
                </td>

                {{-- FECHA DE ENTREGA --}}
                <td class="px-6 py-4">
                    {{ $orden->fecha_entrega_estimada?->format('d/m/Y') ?? '—' }}
                </td>

                {{-- ACCIONES --}}
                <td class="px-6 py-4">

                    <a
                        href="{{ route('ordenes.show', $orden) }}"
                        class="text-blue-700 font-semibold hover:underline"
                    >
                        Ver
                    </a>

                    <a
                        href="{{ route('ordenes.edit', $orden) }}"
                        class="ml-3 text-amber-700 font-semibold hover:underline"
                    >
                        Editar
                    </a>

                </td>

            </tr>

        @empty

            <tr>
                <td
                    colspan="9"
                    class="px-6 py-12 text-center text-slate-400"
                >
                    No hay órdenes registradas.
                </td>
            </tr>

        @endforelse

            </tbody>

        </table>

    </div>


        @if ($ordenes->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $ordenes->links() }}
            </div>
        @endif

    </div>

        @if (session('success'))

            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-xl">
                {{ session('success') }}
            </div>

        @endif
    @endsection