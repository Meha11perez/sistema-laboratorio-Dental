<table>
    <thead><tr>
        <th scope="col">Orden / registro</th><th scope="col">Técnico del historial</th>
        <th scope="col">Área / prótesis</th><th scope="col">Etapa</th>
        <th scope="col">Inicio</th><th scope="col">Fin</th><th scope="col">Estado registrado</th>
    </tr></thead>
    <tbody>
        @forelse($historiales as $historial)
            <tr>
                <td>
                    @if($historial->ordenTrabajo)
                        @if($paraImprimir)
                            {{ $historial->ordenTrabajo->codigo_area ?: $historial->ordenTrabajo->codigo }}
                        @else
                            <a href="{{ route('reportes.trazabilidad', ['orden' => $historial->orden_trabajo_id]) }}" class="rl-link">{{ $historial->ordenTrabajo->codigo_area ?: $historial->ordenTrabajo->codigo }}</a>
                        @endif
                    @else
                        Orden no disponible
                    @endif
                    <p class="rl-note">Registro #{{ $historial->id }}</p>
                </td>
                <td>{{ $historial->tecnico?->user?->name ?? ($historial->tecnico_id ? 'Técnico #'.$historial->tecnico_id : 'Sin técnico asignado') }}</td>
                <td>{{ $areas[$historial->ordenTrabajo?->area_trabajo ?? ''] ?? 'Área por revisar' }}<p class="rl-note">{{ $historial->ordenTrabajo?->tipoProtesis?->nombre ?? 'Sin tipo de prótesis' }}</p></td>
                <td>{{ $historial->etapaProduccion?->nombre ?? 'Etapa no disponible' }}</td>
                <td>{{ $historial->fecha_inicio?->format('d/m/Y H:i') ?? 'Sin fecha' }}</td>
                <td>{{ $historial->fecha_fin?->format('d/m/Y H:i') ?? 'Sin fecha de cierre' }}</td>
                <td>{{ $historial->estado ?: 'Sin estado registrado' }}</td>
            </tr>
        @empty
            <tr><td colspan="7">No hay registros en esta página con los filtros seleccionados.</td></tr>
        @endforelse
    </tbody>
</table>
