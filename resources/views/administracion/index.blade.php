@extends('layouts.app')

@section('title', 'Administración')

@section('content')
<div class="w-full max-w-7xl mx-auto min-w-0">
    <div class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-widest text-[#315875]">Laboratorio</p>
        <h1 class="mt-1 text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Administración</h1>
        <p class="mt-2 text-sm text-slate-500">Gestión del personal técnico y los registros del laboratorio.</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['Técnicos', 'Personal técnico del laboratorio.', 'administracion.tecnicos.index'],
            ['Odontólogos', 'Registro de odontólogos.', 'administracion.odontologos.index'],
            ['Clínicas', 'Clínicas asociadas al laboratorio.', 'administracion.clinicas.index'],
            ['Pacientes', 'Registro de pacientes.', 'administracion.pacientes.index'],
        ] as [$titulo, $descripcion, $ruta])
            <a href="{{ route($ruta) }}" class="block min-w-0 rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#315875] hover:shadow-md focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315875]">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-[#e7eef8] text-[#315875]">
                    @include('layouts.partials.icono', ['icono' => 'administracion'])
                </span>
                <h2 class="mt-3 font-semibold text-slate-900">{{ $titulo }}</h2>
                <p class="mt-2 text-sm text-slate-500">{{ $descripcion }}</p>
                <span class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-[#315875]">
                    Administrar
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6" /></svg>
                </span>
            </a>
        @endforeach
    </div>

    <p class="mt-6 text-sm text-slate-500">
        La gestión de Usuarios y Roles está disponible en
        <a href="{{ route('ajustes.index') }}" class="font-semibold text-[#315875] hover:underline">Ajustes</a>.
    </p>
</div>
@endsection