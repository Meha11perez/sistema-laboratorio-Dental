<div class="shrink-0 border-t border-slate-200">
    <a
        href="{{ route('ajustes.index') }}"
        class="flex items-center gap-3 border-l-4 px-6 py-4 transition {{ request()->routeIs('ajustes.*', 'administracion.usuarios.*', 'administracion.roles.*') ? 'border-[#315875] bg-[#e7eef8] font-semibold text-[#315875]' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-[#182d47]' }}"
    >
        @include('layouts.partials.icono', ['icono' => 'ajustes'])
        Ajustes
    </a>
</div>
