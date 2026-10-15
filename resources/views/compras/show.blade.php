@extends('layouts.app')
@section('title', 'Detalle de compra')
@section('content')
@php($admin = auth()->user()?->role?->nombre === 'Administrador')
<div class="cc compras">@include('compras.partials.cabecera', ['titulo'=>'Compra #'.$compra->id])
    <section class="cc-card"><div class="cc-header"><div><h2>{{ $compra->estado }}</h2><p class="cc-muted">Registró {{ $compra->registrador?->name }} · {{ $compra->created_at?->timezone('America/Guatemala')->format('d/m/Y H:i') }}</p></div><a class="cc-btn" href="{{ route('compras.comprobante', $compra) }}">Descargar factura</a></div>
        @include('compras.partials.datos', ['datos'=>$compra->instantanea()])
        <p class="cc-muted mt-4">{{ $compra->fecha_recepcion ? 'Recepción confirmada por '.$compra->receptor?->name.' el '.$compra->fecha_recepcion->timezone('America/Guatemala')->format('d/m/Y H:i') : ($compra->detalles->contains('tipo','Material') ? 'Materiales pendientes de recepción.' : 'Compra de servicios: no modifica inventario.') }}</p>
        @if($compra->fecha_recepcion)<ul class="mt-3">@foreach($compra->detalles->where('tipo','Material') as $linea)<li>Detalle #{{ $linea->id }} · Entrada #{{ $linea->movimiento_entrada_id }} · {{ $linea->material?->nombre }}</li>@endforeach</ul>@endif
    </section>
    @if($pendiente)<div class="cc-notice cc-notice-warning">Existe una solicitud pendiente. Los datos actuales se conservan hasta su aprobación.</div>@endif
    @if($compra->estado === 'Registrada' && !$pendiente)
        <div class="cc-actions mb-5"><a class="cc-btn" href="{{ route('compras.corregir', $compra) }}">Solicitar corrección</a></div>
        @if(!$compra->fecha_recepcion)
        <details class="cc-card"><summary class="font-semibold cursor-pointer">Solicitar anulación</summary><p class="cc-muted my-3">No se elimina la factura ni su historial. Requiere aprobación del Administrador.</p><form method="POST" action="{{ route('compras.solicitar', $compra) }}">@csrf<input type="hidden" name="tipo" value="Anular"><input type="hidden" name="version_compra" value="{{ $compra->version }}"><input type="hidden" name="clave_registro" value="{{ (string) \Illuminate\Support\Str::uuid() }}"><label>Motivo *<textarea name="motivo" required minlength="5" maxlength="1000">{{ old('motivo') }}</textarea></label><button class="cc-btn cc-btn-danger mt-3">Enviar solicitud de anulación</button></form></details>
        @endif
        @if($admin && !$compra->fecha_recepcion && $compra->detalles->contains('tipo','Material'))
        <section class="cc-card"><h2>Confirmar recepción completa</h2><p class="cc-muted my-3">Confirme únicamente cuando recibió todos los materiales de esta factura, en las unidades indicadas. No registre además una entrada manual por esta compra. Los servicios no generan stock.</p><form method="POST" action="{{ route('compras.recibir', $compra) }}">@csrf<input type="hidden" name="version_compra" value="{{ $compra->version }}"><label>Observación de recepción *<textarea name="motivo" required minlength="5" maxlength="1000">{{ old('motivo') }}</textarea></label><label class="compras-check"><input type="checkbox" name="confirmar" value="1" required> Verifiqué la factura y recibí todos sus materiales.</label><button class="cc-btn cc-btn-primary mt-3">Confirmar entrada al inventario</button></form></section>
        @endif
    @endif
    <section class="cc-card"><h2>Solicitudes y comparación de cambios</h2>
        @forelse($compra->correcciones as $correccion)
        <article class="cc-history"><h3 class="font-semibold">Solicitud #{{ $correccion->id }} · {{ $correccion->tipo }} · {{ $correccion->estado }}</h3><p>{{ $correccion->motivo }}</p><p class="cc-muted">{{ $correccion->solicitante?->name }} · {{ $correccion->created_at?->timezone('America/Guatemala')->format('d/m/Y H:i') }}</p>
            <details class="my-3"><summary>Datos anteriores</summary>@include('compras.partials.datos', ['datos'=>$correccion->anteriores])<a class="cc-btn mt-3" href="{{ route('compras.comprobante', ['compra'=>$compra, 'correccion'=>$correccion->id, 'version'=>'anterior']) }}">Factura anterior</a></details>
            @if($correccion->propuestos)<details class="my-3"><summary>Datos propuestos</summary>@include('compras.partials.datos', ['datos'=>$correccion->propuestos])<a class="cc-btn mt-3" href="{{ route('compras.comprobante', ['compra'=>$compra, 'correccion'=>$correccion->id, 'version'=>'propuesta']) }}">Factura propuesta</a></details>@endif
            @if($correccion->aplicados)<details class="my-3"><summary>Datos aplicados</summary>@include('compras.partials.datos', ['datos'=>$correccion->aplicados])</details>@endif
            @if($correccion->estado === 'Pendiente' && $admin)<form action="{{ route('compras.resolver', $correccion) }}" method="POST" class="my-4">@csrf @method('PATCH')<label>Motivo de la decisión *<textarea name="motivo" required minlength="5" maxlength="1000"></textarea></label><div class="cc-actions mt-3"><button name="decision" value="aprobar" class="cc-btn cc-btn-primary">Aprobar solicitud</button><button name="decision" value="rechazar" class="cc-btn cc-btn-danger">Rechazar solicitud</button></div></form>@elseif($correccion->estado !== 'Pendiente')<p class="cc-muted">Resolvió {{ $correccion->resolutor?->name }} · {{ $correccion->fecha_resolucion?->timezone('America/Guatemala')->format('d/m/Y H:i') }} · {{ $correccion->motivo_resolucion }}</p>@endif
        </article>
        @empty<p class="cc-muted mt-3">No hay solicitudes.</p>@endforelse
    </section>
    <section class="cc-card"><h2>Historial de la compra</h2>@foreach($compra->historiales as $evento)<article class="cc-history"><h3 class="font-semibold">{{ $evento->accion }} · {{ $evento->usuario?->name }}</h3><p class="cc-muted">{{ $evento->created_at?->timezone('America/Guatemala')->format('d/m/Y H:i') }}</p><p>{{ $evento->motivo }}</p><details class="mt-3"><summary>Ver datos conservados</summary>@include('compras.partials.datos', ['datos'=>$evento->datos])</details></article>@endforeach</section>
</div>
@endsection
