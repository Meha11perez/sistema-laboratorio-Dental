<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Rótulo {{ $orden->codigo }}</title>
    @include('ordenes.partials.estilos-rotulo')
</head>

<body>

    <main class="rotulo">

        <header class="encabezado">
            <img
                src="{{ asset('images/logo-dis-dental.png') }}"
                alt="Diseño Dental — Laboratorio dental"
                class="logo"
            >

            <h1>Rótulo de trabajo</h1>
        </header>

        <div class="codigo-orden">
            <span>Orden de trabajo</span>

            <strong>{{ $orden->codigo }}</strong>
        </div>

        <div class="datos">

            <p>
                <strong>Código de área:</strong>
                {{ $orden->codigo_area ?: '—' }}
            </p>

            <p>
                <strong>Área de trabajo:</strong>
                {{ $orden->area_trabajo
                    ? ucfirst(str_replace('_', ' ', $orden->area_trabajo))
                    : '—' }}
            </p>

            <p>
                <strong>Doctor:</strong>
                {{ $orden->odontologo?->nombre ?: '—' }}
            </p>

            <p>
                <strong>Clínica:</strong>
                {{ $orden->odontologo?->clinica?->nombre ?: '—' }}
            </p>

            <p>
                <strong>Paciente:</strong>
                {{ trim(
                    ($orden->paciente?->nombre ?? '') . ' ' .
                    ($orden->paciente?->apellido ?? '')
                ) ?: '—' }}
            </p>

            <p>
                <strong>Trabajo:</strong>
                {{ $orden->tipoProtesis?->nombre ?: '—' }}
            </p>

        </div>

        <div class="especificaciones">
            <strong>Especificaciones:</strong>

            <p>{{ $orden->especificaciones ?: 'Sin especificaciones registradas.' }}</p>
        </div>

        <p class="fecha">
            Fecha de impresión: {{ now()->format('d/m/Y') }}
        </p>

    </main>

    <div class="acciones">
        <button
            type="button"
            onclick="window.print()"
            class="boton-imprimir"
        >
            Imprimir rótulo
        </button>
    </div>

</body>

</html>