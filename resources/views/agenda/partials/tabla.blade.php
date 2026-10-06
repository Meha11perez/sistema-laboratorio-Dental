<div class="overflow-x-auto">

    <table class="w-full text-sm">

        <thead class="bg-white text-slate-500 text-xs uppercase">

            <tr>
                <th class="text-left px-5 py-3">F. Ingreso</th>
                <th class="text-left px-5 py-3">Área / Código</th>
                <th class="text-left px-5 py-3">Doctor / Clínica</th>
                <th class="text-left px-5 py-3">Paciente</th>
                <th class="text-left px-5 py-3">Trabajo</th>
                <th class="text-left px-5 py-3">Técnico</th>
                <th class="text-left px-5 py-3">Prioridad</th>
                <th class="text-left px-5 py-3">Estado</th>
                <th class="text-left px-5 py-3">F. Salida</th>
                <th class="text-left px-5 py-3">Acciones</th>
            </tr>

        </thead>

        <tbody class="divide-y divide-slate-100">

            @forelse($ordenesGrupo as $orden)

                <tr class="hover:bg-slate-50 transition">

                    {{-- FECHA INGRESO --}}
                    <td class="px-5 py-4 whitespace-nowrap">

                        {{ $orden->fecha_ingreso?->format('d/m/Y') ?? '—' }}

                    </td>


                   {{-- ÁREA / CÓDIGO --}}
                    <td class="px-5 py-4 whitespace-nowrap">

                        @php
                            $nombreArea = match($orden->area_trabajo) {
                                'removible' => 'PR',
                                'fija' => 'PF',
                                'cromo_cobalto' => 'CC',
                                'ortodoncia' => 'AO',
                                default => '—',
                            };
                        @endphp

                        <div class="font-semibold text-slate-800">
                            {{ $nombreArea }}
                        </div>

                        <div class="text-xs text-slate-400 mt-1">
                            {{ $orden->codigo_area ?? 'Sin código' }}
                        </div>

                    </td>

                    {{-- ODONTÓLOGO / CLÍNICA --}}
                    <td class="px-5 py-4">

                        <div class="font-medium text-slate-800">

                            {{ $orden->odontologo?->nombre ?? '—' }}

                        </div>

                        @if($orden->odontologo?->clinica)

                            <div class="text-xs text-slate-400 mt-1">

                                {{ $orden->odontologo->clinica->nombre }}

                            </div>

                        @endif

                    </td>


                    {{-- PACIENTE --}}
                    <td class="px-5 py-4">

                        <span class="font-medium text-slate-700">

                            {{ $orden->paciente?->nombre ?? '—' }}

                            {{ $orden->paciente?->apellido ?? '' }}

                        </span>

                    </td>


                    {{-- TIPO DE TRABAJO --}}
                    <td class="px-5 py-4">

                        <div class="font-medium text-slate-800">

                            {{ $orden->tipoProtesis?->nombre ?? '—' }}

                        </div>

                        @if($orden->especificaciones)

                            <div
                                class="text-xs text-slate-500 mt-1 max-w-xs"
                                title="{{ $orden->especificaciones }}"
                            >

                                {{ \Illuminate\Support\Str::limit(
                                    $orden->especificaciones,
                                    60
                                ) }}

                            </div>

                        @endif

                    </td>


                    {{-- TÉCNICO ACTUAL --}}
                    <td class="px-5 py-4">

                        @if($orden->tecnicoActual)

                            <span
                                class="inline-flex items-center
                                       px-2.5 py-1
                                       bg-slate-100
                                       text-slate-700
                                       rounded-lg
                                       text-xs font-semibold"
                            >

                                {{ $orden->tecnicoActual?->user?->name
                                    ?? 'Técnico asignado' }}

                            </span>

                        @else

                            <span
                                class="inline-flex items-center
                                       px-2.5 py-1
                                       bg-amber-50
                                       text-amber-700
                                       rounded-lg
                                       text-xs font-semibold"
                            >

                                Sin asignar

                            </span>

                        @endif

                    </td>


                    {{-- PRIORIDAD --}}
                    <td class="px-5 py-4">

                        @if($orden->prioridad === 'Urgente')

                            <span
                                class="inline-flex items-center
                                       px-2.5 py-1
                                       rounded-full
                                       bg-red-50
                                       text-red-700
                                       text-xs font-bold"
                            >

                                Urgente

                            </span>

                        @else

                            <span
                                class="inline-flex items-center
                                       px-2.5 py-1
                                       rounded-full
                                       bg-slate-100
                                       text-slate-600
                                       text-xs font-semibold"
                            >

                                {{ $orden->prioridad ?? 'Normal' }}

                            </span>

                        @endif

                    </td>


                    {{-- ESTADO --}}
                    <td class="px-5 py-4">

                        <span
                            class="inline-flex items-center
                                   px-2.5 py-1
                                   rounded-full
                                   bg-teal-50
                                   text-teal-700
                                   text-xs font-semibold"
                        >

                            {{ $orden->estadoOrden?->nombre ?? '—' }}

                        </span>

                    </td>


                    {{-- FECHA SALIDA --}}
                    <td class="px-5 py-4 whitespace-nowrap">

                        {{ $orden->fecha_entrega_estimada?->format('d/m/Y') ?? '—' }}

                    </td>


                    {{-- ACCIONES --}}
                    <td class="px-5 py-4">

                        <a
                            href="{{ route('ordenes.show', $orden) }}"
                            class="inline-flex items-center
                                   px-3 py-2
                                   bg-slate-100
                                   hover:bg-teal-50
                                   text-teal-700
                                   rounded-lg
                                   text-xs font-semibold
                                   transition"
                        >

                            Ver orden

                        </a>

                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="10"
                        class="px-5 py-8 text-center text-slate-400"
                    >

                        Sin trabajos programados.

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</div>