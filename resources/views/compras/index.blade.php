@extends('layouts.app')
@section('title', 'Compras | Laboratorio Dental')
@section('content')
<div class="cc compras">
    @include('compras.partials.cabecera', ['titulo' => 'Compras de materiales y servicios'])
    <div class="cc-actions mb-5"><a class="cc-btn cc-btn-primary" href="{{ route('compras.create') }}">Registrar compra</a><span class="cc-muted">{{ $solicitudes }} solicitudes pendientes de decisión.</span></div>
    <section class="cc-card cc-grid mb-5"><div><p class="cc-muted">Compras vigentes del mes</p><strong>Q {{ \App\Support\ImporteCajaChica::formato($totalMes) }}</strong></div><div><p class="cc-muted">Compras con materiales por recibir</p><strong>{{ $pendientes }}</strong></div><p class="cc-muted"></p></section>
    <form method="GET" class="cc-card mb-5"><div class="cc-grid">
        <div><label for="mes">Mes</label><input id="mes" type="month" name="mes" value="{{ $mes }}" required></div>
        <div><label for="buscar">Proveedor, NIT o factura</label><input id="buscar" name="buscar" value="{{ request('buscar') }}" maxlength="150"></div>
        <div><label for="estado">Estado</label><select id="estado" name="estado"><option value="">Todos</option>@foreach(['Registrada','Anulada'] as $estado)<option @selected(request('estado') === $estado)>{{ $estado }}</option>@endforeach</select></div>
        <div><label for="recepcion">Recepción</label><select id="recepcion" name="recepcion"><option value="">Todas</option>@foreach(['pendiente'=>'Materiales pendientes','recibida'=>'Materiales recibidos','servicios'=>'Solo servicios'] as $valor=>$texto)<option value="{{ $valor }}" @selected(request('recepcion') === $valor)>{{ $texto }}</option>@endforeach</select></div>
    </div><button class="cc-btn cc-btn-primary mt-4">Filtrar</button></form>
    <section class="cc-card"><div class="cc-scroll" tabindex="0" role="region" aria-label="Listado de compras"><table class="cc-table"><thead><tr><th>Fecha</th><th>Proveedor / NIT</th><th>Factura</th><th>Total</th><th>Estado</th><th>Recepción</th><th>Registró</th><th>Detalle</th></tr></thead><tbody>
        @forelse($compras as $compra)<tr><td>{{ $compra->fecha->format('d/m/Y') }}</td><td>{{ $compra->proveedor }}<br><small>{{ $compra->nit }}</small></td><td>{{ $compra->serie }} {{ $compra->numero_factura }}</td><td>Q {{ \App\Support\ImporteCajaChica::formato($compra->total_centavos) }}</td><td>{{ $compra->estado }}</td><td>{{ $compra->fecha_recepcion ? 'Recibida' : ($compra->detalles->contains('tipo', 'Material') ? 'Pendiente' : 'No aplica: servicios') }}</td><td>{{ $compra->registrador?->name }}</td><td><a class="cc-link" href="{{ route('compras.show', $compra) }}">Ver</a></td></tr>@empty<tr><td colspan="8">No hay compras para estos filtros.</td></tr>@endforelse
    </tbody></table></div><div class="mt-4">{{ $compras->links() }}</div></section>
</div>
@endsection
