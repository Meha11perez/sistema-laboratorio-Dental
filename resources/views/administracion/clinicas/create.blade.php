@extends('layouts.app')

@section('title', 'Nueva Clínica | Laboratorio Dental')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('administracion.clinicas.index') }}" class="text-sm font-semibold text-blue-700 hover:underline">← Volver a Clínicas</a>
        <p class="mt-5 text-sm font-semibold uppercase text-blue-800">Administración</p>
        <h1 class="text-3xl font-bold text-slate-900">Nueva Clínica</h1>
        <p class="mt-1 text-slate-500">Registre la información de la clínica.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-5 py-4">
            <p class="mb-2 font-semibold text-red-700">Revise la información ingresada.</p>
            <ul class="ml-5 list-disc text-sm text-red-600">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('administracion.clinicas.store') }}" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label for="nombre" class="mb-2 block text-sm font-semibold text-slate-700">Nombre de la clínica <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" maxlength="150" required placeholder="Ej. Clínica Dental Sonrisas" class="w-full rounded-lg border border-slate-300 px-4 py-2.5">
            </div>

            <div>
                <label for="nit" class="mb-2 block text-sm font-semibold text-slate-700">NIT</label>
                <input type="text" name="nit" id="nit" value="{{ old('nit') }}" maxlength="30" placeholder="Ej. 1234567-8" class="w-full rounded-lg border border-slate-300 px-4 py-2.5">
            </div>

            <div>
                <label for="asistente_secretaria" class="mb-2 block text-sm font-semibold text-slate-700">Asistente o secretaria</label>
                <input type="text" name="asistente_secretaria" id="asistente_secretaria" value="{{ old('asistente_secretaria') }}" maxlength="150" placeholder="Ej. María López" class="w-full rounded-lg border border-slate-300 px-4 py-2.5">
            </div>

            <div>
                <label for="telefono" class="mb-2 block text-sm font-semibold text-slate-700">Teléfono de clínica</label>
                <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}" maxlength="20" placeholder="Ej. 5555-5555" class="w-full rounded-lg border border-slate-300 px-4 py-2.5">
            </div>

            <div>
                <label for="correo" class="mb-2 block text-sm font-semibold text-slate-700">Correo electrónico</label>
                <input type="email" name="correo" id="correo" value="{{ old('correo') }}" maxlength="150" placeholder="Ej. contacto@clinica.com" class="w-full rounded-lg border border-slate-300 px-4 py-2.5">
            </div>

            @include('administracion.clinicas.partials.ubicacion')
        </div>

        <div class="mt-8 flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
            <a href="{{ route('administracion.clinicas.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-center text-slate-600 hover:bg-slate-50">Cancelar</a>
            <button type="submit" class="rounded-lg bg-blue-800 px-5 py-2.5 font-semibold text-white hover:bg-blue-900">Guardar Clínica</button>
        </div>
    </form>
</div>
@endsection
