@extends('layouts.app')
@section('title', 'Solicitudes de compras')
@section('content')
<div class="cc compras">@include('compras.partials.cabecera', ['titulo'=>'Solicitudes de compras'])
    <p class="cc-muted mb-4">Contabilidad consulta el seguimiento. Solo el Administrador aprueba o rechaza desde el detalle de la compra.</p>
    <section class="cc-card"><div class="cc-scroll" tabindex="0" role="region" aria-label="Solicitudes de compras"><table class="cc-table"><thead><tr><th>Solicitud</th><th>Compra / proveedor</th><th>Tipo</th><th>Motivo</th><th>Solicitó</th><th>Estado</th><th>Detalle</th></tr></thead><tbody>@forelse($solicitudes as $solicitud)<tr><td>#{{ $solicitud->id }}</td><td>#{{ $solicitud->compra_id }} · {{ $solicitud->compra?->proveedor }}</td><td>{{ $solicitud->tipo }}</td><td>{{ $solicitud->motivo }}</td><td>{{ $solicitud->solicitante?->name }}</td><td>{{ $solicitud->estado }}</td><td><a class="cc-link" href="{{ route('compras.show', $solicitud->compra_id) }}">Ver comparación</a></td></tr>@empty<tr><td colspan="7">No hay solicitudes registradas.</td></tr>@endforelse</tbody></table></div><div class="mt-4">{{ $solicitudes->links() }}</div></section>
</div>
@endsection
