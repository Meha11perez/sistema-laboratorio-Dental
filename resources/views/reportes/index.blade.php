@extends('layouts.app')
@section('title', 'Reportes | Laboratorio Dental')
@section('content')
@include('reportes.partials.estilos')

<style>
    .reportes-menu { --rm-blue:#365b7c; --rm-cyan:#267f90; }
    .reportes-menu .rm-header { display:flex; align-items:center; justify-content:space-between; gap:24px; padding:28px 30px; margin-bottom:24px; border:1px solid #d6e5ec; border-left:4px solid #4095aa; border-radius:18px; background:linear-gradient(115deg,#fff 20%,#edf7f9 100%); }
    .reportes-menu .rm-eyebrow { color:var(--rm-cyan); font-size:11px; font-weight:700; letter-spacing:.13em; text-transform:uppercase; margin-bottom:8px; }
    .reportes-menu .rm-header h1 { color:#28455e; font-size:32px; font-weight:650; letter-spacing:-.7px; }
    .reportes-menu .rm-description { color:#607487; font-size:14px; line-height:1.7; margin-top:8px; max-width:560px; }
    .reportes-menu .rm-header-mark { display:flex; align-items:flex-end; gap:9px; width:92px; height:72px; padding:12px 15px; border-bottom:2px solid #adcbd6; flex-shrink:0; }
    .reportes-menu .rm-header-mark span { width:14px; border-radius:4px 4px 0 0; }
    .reportes-menu .rm-header-mark span:nth-child(1) { height:22px; background:#a5ccd7; }
    .reportes-menu .rm-header-mark span:nth-child(2) { height:34px; background:#6ca6bb; }
    .reportes-menu .rm-header-mark span:nth-child(3) { height:46px; background:#365b7c; }
    .reportes-menu .rm-section-label { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; margin-bottom:14px; }
    .reportes-menu .rm-section-label h2 { font-size:16px; color:#334d63; font-weight:600; }
    .reportes-menu .rm-section-label p { font-size:12px; color:#697d8f; }
    .reportes-menu .rm-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:18px; }
    .reportes-menu .rm-card { display:flex; flex-direction:column; min-width:0; padding:23px; border:1px solid #dce6ee; border-radius:15px; background:#fff; box-shadow:0 3px 12px rgba(42,69,91,.035); }
    .reportes-menu .rm-card-active { border-color:#9fbfce; background:linear-gradient(145deg,#f2f9fb,#fff 70%); box-shadow:0 4px 16px rgba(42,89,112,.065); }
    .reportes-menu .rm-card-top { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:20px; }
    .reportes-menu .rm-icon { display:flex; align-items:center; justify-content:center; flex-shrink:0; width:46px; height:46px; border:1px solid #dfebf1; border-radius:13px; color:var(--rm-blue); background:#f1f6fa; }
    .reportes-menu .rm-card-active .rm-icon { background:#e1f0f4; border-color:#c4e0e8; color:#267589; }
    .reportes-menu .rm-icon svg { width:24px; height:24px; flex-shrink:0; }
    .reportes-menu .rm-status { font-size:10px; font-weight:600; line-height:1.4; padding:5px 9px; border-radius:20px; color:#637487; background:#f0f4f7; white-space:nowrap; }
    .reportes-menu .rm-status-active { color:#276d7d; background:#e0f1f3; border:1px solid #c5e3e7; }
    .reportes-menu .rm-category { color:var(--rm-cyan); font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; margin-bottom:7px; }
    .reportes-menu .rm-card h3 { margin:0; color:#2f4960; font-size:18px; font-weight:600; line-height:1.4; letter-spacing:-.2px; }
    .reportes-menu .rm-card-description { font-size:13px; color:#617487; line-height:1.75; margin-top:10px; margin-bottom:22px; }
    .reportes-menu .rm-card-footer { margin-top:auto; padding-top:16px; border-top:1px solid #e6edf2; }
    .reportes-menu .rm-open { display:flex; align-items:center; justify-content:space-between; gap:12px; min-height:42px; padding:10px 13px; border-radius:9px; background:var(--rm-blue); color:#fff; font-size:12px; font-weight:600; transition:background .15s ease; }
    .reportes-menu .rm-open:hover { background:#294c6a; }
    .reportes-menu .rm-open svg { width:17px; height:17px; flex-shrink:0; }
    .reportes-menu .rm-pending-text { display:flex; align-items:center; min-height:42px; color:#6b7d8f; font-size:12px; }
    @media (max-width:1100px) { .reportes-menu .rm-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media (max-width:640px) {
        .reportes-menu .rm-header { padding:22px; margin-bottom:20px; }
        .reportes-menu .rm-header h1 { font-size:27px; }
        .reportes-menu .rm-header-mark { display:none; }
        .reportes-menu .rm-grid { grid-template-columns:1fr; gap:14px; }
        .reportes-menu .rm-card { padding:21px; }
    }
    .reportes-menu .rm-summary { display:flex; align-items:center; justify-content:space-between; gap:20px; padding:21px 24px; margin-bottom:24px; border:1px solid #b7d5df; border-radius:15px; background:#eaf5f8; }
    .reportes-menu .rm-summary h2 { font-size:20px; color:#2f4960; font-weight:600; margin-top:4px; }
    .reportes-menu .rm-summary .rm-open { min-width:190px; flex-shrink:0; }
    @media(max-width:640px) { .reportes-menu .rm-summary { align-items:stretch; flex-direction:column; padding:21px; gap:15px; } }
</style>

<div class="reportes-lab reportes-menu">
    <header class="rm-header">
        <div>
            <p class="rm-eyebrow">Diseño Dental · Análisis del laboratorio</p>
            <h1>Reportes</h1>
            <p class="rm-description">Consulta la producción, los cobros y el seguimiento de tu laboratorio.</p>
        </div>
        <div class="rm-header-mark" aria-hidden="true"><span></span><span></span><span></span></div>
    </header>

    <article class="rm-summary">
        <div>
            <p class="rm-category">Consulta mensual por cliente</p>
            <h2>Órdenes e ingresos por odontólogo</h2>
            <p class="rm-description">Consulta cuántas órdenes registró cada doctor, el valor de sus trabajos, sus abonos del mes y el saldo actual.</p>
        </div>
        <a href="{{ route('reportes.odontologos') }}" class="rm-open">
            Consultar por odontólogo
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6" /></svg>
        </a>
    </article>

    <section aria-labelledby="consultas-laboratorio">
        <div class="rm-section-label">
            <h2 id="consultas-laboratorio">Consultas del laboratorio</h2>
            <p>Selecciona el reporte que necesitas consultar.</p>
        </div>
        <div class="rm-grid">
            @foreach([
                ['Pacientes', 'Pacientes y sus órdenes', 'Resumen por paciente y detalle de trabajos, con filtros por ingreso, odontólogo y estado.', 'reportes.pacientes', 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8M16 11h6M19 8v6'],
                ['Producción', 'Producción y trazabilidad', 'Órdenes por período, área, prótesis, odontólogo y estado, con el historial de etapas.', 'reportes.produccion', 'M9 4H6a2 2 0 0 0-2 2v14h16V6a2 2 0 0 0-2-2h-3M9 3h6v4H9zM8 12h8M8 16h5'],
                ['Técnicos', 'Producción por técnico', 'Participación de cada técnico, órdenes trabajadas y etapas registradas.', 'reportes.tecnicos', 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75'],
                ['Inventario', 'Materiales e inventario', 'Existencias actuales, materiales por reponer, asignaciones y movimientos del período.', 'reportes.inventario', 'M12 3 3 7.5v9L12 21l9-4.5v-9L12 3zM3 7.5l9 4.5 9-4.5M12 12v9M7.5 5.25l9 4.5'],
                ['Calidad', 'Garantías y devoluciones', 'Garantías, motivos de devolución y órdenes de repetición con su seguimiento.', 'reportes.calidad', 'M12 3 4 6v6c0 5 8 9 8 9s8-4 8-9V6l-8-3zM8 12l3 3 5-6'],
                ['Pagos', 'Pagos y créditos', 'Cobros del período y saldos actuales, con filtros por odontólogo y modalidad de cuenta.', 'reportes.pagos', 'M4 5h16v14H4zM4 9h16M7 15h3M15 15h2'],
                ['Mensajería', 'Entregas y recolecciones', 'Visitas por fecha de ruta, mensajero, odontólogo y estado registrado.', 'reportes.mensajeria', 'M3 5h11v12H3zM14 9h4l3 4v4h-7M5 17a2 2 0 1 0 4 0 2 2 0 1 0-4 0M16 17a2 2 0 1 0 4 0 2 2 0 1 0-4 0M14 13h7'],
            ] as [$categoria, $titulo, $descripcion, $ruta, $icono])
                <article class="rm-card">
                    <div class="rm-card-top">
                        <span class="rm-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $icono }}" /></svg>
                        </span>
                        <span class="rm-status rm-status-active">Disponible</span>
                    </div>
                    <p class="rm-category">{{ $categoria }}</p>
                    <h3>{{ $titulo }}</h3>
                    <p class="rm-card-description">{{ $descripcion }}</p>
                    <div class="rm-card-footer">
                        <a href="{{ route($ruta) }}" class="rm-open">
                            Consultar reporte
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6" /></svg>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
</div>
@endsection
