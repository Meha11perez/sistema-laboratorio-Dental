@if(in_array(auth()->user()?->role?->nombre, ['Administrador', 'Recepcion'], true))
    @php($totalRevisionesProduccion = \App\Services\RevisionProduccion::pendientes()->count())
    @if($totalRevisionesProduccion > 0)
        <aside class="mb-5 flex flex-col gap-4 rounded-xl border border-[#cbdce7] bg-[#f2f7fa] p-4 sm:flex-row sm:items-center sm:justify-between print:hidden" aria-label="Etapas completadas pendientes de revisión">
            <div class="flex min-w-0 items-start gap-3">
                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-[#315875]" aria-hidden="true">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 4H6a2 2 0 0 0-2 2v14h16V6a2 2 0 0 0-2-2h-3M9 3h6v4H9zM8 14l3 3 5-6" /></svg>
                </span>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-[#182d47]">{{ $totalRevisionesProduccion }} {{ $totalRevisionesProduccion === 1 ? 'orden requiere revisión' : 'órdenes requieren revisión' }}</p>
                    <p class="mt-1 text-xs leading-5 text-slate-600">Etapas completadas por los técnicos. Confirme el siguiente paso o la finalización.</p>
                </div>
            </div>
            <a href="{{ route('produccion.revisiones.index') }}" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-[#315875] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#182d47] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315875]">
                Ver pendientes
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6" /></svg>
            </a>
        </aside>
    @else
        <div class="mb-4 text-right print:hidden"><a href="{{ route('produccion.revisiones.index') }}" class="text-sm font-semibold text-[#315875] hover:underline">Revisiones de producción</a></div>
    @endif
@endif
