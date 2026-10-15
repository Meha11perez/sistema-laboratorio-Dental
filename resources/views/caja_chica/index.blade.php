@extends('layouts.app')
@section('title', 'Caja chica | Laboratorio Dental')
@section('content')
@include('caja_chica.partials.estilos')
@php($dinero = fn ($centavos) => \App\Support\ImporteCajaChica::formato((int) $centavos))
<div class="cc">
    <header class="cc-header">
        <div><p class="cc-kicker">Administración de gastos</p><h1>Caja chica</h1><p class="cc-muted mt-2">Gastos menores, comprobantes y autorizaciones del mes.</p></div>
        <div class="cc-actions"><a class="cc-btn" href="{{ route('caja-chica.solicitudes') }}">Solicitudes <span class="cc-badge cc-badge-pending">{{ $solicitudesPendientes }}</span></a><a class="cc-btn cc-btn-primary" href="{{ route('caja-chica.create') }}">Registrar gasto</a></div>
    </header>
    @include('caja_chica.partials.mensajes')
    @include('caja_chica.partials.resumen')
    <form action="{{ route('caja-chica.index') }}" method="GET" class="cc-card cc-padding">
        <div class="cc-grid">
            <div><label for="mes">Mes</label><input type="month" id="mes" name="mes" required value="{{ $mes }}"></div>
            <div><label for="estado">Estado</label><select id="estado" name="estado"><option value="">Todos</option>@foreach(\App\Models\CajaChicaGasto::ESTADOS as $estado)<option value="{{ $estado }}" @selected(request('estado') === $estado)>{{ $estado }}</option>@endforeach</select></div>
            <div><label for="buscar">Buscar</label><input id="buscar" name="buscar" maxlength="150" placeholder="Concepto, responsable o documento" value="{{ request('buscar') }}"></div>
            <div class="cc-actions self-end"><button type="submit" class="cc-btn cc-btn-primary">Filtrar</button><a class="cc-btn" href="{{ route('caja-chica.index') }}">Limpiar</a></div>
        </div>
    </form>
    <div class="cc-header"><p class="cc-muted">Los indicadores incluyen todo el mes. Los filtros de estado y búsqueda se aplican al listado.</p><div class="cc-actions"><a class="cc-btn cc-btn-primary" href="{{ route('caja-chica.excel', ['mes' => $mes]) }}">Excel del mes</a><a class="cc-btn" href="{{ route('caja-chica.imprimir', ['mes' => $mes]) }}" target="_blank" rel="noopener">Imprimir mes</a></div></div>
    <section class="cc-card" aria-label="Gastos del mes">
        <div class="cc-scroll" tabindex="0" role="region" aria-label="Listado de gastos de caja chica">
            <table class="cc-table"><thead><tr><th>Gasto</th><th>Fecha</th><th>N.º documento</th><th>Especificaciones</th><th>Cantidad</th><th class="cc-number">Precio unitario</th><th class="cc-number">Total</th><th>Estado</th><th>Responsable</th><th>Acción</th></tr></thead><tbody>
                @forelse($gastos as $gasto)
                    <tr><td>#{{ $gasto->id }}</td><td class="whitespace-nowrap">{{ $gasto->fecha_gasto->format('d/m/Y') }}</td><td>{{ $gasto->numero_documento ?? '—' }}</td><td class="cc-concepto"><p class="font-semibold">{{ $gasto->concepto }}</p><p class="cc-help">{{ $gasto->beneficiario ?? '—' }}</p></td><td>{{ $gasto->cantidad }}</td><td class="cc-number">Q {{ $dinero($gasto->precio_unitario_centavos) }}</td><td class="cc-number">Q {{ $dinero($gasto->monto_centavos) }}</td><td>@include('caja_chica.partials.estado', ['estado' => $gasto->estado])</td><td>{{ $gasto->responsable }}</td><td><a class="cc-link whitespace-nowrap" href="{{ route('caja-chica.show', $gasto) }}">Ver detalle</a></td></tr>
                @empty
                    <tr><td colspan="10" class="cc-empty">No hay gastos para los filtros seleccionados.</td></tr>
                @endforelse
            </tbody></table>
        </div>
        @if($gastos->hasPages())<div class="cc-padding border-t border-slate-100">{{ $gastos->onEachSide(1)->links() }}</div>@endif
    </section>
    <p class="cc-help"></p>
    <p class="cc-help"></p>
</div>
@endsection
