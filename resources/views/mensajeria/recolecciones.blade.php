@extends('layouts.app')

@section('title', 'Recolecciones')

@section('content')

<div class="max-w-7xl mx-auto">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">
            Recolecciones
        </h1>

        <p class="text-slate-500 mt-1">
            Seguimiento de recolecciones realizadas por mensajería.
        </p>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-900">
                Registro de Recolecciones
            </h2>
        </div>

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                    <tr>
                        <th class="text-left px-6 py-4">Fecha</th>
                        <th class="text-left px-6 py-4">Ruta</th>
                        <th class="text-left px-6 py-4">Odontólogo</th>
                        <th class="text-left px-6 py-4">Clínica</th>
                        <th class="text-left px-6 py-4">Dirección</th>
                        <th class="text-left px-6 py-4">Estado</th>
                        <th class="text-left px-6 py-4">Hora</th>
                        <th class="text-right px-6 py-4">Acción</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse($recolecciones as $detalle)

                        <tr class="hover:bg-slate-50">

                            <td class="px-6 py-4">
                                {{ $detalle->rutaMensajeria?->fecha?->format('d/m/Y') ?? '—' }}
                            </td>

                            <td class="px-6 py-4 font-semibold">
                                Ruta #{{ $detalle->ruta_mensajeria_id }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $detalle->odontologo?->nombre ?? '—' }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $detalle->clinica?->nombre ?? '—' }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $detalle->direccion_referencia ?? '—' }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $detalle->estado }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $detalle->hora_realizada ?? '—' }}
                            </td>

                            <td class="px-6 py-4 text-right">

                                <a
                                    href="{{ route(
                                        'mensajeria.show',
                                        $detalle->ruta_mensajeria_id
                                    ) }}"
                                    class="text-blue-700 font-semibold hover:underline"
                                >
                                    Ver ruta
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="8"
                                class="px-6 py-12 text-center text-slate-400"
                            >
                                No hay recolecciones registradas.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection