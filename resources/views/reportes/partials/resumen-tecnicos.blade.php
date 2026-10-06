<table>
    <thead><tr>
        <th scope="col">Técnico del historial</th><th scope="col">Órdenes distintas</th>
        <th scope="col">Registros de etapas</th><th scope="col">Con fecha de cierre</th><th scope="col">Sin fecha de cierre</th>
    </tr></thead>
    <tbody>
        @forelse($porTecnico as $fila)
            <tr>
                <td>{{ $fila->nombre_tecnico }}</td><td>{{ $fila->ordenes }}</td>
                <td>{{ $fila->registros }}</td><td>{{ $fila->cerradas }}</td><td>{{ $fila->sin_cierre }}</td>
            </tr>
        @empty
            <tr><td colspan="5">No hay etapas iniciadas con los filtros seleccionados.</td></tr>
        @endforelse
    </tbody>
</table>
