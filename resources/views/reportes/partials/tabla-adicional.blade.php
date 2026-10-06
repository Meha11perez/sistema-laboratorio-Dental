<table class="ra-table">
    <thead><tr>
        @foreach($datos['columnas'] as $columna)<th scope="col">{{ $columna['etiqueta'] }}</th>@endforeach
    </tr></thead>
    <tbody>
        @forelse($filas as $fila)
            <tr>
                @foreach($datos['columnas'] as $columna)
                    <td @class(['ra-importe' => in_array($columna['formato'], ['moneda', 'decimal'], true)])>
                        @include('reportes.partials.valor-adicional', ['valor' => $fila[$columna['campo']] ?? null, 'formato' => $columna['formato']])
                    </td>
                @endforeach
            </tr>
        @empty
            <tr><td colspan="{{ count($datos['columnas']) }}">No hay resultados en esta página con los filtros seleccionados.</td></tr>
        @endforelse
    </tbody>
</table>
