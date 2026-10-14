<p class="mb-2 px-6 text-xs font-semibold uppercase tracking-widest text-slate-400">Recepción</p>

@foreach([
    ['Agenda', 'agenda.index', 'agenda.*', 'agenda'],
    ['Nueva orden', 'ordenes.create', 'ordenes.create', 'ordenes'],
    ['Crear paciente', 'administracion.pacientes.create', 'administracion.pacientes.*', 'administracion'],
    ['Rutas de mensajería', 'mensajeria.index', 'mensajeria.*', 'mensajeria'],
    ['Entregas', 'mensajeria.entregas', 'mensajeria.entregas*', 'mensajeria'],
    ['Recolecciones', 'mensajeria.recolecciones', 'mensajeria.recolecciones*', 'recolecciones'],
] as [$titulo, $ruta, $patron, $icono])
    <a href="{{ route($ruta) }}" class="flex items-center gap-3 border-l-4 px-6 py-4 transition {{ request()->routeIs($patron) ? 'border-[#315875] bg-[#e7eef8] font-semibold text-[#315875]' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-[#182d47]' }}">
        @include('layouts.partials.icono', ['icono' => $icono])
        {{ $titulo }}
    </a>
@endforeach