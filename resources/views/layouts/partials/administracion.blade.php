@if(in_array($rol, ['Administrador', 'Recepcion'], true))
    <div class="mt-4 pt-4 border-t border-slate-200">
        <p class="px-6 mb-2 text-xs font-semibold text-slate-400 uppercase tracking-widest">
            {{ $rol === 'Administrador' ? 'Administración' : 'Clientes' }}
        </p>

        <details class="group" {{ request()->routeIs('administracion.*') ? 'open' : '' }}>
            <summary class="flex items-center justify-between px-6 py-4 text-slate-600 hover:bg-slate-50 hover:text-[#182d47] transition cursor-pointer">
                <div class="flex items-center gap-3">
                    @include('layouts.partials.icono', ['icono' => 'administracion'])
                    {{ $rol === 'Administrador' ? 'Administración' : 'Clientes' }}
                </div>

                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 group-open:rotate-180 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m6 9 6 6 6-6" />
                </svg>
            </summary>

            <div class="bg-slate-50 border-y border-slate-100">
                @if($rol === 'Administrador')
                    @foreach([
                        'administracion.tecnicos.index' => 'Técnicos',
                    ] as $ruta => $titulo)
                        <a
                            href="{{ route($ruta) }}"
                            class="block pl-14 pr-6 py-3 text-sm transition {{ request()->routeIs(str_replace('.index', '.*', $ruta)) ? 'bg-[#e7eef8] text-[#315875] font-semibold' : 'text-slate-600 hover:text-[#182d47] hover:bg-slate-100' }}"
                        >
                            {{ $titulo }}
                        </a>
                    @endforeach
                @endif

                @foreach([
                    'administracion.odontologos.index' => 'Odontólogos',
                    'administracion.clinicas.index' => 'Clínicas',
                    'administracion.pacientes.index' => 'Pacientes',
                ] as $ruta => $titulo)
                    <a
                        href="{{ route($ruta) }}"
                        class="block pl-14 pr-6 py-3 text-sm transition {{ request()->routeIs(str_replace('.index', '.*', $ruta)) ? 'bg-[#e7eef8] text-[#315875] font-semibold' : 'text-slate-600 hover:text-[#182d47] hover:bg-slate-100' }}"
                    >
                        {{ $titulo }}
                    </a>
                @endforeach
            </div>
        </details>
    </div>
@endif
