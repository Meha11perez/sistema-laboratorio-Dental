@extends('layouts.app')
@section('title', 'Solicitudes | Caja chica')
@section('content')
@include('caja_chica.partials.estilos')
@php($dinero = fn ($centavos) => \App\Support\ImporteCajaChica::formato((int) $centavos))
<div class="cc">
    <header class="cc-header"><div><p class="cc-kicker">Caja chica</p><h1>Solicitudes pendientes</h1><p class="cc-muted mt-2">Incluye todos los meses. El Administrador decide desde el detalle de cada gasto.</p></div><a class="cc-btn" href="{{ route('caja-chica.index') }}">Volver a caja chica</a></header>
    @include('caja_chica.partials.mensajes')
    <section class="cc-card" aria-labelledby="titulo-excedentes">
        <div class="cc-padding"><h2 id="titulo-excedentes">Gastos que requieren autorización</h2></div>
        <div class="cc-scroll" tabindex="0" role="region" aria-label="Gastos pendientes de autorización"><table class="cc-table"><thead><tr><th>Gasto</th><th>Fecha</th><th>Concepto</th><th class="cc-number">Monto</th><th>Registrado por</th><th>Acción</th></tr></thead><tbody>
            @forelse($gastos as $gasto)<tr><td>#{{ $gasto->id }}</td><td>{{ $gasto->fecha_gasto->format('d/m/Y') }}</td><td>{{ $gasto->concepto }}</td><td class="cc-number">Q {{ $dinero($gasto->monto_centavos) }}</td><td>{{ $gasto->usuarioRegistro?->name ?? '—' }}</td><td><a class="cc-link" href="{{ route('caja-chica.show', $gasto) }}">Ver solicitud</a></td></tr>@empty<tr><td colspan="6" class="cc-empty">No hay gastos pendientes de autorización.</td></tr>@endforelse
        </tbody></table></div>
        @if($gastos->hasPages())<div class="cc-padding">{{ $gastos->onEachSide(1)->links() }}</div>@endif
    </section>
    <section class="cc-card" aria-labelledby="titulo-correcciones">
        <div class="cc-padding"><h2 id="titulo-correcciones">Correcciones y anulaciones solicitadas</h2></div>
        <div class="cc-scroll" tabindex="0" role="region" aria-label="Correcciones pendientes"><table class="cc-table"><thead><tr><th>Solicitud</th><th>Gasto</th><th>Tipo</th><th>Motivo</th><th>Solicitada por</th><th>Acción</th></tr></thead><tbody>
            @forelse($correcciones as $correccion)<tr><td>#{{ $correccion->id }}</td><td>#{{ $correccion->caja_chica_gasto_id }}</td><td>{{ $correccion->tipo }}</td><td>{{ $correccion->motivo_solicitud }}</td><td>{{ $correccion->usuarioSolicitud?->name ?? '—' }}</td><td><a class="cc-link whitespace-nowrap" href="{{ route('caja-chica.show', $correccion->caja_chica_gasto_id) }}#correccion-{{ $correccion->id }}">Ver comparación</a></td></tr>@empty<tr><td colspan="6" class="cc-empty">No hay correcciones pendientes.</td></tr>@endforelse
        </tbody></table></div>
        @if($correcciones->hasPages())<div class="cc-padding">{{ $correcciones->onEachSide(1)->links() }}</div>@endif
    </section>
</div>
@endsection
