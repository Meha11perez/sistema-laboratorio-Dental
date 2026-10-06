@if($formato === 'moneda')
    Q {{ number_format((float) ($valor ?? 0), 2, '.', ',') }}
@elseif($formato === 'decimal')
    {{ number_format((float) ($valor ?? 0), 2, '.', ',') }}
@elseif($formato === 'numero')
    {{ number_format((float) ($valor ?? 0), 0, '.', ',') }}
@else
    {{ $valor !== null && (string) $valor !== '' ? $valor : '—' }}
@endif
