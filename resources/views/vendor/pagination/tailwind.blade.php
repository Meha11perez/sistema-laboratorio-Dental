@if ($paginator->hasPages())
    @php
        $claseBase = 'inline-flex min-h-10 min-w-10 items-center justify-center rounded-lg border px-3 py-2 text-sm font-semibold transition-colors';

        $claseEnlace = $claseBase . ' border-[#e5eaf5] bg-white text-[#315875] hover:bg-[#e7eef8] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315875]';

        $claseDeshabilitada = $claseBase . ' border-[#e5eaf5] bg-slate-50 text-slate-400';
    @endphp

    <nav
        role="navigation"
        aria-label="Paginación"
        class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
    >
        <p class="text-sm text-slate-500">
            @if ($paginator->count())
                Mostrando
                <span class="font-semibold text-slate-700">
                    {{ $paginator->firstItem() }}
                </span>
                a
                <span class="font-semibold text-slate-700">
                    {{ $paginator->lastItem() }}
                </span>
                de
                <span class="font-semibold text-slate-700">
                    {{ $paginator->total() }}
                </span>
                registros
            @else
                No hay registros en esta página.
            @endif
        </p>

        <ul class="flex flex-wrap items-center gap-2">
            {{-- PÁGINA ANTERIOR --}}
            <li>
                @if ($paginator->onFirstPage())
                    <span
                        class="{{ $claseDeshabilitada }}"
                        aria-disabled="true"
                    >
                        Anterior
                    </span>
                @else
                    <a
                        href="{{ $paginator->previousPageUrl() }}"
                        class="{{ $claseEnlace }}"
                        rel="prev"
                    >
                        Anterior
                    </a>
                @endif
            </li>

            {{-- NÚMEROS DE PÁGINA --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li>
                        <span class="px-2 text-slate-400">
                            {{ $element }}
                        </span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span
                                    class="{{ $claseBase }} border-[#315875] bg-[#315875] text-white"
                                    aria-current="page"
                                    aria-label="Página {{ $page }}"
                                >
                                    {{ $page }}
                                </span>
                            @else
                                <a
                                    href="{{ $url }}"
                                    class="{{ $claseEnlace }}"
                                    aria-label="Ir a la página {{ $page }}"
                                >
                                    {{ $page }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- PÁGINA SIGUIENTE --}}
            <li>
                @if ($paginator->hasMorePages())
                    <a
                        href="{{ $paginator->nextPageUrl() }}"
                        class="{{ $claseEnlace }}"
                        rel="next"
                    >
                        Siguiente
                    </a>
                @else
                    <span
                        class="{{ $claseDeshabilitada }}"
                        aria-disabled="true"
                    >
                        Siguiente
                    </span>
                @endif
            </li>
        </ul>
    </nav>
@endif