@if($movimiento->tipo_movimiento === 'Consumo' || ($movimiento->tecnico_id && $movimiento->orden_trabajo_id))
    <p>{{ $movimiento->tecnico?->user?->name ?? 'Técnico' }} → orden {{ $movimiento->ordenTrabajo?->codigo_area ?: ($movimiento->ordenTrabajo?->codigo ?? '—') }}</p>
    <p class="text-xs text-slate-500">Saldo del técnico</p>
@elseif($movimiento->tipo_movimiento === 'Salida' && $movimiento->tecnico_id)
    <p>Bodega → {{ $movimiento->tecnico?->user?->name ?? 'Técnico' }}</p>
    <p class="text-xs text-slate-500">Saldo de bodega</p>
@elseif($movimiento->tipo_movimiento === 'Devolución' && $movimiento->tecnico_id)
    <p>{{ $movimiento->tecnico?->user?->name ?? 'Técnico' }} → bodega</p>
    <p class="text-xs text-slate-500">Saldo de bodega</p>
@elseif($movimiento->tipo_movimiento === 'Entrada')
    <p>Recepción de material → bodega</p>
@elseif($movimiento->tipo_movimiento === 'Devolución')
    <p>Devolución externa → bodega</p>
@elseif($movimiento->tipo_movimiento === 'Ajuste')
    <p>Conteo físico de bodega</p>
@else
    <p>Salida de bodega; destino en observaciones</p>
@endif
