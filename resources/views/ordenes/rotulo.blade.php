<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <title>Rótulo {{ $orden->codigo }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white p-8">

<div class="max-w-md mx-auto border-2 border-slate-900 p-6">

    <h1 class="text-xl font-bold text-center mb-6">
        LABORATORIO DENTAL
    </h1>

    <div class="space-y-3">

        <p>
            <strong>Orden:</strong>
            {{ $orden->codigo }}
        </p>

        <p>
            <strong>Caja:</strong>
            {{ $orden->codigo_caja ?? '—' }}
        </p>

        <p>
            <strong>Doctor:</strong>
            {{ $orden->odontologo?->nombre ?? '—' }}
        </p>

        <p>
            <strong>Clínica:</strong>
            {{ $orden->odontologo?->clinica?->nombre ?? '—' }}
        </p>

        <p>
            <strong>Paciente:</strong>
            {{ $orden->paciente?->nombre }}
            {{ $orden->paciente?->apellido }}
        </p>

        <p>
            <strong>Trabajo:</strong>
            {{ $orden->tipoProtesis?->nombre }}
        </p>

        <p>
            <strong>Especificaciones:</strong>
            {{ $orden->especificaciones }}
        </p>

        <p>
            <strong>Fecha:</strong>
            {{ now()->format('d/m/Y') }}
        </p>

    </div>


    <button
        onclick="window.print()"
        class="mt-6 w-full bg-blue-800 text-white py-3 rounded-lg print:hidden"
    >
        Imprimir Rótulo
    </button>

</div>

</body>

</html>