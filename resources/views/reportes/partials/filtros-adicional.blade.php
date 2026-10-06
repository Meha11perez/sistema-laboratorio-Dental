@foreach($datos['campos'] as $campo)
    <span>
        <strong>{{ $campo['etiqueta'] }}:</strong>
        @if($campo['tipo'] === 'select')
            {{ $campo['opciones'][$datos['filtros'][$campo['nombre']] ?? ''] ?? 'Todos' }}
        @else
            {{ $datos['filtros'][$campo['nombre']] ?? '' }}
        @endif
    </span>
@endforeach
