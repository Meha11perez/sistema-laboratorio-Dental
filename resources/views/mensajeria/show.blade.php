@extends('layouts.app')

@section('title', 'Detalle de Ruta de Mensajería')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 mb-8">

        <div>

            <a
                href="{{ route('mensajeria.index') }}"
                class="text-sm font-semibold text-blue-700 hover:underline"
            >
                ← Volver a Mensajería
            </a>

            <h1 class="text-3xl font-bold text-slate-900 mt-3">
                Ruta de Mensajería
            </h1>

            <p class="text-slate-500 mt-1">
                {{ $ruta->fecha?->format('d/m/Y') }}
            </p>

        </div>

        <div class="flex flex-wrap gap-3">

            {{-- ESTADO --}}
            @if($ruta->estado === 'Finalizada')

                <span class="inline-flex px-4 py-2 rounded-full
                             bg-emerald-100 text-emerald-700
                             font-bold text-sm">
                    Finalizada
                </span>

             @elseif($ruta->estado === 'En ruta')

                <span class="inline-flex px-4 py-2 rounded-full
                             bg-blue-100 text-blue-700
                             font-bold text-sm">
                    En ruta
                </span>

             @elseif($ruta->estado === 'Cancelada')

                <span class="inline-flex px-4 py-2 rounded-full
                             bg-red-100 text-red-700
                             font-bold text-sm">
                    Cancelada
                </span>

             @else

                <span class="inline-flex px-4 py-2 rounded-full
                             bg-amber-100 text-amber-700
                             font-bold text-sm">
                    Pendiente
                </span>

            @endif
            
            @if($ruta->estado === 'Pendiente')

                <form
                    method="POST"
                    action="{{ route('mensajeria.iniciar', $ruta) }}"
                >
                    @csrf
                    @method('PUT')

                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-800
                            hover:bg-blue-900 text-white
                            rounded-lg font-semibold text-sm"
                    >
                        Iniciar Ruta
                    </button>
                </form>

            @elseif($ruta->estado === 'En ruta')

                <form
                    method="POST"
                    action="{{ route('mensajeria.finalizar', $ruta) }}"
                >
                    @csrf
                    @method('PUT')

                    <button
                        type="submit"
                        class="px-4 py-2 bg-emerald-700
                            hover:bg-emerald-800 text-white
                            rounded-lg font-semibold text-sm"
                    >
                        Finalizar Ruta
                    </button>
                </form>

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


    @if(session('error'))

        <div class="mb-6 bg-red-50 border border-red-200
                    text-red-700 rounded-xl px-5 py-4">
            {{ session('error') }}
        </div>

    @endif


    {{-- INFORMACIÓN GENERAL --}}
    <section class="bg-white border border-slate-200
                    rounded-xl shadow-sm overflow-hidden mb-8">

        <div class="px-6 py-5 border-b border-slate-200">

            <h2 class="text-lg font-bold text-slate-900">
                Información de la Ruta
            </h2>

        </div>


        <div class="p-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

            <div>

                <p class="text-xs uppercase font-semibold text-slate-400">
                    Mensajero
                </p>

                <p class="font-bold text-slate-900 mt-1">
                    {{ $ruta->mensajero?->name ?? 'Sin asignar' }}
                </p>

            </div>

            <div>

                <p class="text-xs uppercase font-semibold text-slate-400">
                    Fecha
                </p>

                <p class="font-semibold text-slate-900 mt-1">
                    {{ $ruta->fecha?->format('d/m/Y') ?? '—' }}
                </p>

            </div>

            <div>

                <p class="text-xs uppercase font-semibold text-slate-400">
                    Hora de salida
                </p>

                <p class="font-semibold text-slate-900 mt-1">
                    {{ $ruta->hora_salida ?? '—' }}
                </p>

            </div>

            <div>

                <p class="text-xs uppercase font-semibold text-slate-400">
                    Hora de regreso
                </p>

                <p class="font-semibold text-slate-900 mt-1">
                    {{ $ruta->hora_regreso ?? '—' }}
                </p>

            </div>

        </div>

        @if($ruta->observaciones)

            <div class="px-6 pb-6">

                <div class="pt-5 border-t border-slate-100">

                    <p class="text-xs uppercase font-semibold text-slate-400">
                        Observaciones
                    </p>

                    <p class="text-slate-600 mt-2">
                        {{ $ruta->observaciones }}
                    </p>

                </div>

            </div>

        @endif

    </section>

    {{-- VISITAS --}}
    <section class="bg-white border border-slate-200
                    rounded-xl shadow-sm overflow-hidden">

        <div class="flex flex-col md:flex-row
                    md:items-center md:justify-between
                    gap-4 px-6 py-5
                    border-b border-slate-200">

            <div>

                <h2 class="text-lg font-bold text-slate-900">
                    Visitas de la Ruta
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Entregas y recolecciones programadas.
                </p>

            </div>
                 @if(
                        $ruta->estado !== 'Finalizada' &&
                        $ruta->estado !== 'Cancelada'
                    )
                <a
                    href="{{ route('mensajeria.detalles.create', $ruta) }}"
                        class="inline-flex items-center justify-center
                           px-4 py-2.5 bg-blue-800
                           hover:bg-blue-900 text-white
                           rounded-lg font-semibold text-sm"
                >
                    + Agregar Visita
                </a>

            @endif

        </div>

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50
                              text-slate-500 uppercase text-xs">

                    <tr>
                        <th class="text-left px-6 py-4">#</th>
                        <th class="text-left px-6 py-4">Tipo</th>
                        <th class="text-left px-6 py-4">Orden</th>
                        <th class="text-left px-6 py-4">Odontólogo</th>
                        <th class="text-left px-6 py-4">Clínica</th>
                        <th class="text-left px-6 py-4">Dirección</th>
                        <th class="text-left px-6 py-4">Estado</th>
                        <th class="text-left px-6 py-4">Hora</th>
                        <th class="text-right px-6 py-4">Acción</th>
                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse(
                        $ruta->detalles->sortBy('orden_visita')
                        as $detalle
                    )

                        <tr class="hover:bg-slate-50">

                            {{-- ORDEN VISITA --}}
                            <td class="px-6 py-4 font-bold text-slate-900">
                                {{ $detalle->orden_visita }}
                            </td>
                            {{-- TIPO --}}
                            <td class="px-6 py-4">

                                @if($detalle->tipo_movimiento === 'Entrega')

                                    <span class="bg-emerald-100 text-emerald-700
                                                 px-3 py-1 rounded-full
                                                 text-xs font-semibold">
                                        Entrega
                                    </span>

                                @else

                                    <span class="bg-blue-100 text-blue-700
                                                 px-3 py-1 rounded-full
                                                 text-xs font-semibold">
                                        Recolección
                                    </span>

                                @endif

                            </td>


                            {{-- ORDEN --}}
                            <td class="px-6 py-4">

                                @if($detalle->ordenTrabajo)

                                    <a
                                        href="{{ route(
                                            'ordenes.show',
                                            $detalle->ordenTrabajo
                                        ) }}"
                                        class="text-blue-700
                                               font-semibold hover:underline"
                                    >
                                        {{ $detalle->ordenTrabajo->codigo }}
                                    </a>

                                @else

                                    —

                                @endif

                            </td>


                            {{-- ODONTÓLOGO --}}
                            <td class="px-6 py-4">
                                {{ $detalle->odontologo?->nombre ?? '—' }}
                            </td>


                            {{-- CLÍNICA --}}
                            <td class="px-6 py-4">
                                {{ $detalle->clinica?->nombre ?? '—' }}
                            </td>


                            {{-- DIRECCIÓN --}}
                            <td class="px-6 py-4 text-slate-600">
                                {{ $detalle->direccion_referencia ?? '—' }}
                            </td>


                            {{-- ESTADO --}}
                            <td class="px-6 py-4">

                                @if($detalle->estado === 'Realizada')

                                    <span class="bg-emerald-100 text-emerald-700
                                                 px-3 py-1 rounded-full
                                                 text-xs font-semibold">
                                        Realizada
                                    </span>

                                @elseif($detalle->estado === 'No realizada')

                                    <span class="bg-red-100 text-red-700
                                                 px-3 py-1 rounded-full
                                                 text-xs font-semibold">
                                        No realizada
                                    </span>

                                @elseif($detalle->estado === 'Reprogramada')

                                    <span class="bg-purple-100 text-purple-700
                                                 px-3 py-1 rounded-full
                                                 text-xs font-semibold">
                                        Reprogramada
                                    </span>

                                @else

                                    <span class="bg-amber-100 text-amber-700
                                                 px-3 py-1 rounded-full
                                                 text-xs font-semibold">
                                        Pendiente
                                    </span>

                                @endif

                            </td>


                            {{-- HORA --}}
                            <td class="px-6 py-4">
                                {{ $detalle->hora_realizada ?? '—' }}
                            </td>
                          
                            {{-- ACCIÓN --}}
                            <td class="px-6 py-4 text-right">

                                @if(
                                    $ruta->estado === 'En ruta' &&
                                    $detalle->estado === 'Pendiente'
                                )

                                    <details class="relative">

                                        <summary
                                            class="inline-flex items-center justify-center
                                                px-4 py-2
                                                bg-blue-800 hover:bg-blue-900
                                                text-white rounded-lg
                                                font-semibold text-xs
                                                cursor-pointer"
                                        >
                                            Actualizar
                                        </summary>


                                        <div class="mt-3">

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'mensajeria.detalles.estado',
                                                    $detalle
                                                ) }}"
                                                class="min-w-[280px]
                                                    bg-slate-50
                                                    border border-slate-200
                                                    rounded-xl p-4
                                                    space-y-4"
                                            >

                                                @csrf
                                                @method('PUT')


                                                {{-- ESTADO --}}
                                                <div>

                                                    <label
                                                        class="block text-xs
                                                            font-semibold
                                                            text-slate-600 mb-1"
                                                    >
                                                        Estado
                                                    </label>

                                                    <select
                                                        name="estado"
                                                        required
                                                        class="w-full border
                                                            border-slate-300
                                                            rounded-lg
                                                            px-3 py-2
                                                            bg-white text-sm"
                                                    >

                                                        <option value="">
                                                            Seleccione...
                                                        </option>

                                                        <option value="Realizada">
                                                            Realizada
                                                        </option>

                                                        <option value="No realizada">
                                                            No realizada
                                                        </option>

                                                        <option value="Reprogramada">
                                                            Reprogramada
                                                        </option>

                                                    </select>

                                                </div>


                                                {{-- RECIBIDO POR --}}
                                                <div>

                                                    <label
                                                        class="block text-xs
                                                            font-semibold
                                                            text-slate-600 mb-1"
                                                    >
                                                        Recibido por
                                                    </label>

                                                    <input
                                                        type="text"
                                                        name="recibido_por"
                                                        placeholder="Nombre de quien recibió"
                                                        class="w-full border
                                                            border-slate-300
                                                            rounded-lg
                                                            px-3 py-2 text-sm"
                                                    >

                                                </div>
                                
                                                {{-- FIRMA --}} 
                                                    <div>

                                                        <label
                                                            class="block text-xs
                                                                font-semibold
                                                                text-slate-600 mb-1"
                                                        >
                                                            Firma de recibido
                                                        </label>

                                                        <canvas
                                                            id="firma-{{ $detalle->id }}"
                                                            width="280"
                                                            height="120"
                                                            class="w-full border border-slate-300
                                                                rounded-lg bg-white touch-none"
                                                        ></canvas>

                                                        <input
                                                            type="hidden"
                                                            name="firma_recibido"
                                                            id="firma-input-{{ $detalle->id }}"
                                                        >

                                                        <button
                                                            type="button"
                                                            onclick="limpiarFirma({{ $detalle->id }})"
                                                            class="mt-2 text-xs
                                                                text-red-600
                                                                font-semibold hover:underline"
                                                        >
                                                            Limpiar firma
                                                        </button>

                                                    </div>

                                                {{-- OBSERVACIONES --}}
                                                <div>

                                                    <label
                                                        class="block text-xs
                                                            font-semibold
                                                            text-slate-600 mb-1"
                                                    >
                                                        Observaciones
                                                    </label>

                                                    <textarea
                                                        name="observaciones"
                                                        rows="3"
                                                        placeholder="Motivo o detalle..."
                                                        class="w-full border
                                                            border-slate-300
                                                            rounded-lg
                                                            px-3 py-2
                                                            text-sm resize-none"
                                                    ></textarea>

                                                </div>


                                                <div class="flex justify-end">

                                                    <button
                                                        type="submit"
                                                        class="px-4 py-2
                                                            bg-blue-800
                                                            hover:bg-blue-900
                                                            text-white
                                                            rounded-lg
                                                            font-semibold text-xs"
                                                    >
                                                        Guardar
                                                    </button>

                                                </div>

                                            </form>

                                        </div>

                                    </details>
                                    
                                    @elseif($detalle->estado === 'Realizada')

                                <div class="flex flex-col items-end gap-2">

                                    <span class="text-emerald-700 font-semibold text-xs">
                                        Completada
                                    </span>

                                    @if(
                                        $detalle->tipo_movimiento === 'Entrega' &&
                                        (
                                            $detalle->recibido_por ||
                                            $detalle->firma_recibido
                                        )
                                    )

                                        <details class="text-left">

                                            <summary
                                                class="text-blue-700 font-semibold
                                                    text-xs hover:underline
                                                    cursor-pointer"
                                            >
                                                Ver comprobante
                                            </summary>

                                            <div
                                                class="mt-2 w-72 bg-white
                                                    border border-slate-200
                                                    rounded-xl shadow-lg p-4"
                                            >

                                                <p class="text-xs uppercase
                                                        font-semibold text-slate-400">
                                                    Recibido por
                                                </p>

                                                <p class="text-sm font-semibold
                                                        text-slate-800 mt-1">
                                                    {{ $detalle->recibido_por ?? '—' }}
                                                </p>


                                                <p class="text-xs uppercase
                                                        font-semibold text-slate-400
                                                        mt-4">
                                                    Hora de entrega
                                                </p>

                                                <p class="text-sm font-semibold
                                                        text-slate-800 mt-1">
                                                    {{ $detalle->hora_realizada ?? '—' }}
                                                </p>

                                                @if($detalle->firma_recibido)

                                                    <div class="mt-4">

                                                        <p class="text-xs uppercase
                                                                font-semibold text-slate-400
                                                                mb-2">
                                                            Firma de recibido
                                                        </p>

                                                        <img
                                                            src="{{ asset(
                                                                'storage/' .
                                                                $detalle->firma_recibido
                                                            ) }}"
                                                            alt="Firma de recibido"
                                                            class="w-full h-28
                                                                object-contain
                                                                border border-slate-200
                                                                rounded-lg bg-white p-2"
                                                        >

                                                    </div>

                                                @endif

                                            </div>

                                        </details>

                                    @endif

                                </div>

                                @elseif($detalle->estado === 'No realizada')

                                    <span class="text-red-700 font-semibold text-xs">
                                        No realizada
                                    </span>


                                @elseif($detalle->estado === 'Reprogramada')

                                <div class="flex flex-col items-end gap-2">

                                    <span class="text-purple-700 font-semibold text-xs">
                                        Reprogramada
                                    </span>

                                    <a
                                        href="{{ route(
                                            'mensajeria.detalles.reprogramar.form',
                                            $detalle
                                        ) }}"
                                        class="text-blue-700
                                            font-semibold text-xs
                                            hover:underline"
                                    >
                                        Reprogramar
                                    </a>

                                </div>

                                @else
                                    <span class="text-slate-400">
                                        —
                                    </span>
                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="9"
                                class="px-6 py-12 text-center
                                       text-slate-400"
                            >
                                Esta ruta todavía no tiene visitas registradas.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('canvas[id^="firma-"]').forEach(function(canvas) {

        const id = canvas.id.replace('firma-', '');
        const input = document.getElementById('firma-input-' + id);

        const ctx = canvas.getContext('2d');

        let dibujando = false;

        function obtenerPosicion(e) {

            const rect = canvas.getBoundingClientRect();

            const punto = e.touches
                ? e.touches[0]
                : e;

            return {
                x: punto.clientX - rect.left,
                y: punto.clientY - rect.top
            };
        }

        function iniciar(e) {

            dibujando = true;

            const pos = obtenerPosicion(e);

            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);

            e.preventDefault();
        }

        function dibujar(e) {

            if (!dibujando) {
                return;
            }

            const pos = obtenerPosicion(e);

            ctx.lineTo(pos.x, pos.y);

            ctx.lineWidth = 2;
            ctx.lineCap = 'round';

            ctx.stroke();

            e.preventDefault();
        }

        function terminar() {

            if (!dibujando) {
                return;
            }

            dibujando = false;

            input.value =
                canvas.toDataURL('image/png');
        }

        canvas.addEventListener('mousedown', iniciar);
        canvas.addEventListener('mousemove', dibujar);
        canvas.addEventListener('mouseup', terminar);
        canvas.addEventListener('mouseleave', terminar);

        canvas.addEventListener('touchstart', iniciar);
        canvas.addEventListener('touchmove', dibujar);
        canvas.addEventListener('touchend', terminar);

    });

});


    function limpiarFirma(id) {

        const canvas =
            document.getElementById('firma-' + id);

        const input =
            document.getElementById('firma-input-' + id);

        const ctx =
            canvas.getContext('2d');

        ctx.clearRect(
            0,
            0,
            canvas.width,
            canvas.height
        );

        input.value = '';
    }
    </script>
@endsection