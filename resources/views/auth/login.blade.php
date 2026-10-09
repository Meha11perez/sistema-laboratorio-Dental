<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Iniciar sesión | Diseño Dental</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html {
            min-height: 100%;
        }

        body.dental-login {
            box-sizing: border-box;
            margin: 0;
            min-height: 100vh;
            min-height: 100svh;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, Arial, sans-serif;
            background: #edf3f8;
            color: #18324b;
        }

        .dental-login *,
        .dental-login *::before,
        .dental-login *::after {
            box-sizing: border-box;
        }

        .dental-login .login-card {
            width: 100%;
            max-width: 1020px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            overflow: hidden;
            border-radius: 20px;
            border: 1px solid #dce6ef;
            background: #fff;
            box-shadow: 0 18px 45px rgba(10, 45, 90, .13);
        }

        .dental-login .brand-panel,
        .dental-login .access-panel {
            padding: 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .dental-login .company-logo {
            display: block;
            width: 100%;
            max-width: 330px;
            height: auto;
            margin: 0 0 24px;
        }

        .dental-login .brand-label {
            margin: 0 0 14px;
            color: #073279;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .dental-login .brand-title {
            margin: 0 0 16px;
            color: #073279;
            font-size: 29px;
            font-weight: 700;
            line-height: 1.25;
        }

        .dental-login .brand-description {
            margin: 0;
            color: #64748b;
            font-size: 14px;
            line-height: 1.8;
        }

        .dental-login .services {
            margin: 26px 0 0;
            padding: 0;
            list-style: none;
        }

        .dental-login .services li {
            margin-bottom: 12px;
            padding: 11px 14px;
            border-left: 3px solid #1bb5c9;
            background: #f4f8fc;
            border-radius: 0 6px 6px 0;
            color: #294565;
            font-size: 13px;
        }

        .dental-login .access-panel {
            background: linear-gradient(145deg, #073279, #004d98);
            color: #fff;
        }

        .dental-login .access-label {
            margin: 0 0 12px;
            color: #b8edf3;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .dental-login .access-title {
            margin: 0 0 10px;
            color: #fff;
            font-size: 29px;
            font-weight: 700;
            line-height: 1.25;
        }

        .dental-login .access-description {
            margin: 0 0 26px;
            color: #d6e7fa;
            font-size: 14px;
            line-height: 1.6;
        }

        .dental-login .login-errors {
            margin-bottom: 20px;
            padding: 12px 14px;
            border: 1px solid #fecaca;
            border-radius: 8px;
            background: #fff1f2;
            color: #991b1b;
            font-size: 13px;
            line-height: 1.6;
        }

        .dental-login .login-errors p {
            margin: 0;
        }

        .dental-login .field {
            margin-bottom: 20px;
        }

        .dental-login .field-label {
            display: block;
            margin-bottom: 8px;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
        }

        .dental-login .field-input {
            display: block;
            width: 100%;
            min-width: 0;
            height: 48px;
            padding: 12px 14px;
            border: 1px solid #d1deeb;
            border-radius: 8px;
            outline: none;
            background: #fff;
            color: #18324b;
            font: inherit;
            font-size: 14px;
        }

        .dental-login .field-input::placeholder {
            color: #77879a;
        }

        .dental-login .field-input:focus {
            border-color: #1bb5c9;
            box-shadow: 0 0 0 3px rgba(27, 181, 201, .3);
        }

        .dental-login .password-wrapper {
            position: relative;
        }

        .dental-login .password-input {
            padding-right: 86px;
        }

        .dental-login .password-button {
            position: absolute;
            top: 5px;
            right: 5px;
            height: 38px;
            padding: 0 10px;
            border: 0;
            border-radius: 5px;
            background: #edf3f8;
            color: #073279;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .dental-login .password-button:hover {
            background: #dceaf5;
        }

        .dental-login .remember {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 22px;
            color: #e1ecfa;
            font-size: 13px;
            cursor: pointer;
        }

        .dental-login .remember input {
            width: 15px;
            height: 15px;
            accent-color: #1bb5c9;
        }

        .dental-login .submit-button {
            width: 100%;
            min-height: 48px;
            padding: 12px 18px;
            border: 1px solid #1bb5c9;
            border-radius: 8px;
            background: #007d96;
            color: #fff;
            font: inherit;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background .15s;
        }

        .dental-login .submit-button:hover {
            background: #00677e;
        }

        .dental-login .admin-help {
            margin: 20px 0 0;
            color: #d6e7fa;
            font-size: 12px;
            line-height: 1.7;
        }

        .dental-login .access-footer {
            margin: 24px 0 0;
            padding-top: 18px;
            border-top: 1px solid rgba(255, 255, 255, .18);
            color: #bfd5ed;
            font-size: 11px;
            line-height: 1.7;
        }

        .dental-login button:focus-visible,
        .dental-login .remember input:focus-visible {
            outline: 3px solid #a0e5f0;
            outline-offset: 3px;
        }

        @media (max-width: 760px) {
            body.dental-login {
                padding: 16px;
            }

            .dental-login .login-card {
                max-width: 460px;
                grid-template-columns: 1fr;
            }

            .dental-login .brand-panel {
                padding: 24px 28px;
            }

            .dental-login .company-logo {
                max-width: 240px;
                margin-bottom: 14px;
            }

            .dental-login .brand-title {
                font-size: 22px;
                margin-bottom: 0;
            }

            .dental-login .brand-label,
            .dental-login .brand-description,
            .dental-login .services {
                display: none;
            }

            .dental-login .access-panel {
                padding: 30px 28px;
            }

            .dental-login .access-title {
                font-size: 26px;
            }
        }
    </style>
</head>

<body class="dental-login">

    @php
        // Corresponde a public/images/logo-dis-dental.png.
        $rutaLogoEmpresa = 'images/logo-dis-dental.png';
    @endphp

    <main class="login-card">

        {{-- IZQUIERDA: LOGO E IDENTIFICACIÓN --}}
        <aside class="brand-panel" aria-label="Laboratorio Diseño Dental">

            @if (is_file(public_path($rutaLogoEmpresa)))
                <img
                    src="{{ asset($rutaLogoEmpresa) }}"
                    alt="Diseño Dental"
                    class="company-logo"
                    width="330"
                    height="165"
                >
            @endif

            <p class="brand-label">
                Laboratorio dental
            </p>

            <h1 class="brand-title">
                Sistema de Control de Producción
            </h1>

            <p class="brand-description">
                Gestión de órdenes, seguimiento de etapas y control
                de materiales para el trabajo diario del laboratorio.
            </p>

            <ul class="services">
                <li>Órdenes y producción</li>
                <li>Inventario de materiales</li>
                <li>Trazabilidad por etapa y técnico</li>
            </ul>

        </aside>

        {{-- DERECHA: ACCESO --}}
        <section class="access-panel" aria-labelledby="login-title">

            <p class="access-label">
                Acceso al sistema
            </p>

            <h2 class="access-title" id="login-title">
                Iniciar sesión
            </h2>

            <p class="access-description">
                Ingrese las credenciales asignadas por el administrador.
            </p>

            {{-- ERRORES --}}
            @if ($errors->any())
                <div class="login-errors" role="alert">

                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach

                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">

                @csrf

                {{-- USUARIO --}}
                <div class="field">

                    <label class="field-label" for="username">
                        Usuario
                    </label>

                    <input
                        class="field-input"
                        type="text"
                        name="username"
                        id="username"
                        value="{{ old('username') }}"
                        placeholder="Ingrese su usuario"
                        required
                        autofocus
                        autocomplete="username"
                        aria-invalid="{{ $errors->has('username') ? 'true' : 'false' }}"
                    >

                </div>

                {{-- CONTRASEÑA --}}
                <div class="field">

                    <label class="field-label" for="password">
                        Contraseña
                    </label>

                    <div class="password-wrapper">

                        <input
                            class="field-input password-input"
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Ingrese su contraseña"
                            required
                            autocomplete="current-password"
                            aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                        >

                        <button
                            class="password-button"
                            type="button"
                            id="toggle-password"
                            aria-controls="password"
                            aria-label="Mostrar contraseña"
                            aria-pressed="false"
                        >
                            Mostrar
                        </button>

                    </div>
                </div>

                <label class="remember">

                    <input
                        type="checkbox"
                        name="remember"
                        @checked(old('remember'))
                    >

                    Recordarme

                </label>

                <button class="submit-button" type="submit">
                    Iniciar sesión
                </button>

            </form>

            <p class="admin-help">
                ¿Necesita restablecer su contraseña?<br>
                Solicítelo al administrador del laboratorio.
            </p>

            <p class="access-footer">
                Acceso exclusivo para personal autorizado.
            </p>

        </section>

    </main>

    <script>
        (() => {
            const input = document.getElementById('password');
            const button = document.getElementById('toggle-password');

            button.addEventListener('click', () => {
                const visible = input.type === 'password';

                input.type = visible ? 'text' : 'password';

                button.textContent = visible ? 'Ocultar' : 'Mostrar';

                button.setAttribute(
                    'aria-pressed',
                    String(visible)
                );

                button.setAttribute(
                    'aria-label',
                    visible ? 'Ocultar contraseña' : 'Mostrar contraseña'
                );
            });
        })();
    </script>

</body>
</html>