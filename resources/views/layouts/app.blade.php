<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta

        name="viewport"

        content="width=device-width, initial-scale=1.0"

    >

    <title>

        @yield('title', 'Laboratorio Dental')

    </title>

    @vite([

        'resources/css/app.css',

        'resources/js/app.js'

    ])

</head>

<body class="bg-[#f7f9ff] text-slate-900">

@php

    $rol = auth()->user()->role?->nombre;

@endphp

<div class="min-h-screen flex">

    {{-- =====================================================

         OVERLAY MÓVIL

    ====================================================== --}}

    <div

        id="sidebarOverlay"

        class="

            fixed inset-0

            bg-slate-950/50

            z-40

            hidden

            lg:hidden

        "

    ></div>

    {{-- =====================================================

         SIDEBAR

    ====================================================== --}}

    <aside

        id="sidebar"

        class="

            fixed inset-y-0 left-0 z-50

            w-64 max-w-[85vw] h-275dvh
          bg-[#eff4ff]
            border-r border-slate-200

            flex flex-col

            -translate-x-full

            transition-transform duration-300 ease-in-out

            shadow-2xl

            lg:translate-x-0

            lg:shadow-none

        ">

        {{-- =================================================

            LOGO

            ================================================== --}}

        <div

            class="h-20 flex items-center justify-between px-5 border-b border-slate-200 shrink-0">

            {{-- LOGO + NOMBRE --}}

            <div class="flex items-center min-w-0">

                <img src="{{ asset('images/logo-dis-dental.png') }}" alt="Logo Laboratorio Dental" class="h-14 w-14 object-contain shrink-0">

                <div class="ml-3 min-w-0">

                    <p class="font-bold

                            text-sm

                            text-slate-900

                            leading-tight">

                        Laboratorio Dental

                    </p>

                    <p class="text-xs

                            text-[#315875]

                            mt-1">

                        Panel de Control

                    </p>

                </div>

            </div>

            {{-- CERRAR SIDEBAR EN MÓVIL --}}

            <button

                id="closeSidebar"

                type="button"

                class="lg:hidden

                    w-9 h-9

                    flex items-center

                    justify-center

                    rounded-lg

                    text-slate-500

                    hover:bg-slate-100

                    hover:text-slate-900

                    transition

                    shrink-0"

                aria-label="Cerrar menú"

            >

                ✕

            </button>

        </div>

        {{-- =================================================

             MENÚ

        ================================================== --}}

        <nav class="flex-1 min-h-0 py-5 overflow-y-auto overscroll-contain">

            {{-- =================================================

                 ADMINISTRADOR / RECEPCIÓN

            ================================================== --}}

            @if(in_array($rol, ['Administrador', 'Recepcion']))

                {{-- DASHBOARD --}}

                <a

                    href="{{ route('dashboard') }}"

                    class="

                        flex

                        items-center

                        gap-3

                        px-6

                        py-4

                        {{ request()->routeIs('dashboard')

                            ? 'bg-[#e7eef8] border-l-4 border-[#315875] text-[#315875] font-semibold'

                            : 'text-slate-600 border-l-4 border-transparent hover:bg-slate-50 hover:text-[#182d47]'

                        }}

                        transition

                    "

                >

                @include('layouts.partials.icono', ['icono' => 'panel'])
                
                Dashboard

                </a>

                {{-- AGENDA --}}

                <a

                    href="{{ route('agenda.index') }}"

                    class="

                        flex

                        items-center

                        gap-3

                        px-6

                        py-4

                        {{ request()->routeIs('agenda.*')

                            ? 'bg-[#e7eef8] border-l-4 border-[#315875] text-[#315875] font-semibold'

                            : 'text-slate-600 border-l-4 border-transparent hover:bg-slate-50 hover:text-[#182d47]'

                        }}

                        transition

                    "

                >

                    @include('layouts.partials.icono', ['icono' => 'agenda'])   

                    Agenda

                </a>

                {{-- ÓRDENES --}}

                <a

                    href="{{ route('ordenes.index') }}"

                    class="

                        flex

                        items-center

                        gap-3

                        px-6

                        py-4

                        {{ request()->routeIs('ordenes.*')

                            ? 'bg-[#e7eef8] border-l-4 border-[#315875] text-[#315875] font-semibold'

                            : 'text-slate-600 border-l-4 border-transparent hover:bg-slate-50 hover:text-[#182d47]'

                        }}

                        transition

                    "

                >

                    @include('layouts.partials.icono', ['icono' => 'ordenes'])

                    Órdenes de Trabajo

                </a>

                {{-- PRODUCCIÓN --}}

                <a

                    href="{{ route('produccion.index') }}"

                    class="flex items-center gap-3 px-6 py-4 text-slate-600 border-l-4 border-transparent

                        hover:bg-slate-50 hover:text-[#182d47] transition"

                >

                    @include('layouts.partials.icono', ['icono' => 'produccion'])

                    Producción

                </a>

                {{-- =================================================

                     INVENTARIO

                ================================================== --}}

                <details

                    class="group"

                    {{ request()->routeIs('inventario.*') ? 'open' : '' }}

                >

                    <summary

                        class="

                            flex

                            items-center

                            justify-between

                            px-6

                            py-4

                            text-slate-600

                            hover:bg-slate-50

                            hover:text-[#182d47]

                            transition

                            cursor-pointer

                        "

                    >

                        <div class="flex items-center gap-3">

                            @include('layouts.partials.icono', ['icono' => 'inventario'])

                            Inventario

                        </div>

                        <span

                            class="

                                group-open:rotate-180

                                transition-transform

                            "

                        >

                            ▾

                        </span>

                    </summary>

                    <div

                        class="

                            bg-slate-50

                            border-y

                            border-slate-100

                        "

                    >

                        <a

                            href="{{ route('inventario.index') }}"

                            class="

                                block

                                pl-14

                                pr-6

                                py-3

                                text-sm

                                text-slate-600

                                hover:text-[#182d47]

                                hover:bg-slate-100

                            "

                        >

                            Inventario General

                        </a>

                        <a href="{{ route('inventario.tecnicos') }}" class="block pl-14 pr-6 py-3
                                text-sm
                                text-slate-600
                                hover:text-[#182d47]
                                hover:bg-slate-100
                            ">

                            Inventario por Técnico

                        </a>

                        <a href="{{ route('inventario.movimientos') }}"

                             class=" block pl-14 pr-6 py-3

                                text-sm

                                text-slate-600

                                hover:text-[#182d47]

                                hover:bg-slate-100

                            "

                        >

                            Movimientos

                        </a>

                    </div>

                </details>

                {{-- GARANTÍAS --}}

                <a

                    href="{{ route('garantias.index') }}"

                    class="

                        flex

                        items-center

                        gap-3

                        px-6

                        py-4

                        {{ request()->routeIs('garantias.*')

                            ? 'bg-[#e7eef8] border-l-4 border-[#315875] text-[#315875] font-semibold'

                            : 'text-slate-600 border-l-4 border-transparent hover:bg-slate-50 hover:text-[#182d47]'

                        }}

                        transition

                    "

                >

                    @include('layouts.partials.icono', ['icono' => 'garantias'])

                    Garantías y Devoluciones

                </a>

                {{-- =================================================
                     PAGOS
                ================================================== --}}

                <details
                    class="group"
                    {{
                        request()->routeIs('pagos.*') ||

                        request()->routeIs('cuentas-odontologos.*') ? 'open': ''
                    }}>

                    <summary

                        class="flex items-center justify-between px-6 py-4 text-slate-600

                            hover:bg-slate-50 hover:text-[#182d47] transition cursor-pointer">

                        <div class="flex items-center gap-3">

                        @include('layouts.partials.icono', ['icono' => 'pagos'])
                            Pagos y Créditos

                        </div>

                        <span

                            class="

                                group-open:rotate-180

                                transition-transform" >

                            ▾

                        </span>

                    </summary>

                    <div

                        class="bg-slate-50 border-y border-slate-100">

                        <a

                            href="{{ route('pagos.index') }}"

                            class=" block
                                pl-14
                                pr-6 py-3
                                text-sm text-slate-600 hover:text-[#182d47] hover:bg-slate-100">

                            Pagos

                        </a>

                        <a

                            href="{{ route('cuentas-odontologos.index') }}"

                            class="

                                block

                                pl-14

                                pr-6

                                py-3

                                text-sm

                                text-slate-600

                                hover:text-[#182d47]

                                hover:bg-slate-100

                            "

                        >

                            Cuentas de Odontólogos

                        </a>

                    </div>

                </details>

                {{-- =================================================

                     MENSAJERÍA

                ================================================== --}}

                <details

                    class="group"

                    {{ request()->routeIs('mensajeria.*') ? 'open' : '' }}

                >

                    <summary

                        class="

                            flex

                            items-center

                            justify-between

                            px-6

                            py-4

                            text-slate-600

                            hover:bg-slate-50

                            hover:text-[#182d47]

                            transition

                            cursor-pointer

                        "

                    >

                        <div class="flex items-center gap-3">

                        @include('layouts.partials.icono', ['icono' => 'mensajeria'])
                            Mensajería

                        </div>

                        <span

                            class="

                                group-open:rotate-180

                                transition-transform

                            "

                        >

                            ▾

                        </span>

                    </summary>

                    <div

                        class="

                            bg-slate-50

                            border-y

                            border-slate-100

                        "

                    >

                        <a

                            href="{{ route('mensajeria.index') }}"

                            class="

                                block

                                pl-14

                                pr-6

                                py-3

                                text-sm

                                {{ request()->routeIs('mensajeria.index')

                                    ? 'text-[#315875] font-semibold bg-[#e7eef8]'

                                    : 'text-slate-600 hover:text-[#182d47] hover:bg-slate-100'

                                }}

                            "

                        >

                            Rutas

                        </a>

                        <a

                            href="{{ route('mensajeria.entregas') }}"

                            class="

                                block

                                pl-14

                                pr-6

                                py-3

                                text-sm

                                {{ request()->routeIs('mensajeria.entregas*')

                                    ? 'text-[#315875] font-semibold bg-[#e7eef8]'

                                    : 'text-slate-600 hover:text-[#182d47] hover:bg-slate-100'

                                }}

                            "

                        >

                            Entregas

                        </a>

                        <a

                            href="{{ route('mensajeria.recolecciones') }}"

                            class="

                                block

                                pl-14

                                pr-6

                                py-3

                                text-sm

                                {{ request()->routeIs('mensajeria.recolecciones*')

                                    ? 'text-[#315875] font-semibold bg-[#e7eef8]'

                                    : 'text-slate-600 hover:text-[#182d47] hover:bg-slate-100'

                                }}

                            "

                        >

                            Recolecciones

                        </a>

                    </div>

                </details>

                {{-- REPORTES --}}

                <a href="{{ route('reportes.index') }}" class="flex items-center gap-3 px-6 py-4

                        {{ request()->routeIs('reportes.*')

                            ? 'bg-[#e7eef8] border-l-4 border-[#315875] text-[#315875] font-semibold'

                            : 'text-slate-600 border-l-4 border-transparent hover:bg-slate-50 hover:text-[#182d47]'

                        }} transition" >

                    @include('layouts.partials.icono', ['icono' => 'reportes'])

                    Reportes

                </a>

            {{-- =================================================

                SOLO ADMINISTRADOR

            ================================================== --}}

                @include('layouts.partials.administracion')

            {{-- =================================================

                 MENSAJERO

            ================================================== --}}

            @elseif($rol === 'Mensajero')

                <p

                    class="

                        px-6

                        mb-2

                        text-xs

                        font-semibold

                        text-slate-400

                        uppercase

                        tracking-widest

                    "

                >

                    Mensajería

                </p>

                {{-- MIS RUTAS --}}

                <a

                    href="{{ route('mensajeria.index') }}"

                    class="

                        flex

                        items-center

                        gap-3

                        px-6

                        py-4

                        {{ request()->routeIs('mensajeria.index')

                            || request()->routeIs('mensajeria.show')

                            ? 'bg-[#e7eef8] border-l-4 border-[#315875] text-[#315875] font-semibold'

                            : 'text-slate-600 border-l-4 border-transparent hover:bg-slate-50 hover:text-[#182d47]'

                        }}

                        transition

                    "

                >

                @include('layouts.partials.icono', ['icono' => 'mensajeria'])
                    Mis rutas

                </a>
 
                {{-- MIS ENTREGAS --}}

                <a href="{{ route('mensajeria.entregas') }}"
                    class="
                        flex
                        items-center
                        gap-3
                        px-6
                        py-4

                        {{ request()->routeIs('mensajeria.entregas*')

                            ? 'bg-[#e7eef8] border-l-4 border-[#315875] text-[#315875] font-semibold'

                            : 'text-slate-600 border-l-4 border-transparent hover:bg-slate-50 hover:text-[#182d47]'

                        }}

                        transition

                    "

                >

                @include('layouts.partials.icono', ['icono' => 'mensajeria'])
                    Mis entregas

                </a>

                {{-- MIS RECOLECCIONES --}}

                <a

                    href="{{ route('mensajeria.recolecciones') }}"

                    class="

                        flex

                        items-center

                        gap-3

                        px-6

                        py-4

                        {{ request()->routeIs('mensajeria.recolecciones*')

                            ? 'bg-[#e7eef8] border-l-4 border-[#315875] text-[#315875] font-semibold'

                            : 'text-slate-600 border-l-4 border-transparent hover:bg-slate-50 hover:text-[#182d47]'

                        }}

                        transition

                    "

                >

                @include('layouts.partials.icono', ['icono' => 'recolecciones'])
                    Mis recolecciones

                </a>

            {{-- =================================================

                 TÉCNICO

            ================================================== --}}

            @elseif($rol === 'Técnico')

                <p

                    class="

                        px-6

                        mb-2

                        text-xs

                        font-semibold

                        text-slate-400

                        uppercase

                        tracking-widest

                    "

                >

                    Mi trabajo

                </p>

                {{-- AGENDA --}}

                <a

                    href="{{ route('agenda.index') }}"

                    class="

                        flex

                        items-center

                        gap-3

                        px-6

                        py-4

                        {{ request()->routeIs('agenda.*')

                            ? 'bg-[#e7eef8] border-l-4 border-[#315875] text-[#315875] font-semibold'

                            : 'text-slate-600 border-l-4 border-transparent hover:bg-slate-50 hover:text-[#182d47]'

                        }}

                        transition

                    "

                >

                    @include('layouts.partials.icono', ['icono' => 'agenda'])

                    Agenda de hoy

                </a>

                {{-- MIS ÓRDENES --}}

                <a

                    href="{{ route('ordenes.index') }}"

                    class="

                        flex

                        items-center

                        gap-3

                        px-6

                        py-4

                        {{ request()->routeIs('ordenes.*')

                            ? 'bg-[#e7eef8] border-l-4 border-[#315875] text-[#315875] font-semibold'

                            : 'text-slate-600 border-l-4 border-transparent hover:bg-slate-50 hover:text-[#182d47]'

                        }}

                        transition

                    "

                >

                    @include('layouts.partials.icono', ['icono' => 'ordenes'])

                    Mis órdenes

                </a>

                {{-- MI INVENTARIO --}}

                <a

                    href="{{ route('inventario.index') }}"

                    class="

                        flex

                        items-center

                        gap-3

                        px-6

                        py-4

                        {{ request()->routeIs('inventario.*')

                            ? 'bg-[#e7eef8] border-l-4 border-[#315875] text-[#315875] font-semibold'

                            : 'text-slate-600 border-l-4 border-transparent hover:bg-slate-50 hover:text-[#182d47]'

                        }}

                        transition

                    "

                >

                    @include('layouts.partials.icono', ['icono' => 'inventario'])

                    Mi inventario

                </a>

            @endif

        </nav>

        {{-- =================================================
             AJUSTES
        ================================================== --}}
      
        @include('layouts.partials.ajustes')

    </aside>

    {{-- =====================================================
         CONTENIDO PRINCIPAL
    ====================================================== --}}

    <div class="flex-1 min-w-0 w-full lg:ml-64">

        {{-- =================================================

             HEADER

        ================================================== --}}

        <header

            class="

                h-20

                bg-white

                border-b

                border-slate-200

                flex

                items-center

                justify-between

                px-4

                sm:px-6

                lg:px-8

                sticky

                top-0

                z-30

            "

        >

            {{-- IZQUIERDA --}}

            <div

                class=" flex items-center gap-3 min-w-0">

                {{-- BOTÓN MENÚ MÓVIL --}}

                <button id="openSidebar" type="button" class=" lg:hidden w-10 h-10 shrink-0 flex items-center justify-center rounded-xl border

                        border-slate-200

                        bg-white

                        text-slate-700 hover:bg-slate-50 transition" aria-label="Abrir menú"

                >

                    <svg

                        xmlns="http://www.w3.org/2000/svg"

                        viewBox="0 0 24 24"

                        fill="none"

                        stroke="currentColor"

                        stroke-width="2"

                        class="w-5 h-5"

                    >

                        <path

                            stroke-linecap="round"

                            d="M4 6h16M4 12h16M4 18h16"

                        />

                    </svg>

                </button>

                {{-- TÍTULO --}}

                <h2

                    class="

                        font-bold

                        text-sm

                        sm:text-base

                        lg:text-lg

                        truncate

                    "

                >

                    Sistema de Control de Producción

                    <span class="hidden lg:inline">

                        — Laboratorio Dental

                    </span>

                </h2>

            </div>

            {{-- DERECHA --}}

            <div

                class="

                    flex

                    items-center

                    gap-2

                    sm:gap-4

                    ml-4

                    shrink-0

                "

            >

                {{-- ROL --}}

                <span

                    class="

                        hidden

                        sm:inline-flex

                        bg-slate-100

                        text-[#315875]

                        px-3

                        py-1

                        rounded-full

                        text-xs

                        font-bold

                    "

                >

                    {{ strtoupper($rol ?? 'Usuario') }}

                </span>

                {{-- INICIAL --}}

                <div

                    class="

                        w-9

                        h-9

                        rounded-full

                        border

                        border-slate-300

                        flex

                        items-center

                        justify-center

                        text-sm

                        font-medium

                        bg-white

                    "

                >

                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}

                </div>

                {{-- LOGOUT --}}

                <form

                    method="POST"

                    action="{{ route('logout') }}"

                >

                    @csrf

                    <button

                        type="submit"

                        class="

                            sm:border-l

                            sm:border-slate-200

                            sm:pl-4

                            text-sm

                            font-medium

                            text-slate-700

                            hover:text-red-600

                            transition

                        "

                    >

                        <span class="hidden sm:inline">

                            Cerrar Sesión

                        </span>

                        <span class="sm:hidden">

                            Salir

                        </span>

                    </button>

                </form>

            </div>

        </header>

        {{-- =================================================

             CONTENIDO VARIABLE

        ================================================== --}}

        <main class="p-4 sm:p-6 lg:p-8">

            @yield('content')

        </main>

    </div>

</div>

    {{-- =========================================================

        JAVASCRIPT SIDEBAR RESPONSIVE

    ========================================================= --}}

        <script>

            document.addEventListener(

                'DOMContentLoaded',

                function () {

                    const sidebar =

                        document.getElementById('sidebar');

                    const overlay =

                        document.getElementById('sidebarOverlay');

                    const openButton =

                        document.getElementById('openSidebar');

                    const closeButton =

                        document.getElementById('closeSidebar');

                    function abrirSidebar() {

                        sidebar?.classList.remove(

                            '-translate-x-full'

                        );

                        overlay?.classList.remove(

                            'hidden'

                        );

                        document.body.classList.add(

                            'overflow-hidden'

                        );

                    }

                    function cerrarSidebar() {

                        sidebar?.classList.add(

                            '-translate-x-full'

                        );

                        overlay?.classList.add(

                            'hidden'

                        );

                        document.body.classList.remove(

                            'overflow-hidden'

                        );

                    }

                    openButton?.addEventListener(

                        'click',

                        abrirSidebar

                    );

                    closeButton?.addEventListener(

                        'click',

                        cerrarSidebar

                    );

                    overlay?.addEventListener(
                        'click',
                        cerrarSidebar

                    );

                    /*

                    * En móvil, al seleccionar una opción,

                    * cerramos automáticamente el menú.

                    */

                    sidebar

                        ?.querySelectorAll('a')

                        .forEach(function (link) {

                            link.addEventListener(

                                'click',

                                function () {

                                    if (

                                        window.innerWidth < 1024

                                    ) {

                                        cerrarSidebar();

                                    }

                                }

                            );

                        });

                    /*

                    * Si el usuario cambia nuevamente

                    * a tamaño escritorio.

                    */

                    window.addEventListener('resize', function () {
                        if (window.innerWidth >= 1024) {
                            cerrarSidebar();
                        }
                    });

                    /*

                    * ESC también cierra el menú.

                    */

                    document.addEventListener(

                        'keydown',

                        function (event) {

                            if (

                                event.key === 'Escape' &&

                                window.innerWidth < 1024

                            ) {

                                cerrarSidebar();

                            }

                        }

                    );

                }

            );
            window.addEventListener('pageshow', function (event) {
                if (event.persisted) {
                    document.documentElement.style.visibility = 'hidden';
                    window.location.reload();
                }
            });

        </script>

    </body>

</html>
