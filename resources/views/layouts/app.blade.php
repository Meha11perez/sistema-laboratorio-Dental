<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Laboratorio Dental')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-900">

<div class="min-h-screen flex">

    {{-- SIDEBAR --}}
 <aside class="w-72 bg-white border-r border-slate-200 hidden md:flex flex-col">

    {{-- LOGO --}}
    <div class="h-20 flex items-center px-6 border-b border-slate-200">

        <div>
            <h1 class="font-bold text-sm text-slate-900">
                LABORATORIO DENTAL
            </h1>

            <p class="text-xs text-blue-900 tracking-widest">
                PANEL DE CONTROL
            </p>
        </div>

    </div>


    {{-- MENÚ --}}
    <nav class="flex-1 py-5 overflow-y-auto">

        {{-- DASHBOARD --}}
        <a
            href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-6 py-4
                   bg-blue-50 border-l-4 border-blue-800
                   text-blue-900 font-semibold"
        >
            <span>▦</span>
            Dashboard
        </a>


        {{-- AGENDA --}}
        <a
            href="{{ route('agenda.index') }}"
            class="flex items-center gap-3 px-6 py-4
                   text-slate-600 hover:bg-slate-50
                   hover:text-blue-900 transition"
        >
            <span>📅</span>
            Agenda
        </a>


        {{-- ÓRDENES --}}
        <a
            href="{{ route('ordenes.index') }}"
            class="flex items-center gap-3 px-6 py-4
                   text-slate-600 hover:bg-slate-50
                   hover:text-blue-900 transition"
        >
            <span>📋</span>
            Órdenes de Trabajo
        </a>


        {{-- PRODUCCIÓN --}}
        <a
            href="#"
            class="flex items-center gap-3 px-6 py-4
                   text-slate-600 hover:bg-slate-50
                   hover:text-blue-900 transition"
        >
            <span>⚙️</span>
            Producción
        </a>


        {{-- INVENTARIO --}}
        <details class="group">

            <summary
                class="flex items-center justify-between px-6 py-4
                       text-slate-600 hover:bg-slate-50
                       hover:text-blue-900 transition cursor-pointer"
            >

                <div class="flex items-center gap-3">
                    <span>📦</span>
                    Inventario
                </div>

                <span class="group-open:rotate-180 transition">
                    ▾
                </span>

            </summary>

            <div class="bg-slate-50 border-y border-slate-100">

                <a
                    href="{{ route('inventario.index') }}"
                    class="block pl-14 pr-6 py-3 text-sm
                           text-slate-600 hover:text-blue-900
                           hover:bg-slate-100"
                >
                    Inventario General
                </a>

                <a
                    href="#"
                    class="block pl-14 pr-6 py-3 text-sm
                           text-slate-600 hover:text-blue-900
                           hover:bg-slate-100"
                >
                    Inventario por Técnico
                </a>

                <a
                    href="#"
                    class="block pl-14 pr-6 py-3 text-sm
                           text-slate-600 hover:text-blue-900
                           hover:bg-slate-100"
                >
                    Materiales
                </a>

                <a
                    href="#"
                    class="block pl-14 pr-6 py-3 text-sm
                           text-slate-600 hover:text-blue-900
                           hover:bg-slate-100"
                >
                    Movimientos
                </a>

            </div>

        </details>


        {{-- GARANTÍAS --}}
        <a
            href="{{ route('garantias.index') }}"
            class="flex items-center gap-3 px-6 py-4
                   text-slate-600 hover:bg-slate-50
                   hover:text-blue-900 transition"
        >
            <span>↩️</span>
            Garantías y Devoluciones
        </a>


           {{-- PAGOS --}}
        <details class="group">

            <summary
                class="flex items-center justify-between px-6 py-4
                    text-slate-600 hover:bg-slate-50
                    hover:text-blue-900 transition cursor-pointer"
            >

                <div class="flex items-center gap-3">
                    <span>💰</span>
                    Pagos y Créditos
                </div>

                <span class="group-open:rotate-180 transition">
                    ▾
                </span>

            </summary>

            <div class="bg-slate-50 border-y border-slate-100">

                <a
                    href="{{ route('pagos.index') }}"
                    class="block pl-14 pr-6 py-3 text-sm
                        text-slate-600 hover:text-blue-900
                        hover:bg-slate-100"
                >
                    Pagos
                </a>

                <a
                    href="{{ route('cuentas-odontologos.index') }}"
                    class="block pl-14 pr-6 py-3 text-sm
                        text-slate-600 hover:text-blue-900
                        hover:bg-slate-100"
                >
                    Cuentas de Odontólogos
                </a>

            </div>

        </details>


        {{-- MENSAJERÍA --}}
        <details class="group">

            <summary
                class="flex items-center justify-between px-6 py-4
                       text-slate-600 hover:bg-slate-50
                       hover:text-blue-900 transition cursor-pointer"
            >

                <div class="flex items-center gap-3">
                    <span>🚚</span>
                    Mensajería
                </div>

                <span class="group-open:rotate-180 transition">
                    ▾
                </span>

            </summary>

            <div class="bg-slate-50 border-y border-slate-100">

                <a
                    href="{{ route('mensajeria.index') }}"
                    class="block pl-14 pr-6 py-3 text-sm
                           text-slate-600 hover:text-blue-900
                           hover:bg-slate-100"
                >
                    Rutas
                </a>

                <a
                    href="#"
                    class="block pl-14 pr-6 py-3 text-sm
                           text-slate-600 hover:text-blue-900
                           hover:bg-slate-100"
                >
                    Entregas
                </a>

                <a
                    href=""
                class="block pl-14 pr-6 py-3 text-sm
                           text-slate-600 hover:text-blue-900
                           hover:bg-slate-100"
                >
                    Recolecciones
                </a>

            </div>

        </details>


        {{-- REPORTES --}}
        <a
            href="{{ route('reportes.index') }}"
            class="flex items-center gap-3 px-6 py-4
                   text-slate-600 hover:bg-slate-50
                   hover:text-blue-900 transition"
        >
            <span>📊</span>
            Reportes
        </a>


        {{-- SOLO ADMINISTRADOR --}}
        @if(auth()->user()->role?->nombre === 'Administrador')

            <div class="mt-4 pt-4 border-t border-slate-200">

                <p class="px-6 mb-2 text-xs font-semibold text-slate-400 uppercase tracking-widest">
                    Administración
                </p>


                <details class="group">

                    <summary
                        class="flex items-center justify-between px-6 py-4
                               text-slate-600 hover:bg-slate-50
                               hover:text-blue-900 transition cursor-pointer"
                    >

                        <div class="flex items-center gap-3">
                            <span>👥</span>
                            Administración
                        </div>

                        <span class="group-open:rotate-180 transition">
                            ▾
                        </span>

                    </summary>


                    <div class="bg-slate-50 border-y border-slate-100">

                        <a
                            href="{{ route('administracion.index') }}"
                            class="block pl-14 pr-6 py-3 text-sm
                                   text-slate-600 hover:text-blue-900
                                   hover:bg-slate-100"
                        >
                            Usuarios
                        </a>

                        <a
                            href="#"
                            class="block pl-14 pr-6 py-3 text-sm
                                   text-slate-600 hover:text-blue-900
                                   hover:bg-slate-100"
                        >
                            Roles
                        </a>

                        <a
                            href="#"
                            class="block pl-14 pr-6 py-3 text-sm
                                   text-slate-600 hover:text-blue-900
                                   hover:bg-slate-100"
                        >
                            Técnicos
                        </a>

                        <a
                            href="#"
                            class="block pl-14 pr-6 py-3 text-sm
                                   text-slate-600 hover:text-blue-900
                                   hover:bg-slate-100"
                        >
                            Odontólogos
                        </a>

                        <a
                            href="#"
                            class="block pl-14 pr-6 py-3 text-sm
                                   text-slate-600 hover:text-blue-900
                                   hover:bg-slate-100"
                        >
                            Clínicas
                        </a>

                        <a
                            href="#"
                            class="block pl-14 pr-6 py-3 text-sm
                                   text-slate-600 hover:text-blue-900
                                   hover:bg-slate-100"
                        >
                            Pacientes
                        </a>

                    </div>

                </details>

            </div>

        @endif

    </nav>


    {{-- AJUSTES --}}
    <div class="border-t border-slate-200">

        <a
            href="#"
            class="flex items-center gap-3 px-6 py-4
                   text-slate-600 hover:bg-slate-50
                   hover:text-blue-900"
        >
            <span>⚙️</span>
            Ajustes
        </a>

    </div>

</aside>


    {{-- CONTENIDO --}}
    <div class="flex-1 min-w-0">

        {{-- HEADER --}}
        <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-6 lg:px-8">

            <h2 class="font-bold text-lg hidden lg:block">
                Sistema de Control de Producción — Laboratorio Dental
            </h2>

            <div class="flex items-center gap-5 ml-auto">

                <span class="bg-slate-100 text-blue-900 px-4 py-1 rounded-full text-xs font-bold">
                    {{ strtoupper(auth()->user()->role?->nombre ?? 'Usuario') }}
                </span>

                <button class="text-slate-500">
                    
                </button>

                <div class="w-9 h-9 rounded-full border border-slate-300 flex items-center justify-center">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="border-l border-slate-200 pl-5 text-sm font-medium text-slate-700 hover:text-red-600"
                    >
                        Cerrar Sesión
                    </button>
                </form>

            </div>

        </header>


        {{-- CONTENIDO VARIABLE --}}
        <main class="p-6 lg:p-8">

            @yield('content')

        </main>

    </div>

</div>

</body>
</html>