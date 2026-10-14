<p class="mb-2 px-6 text-xs font-semibold uppercase tracking-widest text-slate-400">Contabilidad</p>

@foreach([
    ['Agenda', 'agenda.index', 'agenda.*', 'agenda'],
    ['Inventario general', 'inventario.index', 'inventario.index', 'inventario'],
    ['Inventario por técnico', 'inventario.tecnicos', 'inventario.tecnicos*', 'inventario'],
    ['Movimientos', 'inventario.movimientos', 'inventario.movimientos', 'inventario'],
    ['Pagos', 'pagos.index', 'pagos.*', 'pagos'],
    ['Cuentas de odontólogos', 'cuentas-odontologos.index', 'cuentas-odontologos.*', 'pagos'],
    ['Reporte de pagos', 'reportes.pagos', 'reportes.pagos*', 'reportes'],
    ['Reporte por odontólogo', 'reportes.odontologos', 'reportes.odontologos*', 'reportes'],
    ['Reporte de inventario', 'reportes.inventario', 'reportes.inventario*', 'reportes'],
] as [$titulo, $ruta, $patron, $icono])
    <a href="{{ route($ruta) }}" class="flex items-center gap-3 border-l-4 px-6 py-4 transition {{ request()->routeIs($patron) ? 'border-[#315875] bg-[#e7eef8] font-semibold text-[#315875]' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-[#182d47]' }}">
        @include('layouts.partials.icono', ['icono' => $icono])
        {{ $titulo }}
    </a>
@endforeach