<style>
    .cc { color:#172b4d; max-width:1440px; margin-inline:auto; min-width:0; }
    .cc h1 { font-size:clamp(1.6rem,3vw,2rem); line-height:1.2; font-weight:750; letter-spacing:-.025em; }
    .cc h2 { font-size:1.1rem; font-weight:700; }
    .cc h1, .cc h2, .cc h3 { min-width:0; overflow-wrap:anywhere; }
    .cc .cc-header > div { min-width:0; }
    .cc .cc-header { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:1.5rem; }
    .cc .cc-kicker { color:#315875; font-size:.7rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase; margin-bottom:.4rem; }
    .cc .cc-muted { color:#64748b; font-size:.875rem; line-height:1.6; }
    .cc .cc-actions { display:flex; flex-wrap:wrap; gap:.65rem; align-items:center; }
    .cc .cc-btn { display:inline-flex; align-items:center; justify-content:center; gap:.5rem; min-height:42px; padding:.65rem 1rem; border:1px solid #cbd5e1; border-radius:.65rem; background:#fff; color:#315875; font-size:.85rem; font-weight:650; text-decoration:none; cursor:pointer; }
    .cc .cc-btn:hover { background:#edf3f8; }
    .cc .cc-btn-primary { border-color:#315875; background:#315875; color:#fff; }
    .cc .cc-btn-primary:hover { background:#182d47; }
    .cc .cc-btn-danger { color:#b91c1c; border-color:#fecaca; }
    .cc .cc-btn:focus-visible, .cc a:focus-visible { outline:2px solid #315875; outline-offset:3px; }
    .cc .cc-card { min-width:0; background:#fff; border:1px solid #dce5ee; border-radius:1rem; box-shadow:0 2px 5px #172b4d05; margin-bottom:1.25rem; }
    .cc .cc-padding { padding:1.25rem; }
    .cc .cc-stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:1rem; }
    .cc .cc-stat-label { color:#64748b; font-size:.72rem; }
    .cc .cc-stat-value { font-weight:750; font-size:clamp(1.2rem,2vw,1.6rem); margin-top:.5rem; overflow-wrap:anywhere; }
    .cc .cc-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:1.1rem; }
    .cc .cc-wide { grid-column:1 / -1; }
    .cc label { display:block; margin-bottom:.4rem; font-size:.85rem; font-weight:650; }
    .cc input:not([type=hidden]), .cc select, .cc textarea { width:100%; min-width:0; padding:.7rem .85rem; border:1px solid #cbd5e1; border-radius:.6rem; background:#fff; color:#172b4d; font:inherit; font-size:.875rem; }
    .cc input:focus, .cc select:focus, .cc textarea:focus { outline:2px solid #b7cfdf; outline-offset:1px; border-color:#315875; }
    .cc .cc-help { color:#64748b; font-size:.75rem; margin-top:.4rem; line-height:1.55; }
    .cc .cc-notice { border:1px solid #cbdce7; border-radius:.75rem; background:#f0f6fa; color:#284d68; padding:1rem; margin-bottom:1.25rem; font-size:.875rem; line-height:1.6; }
    .cc .cc-notice-warning { border-color:#f3d391; background:#fff8e8; color:#854d0e; }
    .cc .cc-notice-error { border-color:#fecaca; background:#fef2f2; color:#b91c1c; }
    .cc .cc-notice-success { border-color:#a7dfc6; background:#ecfdf5; color:#166534; }
    .cc .cc-scroll { max-width:100%; overflow-x:auto; }
    .cc table { width:100%; border-collapse:collapse; font-size:.85rem; }
    .cc .cc-table { min-width:1200px; }
    .cc .cc-concepto { min-width:200px; max-width:320px; }
    .cc th { background:#edf3f8; padding:.85rem 1rem; text-align:left; font-size:.7rem; color:#64748b; text-transform:uppercase; letter-spacing:.03em; white-space:nowrap; }
    .cc td { padding:.9rem 1rem; border-top:1px solid #e8eef4; vertical-align:top; overflow-wrap:anywhere; }
    .cc td.cc-number, .cc th.cc-number { text-align:right; white-space:nowrap; }
    .cc .cc-link { color:#315875; font-weight:650; text-decoration:none; }
    .cc .cc-link:hover { text-decoration:underline; }
    .cc .cc-badge { display:inline-flex; border-radius:999px; padding:.25rem .65rem; font-size:.7rem; font-weight:650; background:#f1f5f9; color:#475569; white-space:nowrap; }
    .cc .cc-badge-ok { background:#e5f7ef; color:#166534; }
    .cc .cc-badge-pending { background:#fff3d6; color:#854d0e; }
    .cc .cc-badge-no { background:#fef2f2; color:#b91c1c; }
    .cc dl { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:1.1rem; }
    .cc dt { color:#64748b; font-size:.75rem; }
    .cc dd { font-size:.9rem; margin-top:.3rem; overflow-wrap:anywhere; white-space:pre-wrap; }
    .cc .cc-history { border-left:3px solid #b9cfdd; padding-left:1.1rem; margin-top:1.25rem; }
    .cc .cc-footer { display:flex; flex-wrap:wrap; justify-content:flex-end; gap:.75rem; border-top:1px solid #e2e8f0; padding-top:1.1rem; margin-top:1.5rem; }
    .cc .cc-empty { text-align:center; color:#64748b; padding:2rem; }
    .cc [hidden] { display:none !important; }
    @media(max-width:1000px) { .cc .cc-stats { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media(max-width:640px) { .cc .cc-grid, .cc dl { grid-template-columns:1fr; } .cc .cc-padding { padding:1rem; } .cc .cc-actions { width:100%; } .cc .cc-actions .cc-btn { flex:1; } }
</style>
