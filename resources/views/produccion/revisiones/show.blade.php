@extends('layouts.app')
@section('title', 'Revisar etapa completada')
@section('content')
<div class="mx-auto w-full max-w-4xl min-w-0">
    <a href="{{ route('produccion.revisiones.index') }}" class="text-sm font-semibold text-[#315875] hover:underline">← Volver a revisiones</a>
    <h1 class="mt-3 text-2xl font-bold text-slate-900 sm:text-3xl">Revisar {{ $orden->codigo }}</h1>
    <p class="mt-2 text-sm text-slate-500">Confirme el avance cuando haya verificado que el trabajo está listo.</p>

    @if($errors->any())
        <div role="alert" class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800"><ul class="list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <dl class="mt-6 grid grid-cols-1 gap-5 rounded-xl border border-slate-200 bg-white p-5 sm:grid-cols-2">
        <div><dt class="text-xs text-slate-500">Área / Código</dt><dd class="mt-1 font-semibold text-slate-900">{{ ['removible' => 'Prótesis Removibles', 'fija' => 'Prótesis Fijas', 'cromo_cobalto' => 'Cromo Cobalto', 'ortodoncia' => 'Aparatos de Ortodoncia'][$orden->area_trabajo] ?? 'Sin área' }} · {{ $orden->codigo_area ?? '—' }}</dd></div>
        <div><dt class="text-xs text-slate-500">Paciente</dt><dd class="mt-1 text-slate-900">{{ $orden->paciente?->nombre ?? '—' }} {{ $orden->paciente?->apellido ?? '' }}</dd></div>
        <div><dt class="text-xs text-slate-500">Etapa completada</dt><dd class="mt-1 font-semibold text-slate-900">{{ $historial->etapaProduccion?->nombre ?? '—' }}</dd></div>
        <div><dt class="text-xs text-slate-500">Completada por</dt><dd class="mt-1 text-slate-900">{{ $historial->tecnico?->user?->name ?? 'Técnico no disponible' }} · {{ $historial->fecha_fin?->copy()->timezone('America/Guatemala')->format('d/m/Y H:i') ?? '—' }}</dd></div>
    </dl>

    <form method="POST" action="{{ route('produccion.revisiones.update', $historial) }}" class="mt-6 rounded-xl border border-slate-200 bg-white p-5">
        @csrf
        @method('PATCH')
        <input type="hidden" name="accion" value="{{ $siguienteEtapa ? 'avanzar' : 'terminar' }}">

        @if($siguienteEtapa)
            <h2 class="font-semibold text-slate-900">Siguiente etapa: {{ $siguienteEtapa->nombre }}</h2>
            <p class="mt-2 text-sm text-slate-500">Se conservará el historial completado y se asignará la siguiente etapa al técnico seleccionado.</p>
            <label for="tecnico_actual_id" class="mb-2 mt-5 block text-sm font-semibold text-slate-700">Técnico responsable *</label>
            <select id="tecnico_actual_id" name="tecnico_actual_id" required class="w-full rounded-lg border border-slate-300 bg-white px-4 py-3">
                <option value="">Seleccione un técnico</option>
                @foreach($tecnicos as $tecnico)
                    <option value="{{ $tecnico->id }}" @selected(old('tecnico_actual_id') == $tecnico->id)>{{ $tecnico->user->name }}</option>
                @endforeach
            </select>
            @if($tecnicos->isEmpty())<p class="mt-2 text-sm text-amber-700">No hay técnicos con cuenta activa disponibles. El administrador debe revisar las cuentas antes de asignar.</p>@endif
        @else
            <h2 class="font-semibold text-slate-900">Confirmar trabajo Terminado</h2>
            <p class="mt-2 text-sm text-slate-500">{{ $orden->etapaActual?->es_final ? 'La etapa completada está configurada como final.' : 'No hay otra etapa activa posterior configurada para esta área.' }} Si el trabajo ya está listo, confirme su finalización.</p>
            <p class="mt-2 text-sm text-slate-500">Después podrá programar la entrega y asignarla a un mensajero. La entrega se confirmará desde Mensajería.</p>
        @endif

        <label for="observaciones_revision" class="mb-2 mt-5 block text-sm font-semibold text-slate-700">Observaciones de revisión</label>
        <textarea id="observaciones_revision" name="observaciones_revision" rows="3" maxlength="1000" class="w-full rounded-lg border border-slate-300 px-4 py-3">{{ old('observaciones_revision') }}</textarea>
        <div class="mt-6 flex flex-col gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
            <a href="{{ route('ordenes.show', $orden) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 px-5 py-3 text-sm font-semibold text-[#315875]">Ver orden</a>
            <button type="submit" @disabled($siguienteEtapa && $tecnicos->isEmpty()) class="rounded-lg bg-[#315875] px-5 py-3 text-sm font-semibold text-white hover:bg-[#182d47] disabled:cursor-not-allowed disabled:opacity-50">{{ $siguienteEtapa ? 'Confirmar siguiente etapa' : 'Confirmar Terminado' }}</button>
        </div>
    </form>
</div>
@endsection
