@extends('layouts.app')

@section('title', 'Administración')

@section('content')

<div class="max-w-7xl mx-auto">

    <div class="mb-8">

        <p class="text-sm font-semibold text-blue-800 uppercase">
            Configuración
        </p>

        <h1 class="text-3xl font-bold text-slate-900">
            Administración
        </h1>

        <p class="text-slate-500 mt-1">
            Gestión de información general del sistema.
        </p>

    </div>


    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">

        @foreach([
            ['Usuarios', 'Gestión de cuentas y accesos.', '👥'],
            ['Roles', 'Permisos y perfiles del sistema.', '🔐'],
            ['Técnicos', 'Personal técnico del laboratorio.', '👨‍🔧'],
            ['Odontólogos', 'Registro de odontólogos.', '🦷'],
            ['Clínicas', 'Clínicas asociadas al laboratorio.', '🏥'],
            ['Pacientes', 'Registro de pacientes.', '👤']
        ] as [$titulo, $descripcion, $icono])

            <div class="bg-white border border-slate-200 rounded-xl
                        shadow-sm p-6 hover:shadow-md transition">

                <div class="text-3xl mb-4">
                    {{ $icono }}
                </div>

                <h2 class="font-bold text-lg">
                    {{ $titulo }}
                </h2>

                <p class="text-sm text-slate-500 mt-2">
                    {{ $descripcion }}
                </p>

                <button class="text-blue-800 font-semibold text-sm mt-5">
                    Administrar →
                </button>

            </div>

        @endforeach

    </div>

</div>

@endsection