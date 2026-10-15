@extends('layouts.app')
@section('title', 'Detalle de gasto | Caja chica')
@section('content')
@include('caja_chica.partials.estilos')
@php($dinero = fn ($centavos) => \App\Support\ImporteCajaChica::formato((int) $centavos))
<div class="cc" style="max-width:1100px">
    <header class="cc-header"><div><p class="cc-kicker">Caja chica</p><h1>Gasto #{{ $gasto->id }}</h1><p class="cc-muted mt-2">Información actual e historial de solicitudes.</p></div><div class="cc-actions"><a class="cc-btn" href="{{ route('caja-chica.index', ['mes' => $gasto->fecha_gasto->format('Y-m')]) }}">Volver al mes</a><a class="cc-btn" href="{{ route('caja-chica.solicitudes') }}">Solicitudes</a></div></header>
    @include('caja_chica.partials.mensajes')
    <section class="cc-card cc-padding" aria-labelledby="titulo-gasto">
        <div class="cc-header"><h2 id="titulo-gasto">{{ $gasto->concepto }}</h2>@include('caja_chica.partials.estado', ['estado' => $gasto->estado])</div>
        <dl>
            <div><dt>Total</dt><dd class="font-bold">Q {{ $dinero($gasto->monto_centavos) }}</dd></div>
            <div><dt>Cantidad</dt><dd>{{ $gasto->cantidad }}</dd></div>
            <div><dt>Precio unitario</dt><dd>Q {{ $dinero($gasto->precio_unitario_centavos) }}</dd></div>
            <div><dt>Responsable del gasto</dt><dd>{{ $gasto->responsable }}</dd></div>
            <div><dt>Número de documento</dt><dd>{{ $gasto->numero_documento ?? '—' }}</dd></div>
            <div><dt>Fecha del gasto</dt><dd>{{ $gasto->fecha_gasto->format('d/m/Y') }}</dd></div>
            <div><dt>Proveedor o beneficiario</dt><dd>{{ $gasto->beneficiario ?? '—' }}</dd></div>
            <div><dt>Método de pago</dt><dd>{{ $gasto->metodo_pago }}</dd></div>
            <div><dt>Documento de respaldo</dt><dd>{{ $gasto->tipo_comprobante }}</dd></div>
            <div><dt>NIT</dt><dd>{{ $gasto->nit ?? '—' }}</dd></div>
            <div><dt>N.º transferencia, depósito o cheque</dt><dd>{{ $gasto->referencia ?? '—' }}</dd></div>
            <div><dt>Comprobante</dt><dd>@if($gasto->comprobante_path)<a class="cc-link" href="{{ route('caja-chica.comprobante', $gasto) }}">Descargar comprobante</a>@else Sin archivo adjunto @endif</dd></div>
            <div><dt>Registrado por</dt><dd>{{ $gasto->usuarioRegistro?->name ?? '—' }} · {{ $gasto->created_at->format('d/m/Y H:i') }}</dd></div>
            <div><dt>Observaciones</dt><dd>{{ $gasto->observaciones ?? '—' }}</dd></div>
        </dl>
        @if($gasto->fecha_autorizacion)
            <div class="mt-5 border-t border-slate-100 pt-4"><p class="cc-muted">Decisión registrada por <strong>{{ $gasto->usuarioAutorizacion?->name ?? '—' }}</strong> el {{ $gasto->fecha_autorizacion->format('d/m/Y H:i') }}.</p><p class="mt-2 text-sm whitespace-pre-wrap">{{ $gasto->motivo_decision }}</p></div>
        @endif
        @if(!$correccionPendiente && !in_array($gasto->estado, ['Anulado', 'Rechazado'], true))<div class="cc-footer"><a class="cc-btn" href="{{ route('caja-chica.correcciones.create', $gasto) }}">Solicitar corrección o anulación</a></div>@endif
    </section>
    @if($gasto->estado === 'Pendiente de autorización')
        <section class="cc-card cc-padding" aria-labelledby="titulo-autorizacion">
            <h2 id="titulo-autorizacion">Autorización del gasto</h2>
            <p class="cc-muted mt-2">El gasto excedía el límite mensual al registrarse. Actualmente hay Q {{ $dinero($resumen['gastado']) }} registrados y autorizados en este mes. El límite es Q {{ $dinero($resumen['limite']) }}.</p>
            @if($correccionPendiente)
                <p class="cc-notice cc-notice-warning mt-4">Existe una corrección pendiente. El Administrador debe atenderla antes de autorizar el gasto original.</p>
            @elseif(auth()->user()->role?->nombre === 'Administrador')
                <form action="{{ route('caja-chica.resolver', $gasto) }}" method="POST" class="mt-5" data-envio-caja>
                    @csrf
                    @method('PATCH')
                    <label for="motivo-autorizacion">Motivo de la decisión *</label><textarea id="motivo-autorizacion" name="motivo" required maxlength="1000" rows="3"></textarea>
                    <p class="cc-help">La autorización corresponde únicamente a este gasto. No amplía el límite mensual para gastos posteriores.</p>
                    <div class="cc-actions mt-4"><button class="cc-btn cc-btn-primary" type="submit" name="decision" value="aprobar">Autorizar gasto</button><button class="cc-btn cc-btn-danger" type="submit" name="decision" value="rechazar">Rechazar gasto</button></div>
                </form>
            @else
                <p class="cc-help mt-4">El Administrador puede autorizar o rechazar esta solicitud desde su cuenta.</p>
            @endif
        </section>
    @endif
    <section class="cc-card cc-padding" aria-labelledby="titulo-historial">
        <h2 id="titulo-historial">Historial de correcciones y anulaciones</h2>
        <p class="cc-muted mt-2">Cada solicitud conserva quién la realizó, qué propuso cambiar y la decisión del Administrador.</p>
        @if($gasto->correcciones->isEmpty())<p class="cc-help mt-4">Este gasto no tiene solicitudes de corrección.</p>@endif
        @include('caja_chica.partials.historial')
    </section>
</div>
@include('caja_chica.partials.envio')
@endsection
