@extends('layouts.app')
@section('title', 'Revisiones de producción')
@section('content')
<div class="w-full min-w-0">
    <div class="mb-6">
        <a href="{{ route('agenda.index') }}" class="text-sm font-semibold text-[#315875] hover:underline">← Volver a la agenda</a>
        <h1 class="mt-3 text-2xl font-bold text-slate-900 sm:text-3xl">Revisiones de producción</h1>
        <p class="mt-2 text-sm text-slate-500">Etapas completadas por los técnicos que necesitan confirmación de Recepción o Administración.</p>
    </div>

    @foreach(['success' => 'border-emerald-200 bg-emerald-50 text-emerald-800', 'error' => 'border-red-200 bg-red-50 text-red-800'] as $tipo => $estilo)
        @if(session($tipo))
            <div role="status" class="mb-5 rounded-lg border p-4 {{ $estilo }}">{{ session($tipo) }}</div>
        @endif
    @endforeach
    @if($errors->any())
        <div role="alert" class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <ul class="list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="font-semibold text-slate-900">{{ $pendientes->total() }} {{ $pendientes->total() === 1 ? 'orden pendiente' : 'órdenes pendientes' }}</h2>
            <p class="mt-1 text-xs text-slate-500">Listado compartido. Se actualiza al cargar la página.</p>
        </div>
        <div class="overflow-x-auto" tabindex="0" role="region" aria-label="Órdenes pendientes de revisión">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                    <tr><th class="px-5 py-4">Orden / Área</th><th class="px-5 py-4">Paciente</th><th class="px-5 py-4">Odontólogo</th><th class="px-5 py-4">Etapa completada</th><th class="px-5 py-4">Técnico</th><th class="px-5 py-4">Finalización de etapa</th><th class="px-5 py-4">Acciones</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pendientes as $historial)
                        @php($ordenPendiente = $historial->ordenTrabajo)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4"><p class="font-semibold text-[#315875]">{{ $ordenPendiente->codigo }}</p><p class="mt-1 text-xs text-slate-500">{{ $ordenPendiente->codigo_area ?? 'Sin código de área' }}</p></td>
                            <td class="px-5 py-4">{{ $ordenPendiente->paciente?->nombre ?? '—' }} {{ $ordenPendiente->paciente?->apellido ?? '' }}</td>
                            <td class="px-5 py-4">{{ $ordenPendiente->odontologo?->nombre ?? '—' }}</td>
                            <td class="px-5 py-4">{{ $historial->etapaProduccion?->nombre ?? '—' }}</td>
                            <td class="px-5 py-4">{{ $historial->tecnico?->user?->name ?? 'Técnico no disponible' }}</td>
                            <td class="whitespace-nowrap px-5 py-4">{{ $historial->fecha_fin?->copy()->timezone('America/Guatemala')->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="whitespace-nowrap px-5 py-4"><a href="{{ route('produccion.revisiones.show', $historial) }}" class="font-semibold text-[#315875] hover:underline">Revisar avance</a><a href="{{ route('ordenes.show', $ordenPendiente) }}" class="ml-3 text-slate-600 hover:underline">Ver orden</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-slate-500">No hay etapas completadas pendientes de revisión.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pendientes->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $pendientes->onEachSide(1)->links() }}</div>
        @endif
    </div>

    <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <h2 class="border-b border-slate-200 px-5 py-4 font-semibold text-slate-900">Últimas revisiones atendidas</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500"><tr><th class="px-5 py-3">Orden</th><th class="px-5 py-3">Etapa revisada</th><th class="px-5 py-3">Revisada por</th><th class="px-5 py-3">Fecha de revisión</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($revisadas as $revision)
                        <tr><td class="px-5 py-3">@if($revision->ordenTrabajo)<a href="{{ route('ordenes.show', $revision->ordenTrabajo) }}" class="font-semibold text-[#315875] hover:underline">{{ $revision->ordenTrabajo->codigo }}</a>@else — @endif</td><td class="px-5 py-3">{{ $revision->etapaProduccion?->nombre ?? '—' }}</td><td class="px-5 py-3">{{ $revision->usuarioRevision?->name ?? '—' }}</td><td class="whitespace-nowrap px-5 py-3">{{ $revision->fecha_revision?->copy()->timezone('America/Guatemala')->format('d/m/Y H:i') ?? '—' }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-6 text-center text-slate-500">Todavía no se han registrado revisiones con este flujo.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
