@extends('layouts.app')

@section('title', 'Ajustes')

@section('content')
@php
    $usuarioActual = auth()->user();
    $esAdministrador = $usuarioActual->role?->nombre === 'Administrador';
@endphp

<div class="w-full max-w-5xl mx-auto min-w-0">
    <div class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-widest text-[#315875]">
            Mi cuenta
        </p>
        <h1 class="mt-1 text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
            Ajustes
        </h1>
        <p class="mt-2 text-sm text-slate-500">
            Consulta los datos de tu cuenta y tu acceso al laboratorio.
        </p>
    </div>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" aria-labelledby="datos-personales">
        <div class="flex items-center gap-3 border-b border-slate-200 px-5 py-4 sm:px-6">
            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#e7eef8] text-[#315875]">
                @include('layouts.partials.icono', ['icono' => 'administracion'])
            </span>
            <div class="min-w-0">
                <h2 id="datos-personales" class="font-semibold text-slate-900">Datos personales</h2>
                <p class="mt-1 text-xs text-slate-500">Información registrada de tu cuenta.</p>
            </div>
        </div>

        <dl class="grid grid-cols-1 gap-x-8 gap-y-5 p-5 sm:grid-cols-2 sm:p-6">
            <div class="min-w-0">
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Nombre</dt>
                <dd class="mt-1 break-words text-sm font-medium text-slate-800">{{ $usuarioActual->name }}</dd>
            </div>
            <div class="min-w-0">
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Usuario</dt>
                <dd class="mt-1 break-words text-sm font-medium text-slate-800">{{ $usuarioActual->username ?? 'No configurado' }}</dd>
            </div>
            <div class="min-w-0">
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Correo electrónico</dt>
                <dd class="mt-1 break-words text-sm font-medium text-slate-800">{{ $usuarioActual->email ?? 'No registrado' }}</dd>
            </div>
            <div class="min-w-0">
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Rol</dt>
                <dd class="mt-1 text-sm font-medium text-slate-800">{{ $usuarioActual->role?->nombre ?? 'Sin asignar' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Estado de la cuenta</dt>
                <dd class="mt-2">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $usuarioActual->estado ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                        {{ $usuarioActual->estado ? 'Activa' : 'Inactiva' }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Fecha de registro</dt>
                <dd class="mt-1 text-sm font-medium text-slate-800">{{ $usuarioActual->created_at?->format('d/m/Y') ?? 'No disponible' }}</dd>
            </div>
        </dl>

        <p class="border-t border-slate-100 bg-slate-50 px-5 py-4 text-sm text-slate-500 sm:px-6">
            Para actualizar tus datos o restablecer tu contraseña, contacta al administrador.
        </p>
    </section>

    @if($esAdministrador)
        <section class="mt-8" aria-labelledby="gestion-accesos">
            <h2 id="gestion-accesos" class="text-lg font-semibold text-slate-900">Gestión de accesos</h2>
            <p class="mt-1 text-sm text-slate-500">Administra las cuentas y los roles del sistema.</p>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <a href="{{ route('administracion.usuarios.index') }}" class="block min-w-0 rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#315875] hover:shadow-md focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315875]">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-[#e7eef8] text-[#315875]">
                        @include('layouts.partials.icono', ['icono' => 'administracion'])
                    </span>
                    <h3 class="mt-3 font-semibold text-slate-900">Usuarios</h3>
                    <p class="mt-2 text-sm text-slate-500">Crear cuentas, actualizar datos y contraseñas, asignar roles y activar o desactivar usuarios.</p>
                    <span class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-[#315875]">
                        Administrar usuarios
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6" /></svg>
                    </span>
                </a>

                <a href="{{ route('administracion.roles.index') }}" class="block min-w-0 rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#315875] hover:shadow-md focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315875]">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-[#e7eef8] text-[#315875]">
                        @include('layouts.partials.icono', ['icono' => 'garantias'])
                    </span>
                    <h3 class="mt-3 font-semibold text-slate-900">Roles</h3>
                    <p class="mt-2 text-sm text-slate-500">Consultar los roles y administrar sus datos y estado.</p>
                    <span class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-[#315875]">
                        Administrar roles
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6" /></svg>
                    </span>
                </a>
            </div>
        </section>
    @endif
</div>
@endsection
