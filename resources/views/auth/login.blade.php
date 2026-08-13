<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Iniciar sesión | Laboratorio Dental</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-stone-100 flex items-center justify-center px-4 py-8">

    <div class="w-full max-w-5xl bg-white rounded-3xl shadow-2xl overflow-hidden grid md:grid-cols-2">

        {{-- =========================================================
             PANEL IZQUIERDO - LOGIN
        ========================================================== --}}
        <section class="p-8 md:p-12 flex flex-col justify-center">

            <div class="text-center mb-8">

                {{-- LOGO --}}
                <div class="mx-auto w-14 h-14 bg-teal-700 text-white rounded-2xl flex items-center justify-center mb-4">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        class="w-8 h-8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 3c-1.6 0-2.5-.8-4-.8C4.8 2.2 3 4.9 3 8c0 3.7 1.4 5.9 2.2 8.8.5 1.7.8 4.2 2.2 4.2 1.7 0 1.7-4.8 4.6-4.8s2.9 4.8 4.6 4.8c1.4 0 1.7-2.5 2.2-4.2C19.6 13.9 21 11.7 21 8c0-3.1-1.8-5.8-5-5.8-1.5 0-2.4.8-4 .8Z"
                        />
                    </svg>

                </div>

                <p class="text-xs tracking-[0.3em] text-slate-400 mb-2">
                    BIENVENIDO
                </p>

                <h1 class="text-2xl md:text-3xl font-bold text-teal-800">
                    Laboratorio Dental
                </h1>

                <p class="text-sm text-slate-500 mt-2">
                    Ingrese sus credenciales para acceder al sistema.
                </p>

            </div>


            {{-- ERRORES --}}
            @if ($errors->any())

                <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">

                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach

                </div>

            @endif


            <form method="POST" action="{{ route('login') }}" class="space-y-4">

                @csrf


                {{-- USUARIO --}}
                <div>

                    <label
                        for="email"
                        class="block text-sm font-semibold text-slate-700 mb-2"
                    >
                        Usuario
                    </label>

                    <div class="relative">

                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.7"
                                stroke="currentColor"
                                class="w-5 h-5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.118a7.5 7.5 0 0 1 15 0"
                                />
                            </svg>

                        </span>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="usuario@laboratorio.com"
                            required
                            autofocus
                            autocomplete="email"
                            class="w-full border border-slate-300 rounded-full
                                   pl-11 pr-4 py-3 text-sm
                                   outline-none
                                   focus:border-teal-600
                                   focus:ring-2 focus:ring-teal-100"
                        >

                    </div>

                </div>


                {{-- CONTRASEÑA --}}
                <div>

                    <label
                        for="password"
                        class="block text-sm font-semibold text-slate-700 mb-2"
                    >
                        Contraseña
                    </label>

                    <div class="relative">

                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.7"
                                stroke="currentColor"
                                class="w-5 h-5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M16.5 10.5V7.5a4.5 4.5 0 0 0-9 0v3m-1.5 0h12v9h-12v-9Z"
                                />
                            </svg>

                        </span>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                            class="w-full border border-slate-300 rounded-full
                                   pl-11 pr-12 py-3 text-sm
                                   outline-none
                                   focus:border-teal-600
                                   focus:ring-2 focus:ring-teal-100"
                        >

                        <button
                            type="button"
                            onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-teal-700"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.7"
                                stroke="currentColor"
                                class="w-5 h-5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M2.25 12s3.75-6 9.75-6 9.75 6 9.75 6-3.75 6-9.75 6S2.25 12 2.25 12Z"
                                />
                                <circle cx="12" cy="12" r="2.5"/>
                            </svg>
                        </button>

                    </div>

                </div>


                {{-- OPCIONES --}}
                <div class="flex items-center justify-between text-sm pt-1">

                    <label class="flex items-center gap-2 text-slate-600">

                        <input
                            type="checkbox"
                            name="remember"
                            class="rounded border-slate-300 text-teal-600 focus:ring-teal-600"
                        >

                        Recordarme

                    </label>

                    @if (Route::has('password.request'))

                        <a
                            href="{{ route('password.request') }}"
                            class="text-teal-700 hover:underline"
                        >
                            ¿Olvidó su contraseña?
                        </a>

                    @endif

                </div>


                {{-- BOTÓN --}}
                <button
                    type="submit"
                    class="w-full bg-teal-600 hover:bg-teal-700
                           text-white font-semibold py-3 rounded-full
                           transition shadow-md"
                >
                    INICIAR SESIÓN
                </button>

            </form>


            <div class="text-center mt-7">

                <p class="text-xs text-slate-400">
                    Sistema de Control de Producción
                </p>

                <p class="text-xs text-slate-400 mt-1">
                    Acceso exclusivo para personal autorizado
                </p>

            </div>

        </section>


        {{-- =========================================================
             PANEL DERECHO
        ========================================================== --}}
        <section
            class="relative hidden md:flex
                   bg-gradient-to-br from-teal-700 via-teal-600 to-cyan-700
                   text-white p-12
                   flex-col items-center justify-center
                   text-center overflow-hidden"
        >

            {{-- FORMAS DECORATIVAS --}}
            <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full bg-white/10"></div>

            <div class="absolute -bottom-24 -left-16 w-72 h-72 rounded-full bg-black/10"></div>

            <div class="relative z-10 max-w-sm">

                {{-- ICONO GRANDE --}}
                <div
                    class="mx-auto w-24 h-24 rounded-full
                           bg-white/10 border border-white/20
                           flex items-center justify-center mb-7"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.5"
                        class="w-14 h-14"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 3c-1.6 0-2.5-.8-4-.8C4.8 2.2 3 4.9 3 8c0 3.7 1.4 5.9 2.2 8.8.5 1.7.8 4.2 2.2 4.2 1.7 0 1.7-4.8 4.6-4.8s2.9 4.8 4.6 4.8c1.4 0 1.7-2.5 2.2-4.2C19.6 13.9 21 11.7 21 8c0-3.1-1.8-5.8-5-5.8-1.5 0-2.4.8-4 .8Z"
                        />
                    </svg>

                </div>

                <p class="text-xs tracking-[0.35em] text-teal-100 mb-3">
                    SISTEMA INTEGRAL
                </p>

                <h2 class="text-4xl font-bold mb-5">
                    Laboratorio Dental
                </h2>

                <p class="text-teal-50 leading-7">
                    Control, trazabilidad y seguimiento de la producción
                    de piezas dentales en una sola plataforma.
                </p>


                <div class="grid grid-cols-3 gap-4 mt-10">

                    <div>
                        <div class="w-10 h-10 mx-auto bg-white/10 rounded-full flex items-center justify-center mb-2">
                            ✓
                        </div>

                        <p class="text-xs">
                            Producción
                        </p>
                    </div>

                    <div>
                        <div class="w-10 h-10 mx-auto bg-white/10 rounded-full flex items-center justify-center mb-2">
                            ✓
                        </div>

                        <p class="text-xs">
                            Inventario
                        </p>
                    </div>

                    <div>
                        <div class="w-10 h-10 mx-auto bg-white/10 rounded-full flex items-center justify-center mb-2">
                            ✓
                        </div>

                        <p class="text-xs">
                            Trazabilidad
                        </p>
                    </div>

                </div>

            </div>

        </section>

    </div>


    <script>
        function togglePassword() {
            const password = document.getElementById('password');

            password.type =
                password.type === 'password'
                    ? 'text'
                    : 'password';
        }
    </script>

</body>
</html>