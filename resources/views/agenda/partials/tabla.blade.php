<div class="overflow-x-auto">

    <table class="w-full text-sm">

        <thead class="bg-white text-slate-500 text-xs uppercase">

            <tr>
                <th class="text-left px-5 py-3">F. Ingreso</th>
                <th class="text-left px-5 py-3">Caja</th>
                <th class="text-left px-5 py-3">Doctor / Clínica</th>
                <th class="text-left px-5 py-3">Paciente</th>
                <th class="text-left px-5 py-3">Especificaciones</th>
                <th class="text-left px-5 py-3">Observaciones</th>
                <th class="text-left px-5 py-3">F. Salida</th>
                <th class="text-left px-5 py-3">Acciones</th>

            </tr>

        </thead>

        <tbody class="divide-y divide-slate-100">

            @forelse($ordenesGrupo as $orden)

                <tr class="hover:bg-slate-50">

                    <td class="px-5 py-4">
                        {{ $orden->fecha_ingreso?->format('d/m/Y') }}
                    </td>

                    <td class="px-5 py-4 font-semibold text-slate-800">
                        {{ $orden->codigo_caja ?? '—' }}
                    </td>

                    <td class="px-5 py-4">

                        {{ $orden->odontologo?->nombre ?? '—' }}

                        @if($orden->odontologo?->clinica)

                            <span class="text-slate-400">
                                - {{ $orden->odontologo->clinica->nombre }}
                            </span>

                        @endif

                    </td>

                    <td class="px-5 py-4">
                        {{ $orden->paciente?->nombre ?? '—' }}
                        {{ $orden->paciente?->apellido }}
                    </td>

                    <td class="px-5 py-4 max-w-xs">
                        {{ $orden->especificaciones }}
                    </td>

                    <td class="px-5 py-4 max-w-xs text-slate-600">
                        {{ $orden->observaciones ?? '—' }}
                    </td>

                    <td class="px-5 py-4">
                        {{ $orden->fecha_entrega_estimada?->format('d/m/Y') ?? '—' }}
                    </td>

                    <td class="px-5 py-4">
                        <a
                            href="{{ route('ordenes.show', $orden) }}"
                            class="inline-flex items-center px-3 py-2
                                    bg-slate-100 hover:bg-teal-50
                                    text-teal-700 rounded-lg text-xs font-semibold"
                        >
                            Ver orden
                        </a>
                    </td>
                </tr>

            @empty

                <tr>

                    <td
                        colspan="8"
                        class="px-5 py-6 text-center text-slate-400"
                    >
                        Sin trabajos programados.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</div>