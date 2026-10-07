<svg
    xmlns="http://www.w3.org/2000/svg"
    class="w-5 h-5 shrink-0"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="1.7"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
    focusable="false"
>
    @switch($icono)
        @case('panel')
            <rect x="3" y="3" width="7" height="7" rx="1" />
            <rect x="14" y="3" width="7" height="7" rx="1" />
            <rect x="3" y="14" width="7" height="7" rx="1" />
            <rect x="14" y="14" width="7" height="7" rx="1" />
            @break

        @case('agenda')
            <rect x="3" y="5" width="18" height="16" rx="2" />
            <path d="M16 3v4M8 3v4M3 11h18M7 15h3M14 15h3" />
            @break

        @case('ordenes')
            <rect x="5" y="4" width="14" height="17" rx="2" />
            <path d="M9 4V2h6v2M9 9h6M9 13h6M9 17h4" />
            @break

        @case('produccion')
        @case('ajustes')
            <path d="m9 3 1-1h4l1 1v2l2 1 2-1 2 3-1 2v3l1 2-2 3-2-1-2 1v2l-1 1h-4l-1-1v-2l-2-1-2 1-2-3 1-2v-3L3 8l2-3 2 1 2-1Z" />
            <circle cx="12" cy="11.5" r="3" />
            @break

        @case('inventario')
            <path d="m3 7 9-4 9 4-9 4-9-4ZM3 7v10l9 4 9-4V7M12 11v10M7 5l9 4" />
            @break

        @case('garantias')
            <path d="m12 3 8 3v6c0 5-8 9-8 9s-8-4-8-9V6l8-3Z" />
            <path d="m8 12 3 3 5-6" />
            @break

        @case('pagos')
            <rect x="3" y="5" width="18" height="14" rx="2" />
            <path d="M3 10h18M7 15h3" />
            @break

        @case('mensajeria')
            <path d="M3 6h11v12H3V6ZM14 10h4l3 4v4h-7" />
            <circle cx="7" cy="18" r="2" />
            <circle cx="18" cy="18" r="2" />
            @break

        @case('recolecciones')
            <path d="M12 3v12m-4-4 4 4 4-4M4 14v6h16v-6" />
            @break

        @case('reportes')
            <path d="M4 3v18h17M8 17v-5M13 17V7M18 17V4" />
            @break

        @case('administracion')
            <circle cx="9" cy="7" r="3" />
            <path d="M3 21v-3a6 6 0 0 1 12 0v3M16 4a3 3 0 0 1 0 6M18 14a5 5 0 0 1 3 4v3" />
            @break

        @case('cerrar')
            <path d="m6 6 12 12M18 6 6 18" />
            @break

        @default
            <path d="m9 6 6 6-6 6" />
    @endswitch
</svg>