<style>
    .reportes-lab { --rl-blue:#365b7c; --rl-muted:#627386; --rl-border:#dce5ec; max-width:1280px; margin:0 auto; color:#253649; font-family:'Segoe UI',system-ui,Arial,sans-serif; }
    .reportes-lab * { box-sizing:border-box; }
    .reportes-lab h1,.reportes-lab h2,.reportes-lab p { margin:0; }
    .reportes-lab a { text-decoration:none; }
    .reportes-lab a:focus-visible,.reportes-lab button:focus-visible { outline:3px solid #71c5d2; outline-offset:3px; }
    .reportes-lab .rl-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px; margin-bottom:20px; }
    .reportes-lab .rl-eyebrow { color:#278596; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.1em; margin-bottom:5px; }
    .reportes-lab h1 { font-size:27px; font-weight:600; line-height:1.3; }
    .reportes-lab h2 { font-size:16px; font-weight:600; }
    .reportes-lab .rl-note { color:var(--rl-muted); font-size:12px; line-height:1.6; margin-top:5px; }
    .reportes-lab .rl-actions { display:flex; align-items:center; flex-wrap:wrap; gap:8px; }
    .reportes-lab .rl-button { display:inline-flex; align-items:center; justify-content:center; min-height:40px; padding:9px 14px; border:1px solid var(--rl-blue); border-radius:8px; background:var(--rl-blue); color:#fff; font-size:13px; font-weight:600; cursor:pointer; }
    .reportes-lab .rl-button:hover { background:#294b69; }
    .reportes-lab .rl-secondary { background:#fff; color:var(--rl-blue); border-color:var(--rl-border); }
    .reportes-lab .rl-secondary:hover { background:#edf5f8; }
    .reportes-lab .rl-panel { background:#fff; border:1px solid var(--rl-border); border-radius:12px; overflow:hidden; margin-bottom:18px; }
    .reportes-lab .rl-padding { padding:18px 20px; }
    .reportes-lab .rl-filters { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:14px; }
    .reportes-lab .rl-field label { display:block; color:#455b70; font-size:12px; font-weight:600; margin-bottom:6px; }
    .reportes-lab .rl-field input,.reportes-lab .rl-field select { width:100%; min-width:0; min-height:40px; border:1px solid #cbd9e3; border-radius:8px; background:#fff; padding:8px 10px; color:#253649; font:inherit; font-size:13px; }
    .reportes-lab .rl-field input:focus,.reportes-lab .rl-field select:focus { outline:2px solid #9bcbd4; outline-offset:1px; }
    .reportes-lab .rl-filter-footer { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:15px; }
    .reportes-lab .rl-metrics { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; margin-bottom:18px; }
    .reportes-lab .rl-metric { background:#fff; border:1px solid var(--rl-border); border-top:3px solid #82a7bf; border-radius:10px; padding:15px 18px; }
    .reportes-lab .rl-metric span { color:var(--rl-muted); font-size:12px; }
    .reportes-lab .rl-metric strong { display:block; color:var(--rl-blue); font-size:27px; font-weight:600; margin-top:4px; }
    .reportes-lab .rl-section-header { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; padding:16px 20px; border-bottom:1px solid var(--rl-border); }
    .reportes-lab .rl-table-scroll { overflow-x:auto; }
    .reportes-lab table { width:100%; border-collapse:collapse; min-width:980px; text-align:left; }
    .reportes-lab th { background:#f5f8fa; color:#627386; font-size:11px; font-weight:600; padding:12px 14px; white-space:nowrap; }
    .reportes-lab td { border-top:1px solid #edf2f6; padding:13px 14px; font-size:12px; vertical-align:top; }
    .reportes-lab .rl-link { color:var(--rl-blue); font-size:12px; font-weight:600; }
    .reportes-lab .rl-link:hover { text-decoration:underline; }
    .reportes-lab .rl-badge { display:inline-block; padding:4px 8px; border-radius:6px; background:#edf5f8; color:var(--rl-blue); font-size:11px; }
    .reportes-lab .rl-empty { padding:25px 20px; color:var(--rl-muted); font-size:13px; line-height:1.6; }
    .reportes-lab .rl-errors { margin-bottom:18px; padding:14px 18px; border:1px solid #edc6c6; border-radius:9px; background:#fff6f6; color:#9f3333; font-size:13px; }
    .reportes-lab .rl-errors ul { margin:0; padding-left:18px; }
    .reportes-lab .rl-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px; }
    .reportes-lab .rl-card { background:#fff; border:1px solid var(--rl-border); border-radius:12px; padding:21px; }
    .reportes-lab .rl-card-code { color:#278596; font-size:11px; font-weight:700; letter-spacing:.08em; margin-bottom:12px; }
    .reportes-lab .rl-card .rl-button { margin-top:17px; }
    .reportes-lab .rl-pending { display:inline-block; margin-top:17px; color:#627386; font-size:12px; }
    .reportes-lab .rl-details { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px; }
    .reportes-lab .rl-details dt { color:#627386; font-size:11px; margin-bottom:4px; }
    .reportes-lab .rl-details dd { margin:0; font-size:13px; }
    @media(max-width:1000px) { .reportes-lab .rl-filters,.reportes-lab .rl-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media(max-width:600px) { .reportes-lab .rl-filters,.reportes-lab .rl-details { grid-template-columns:1fr; } .reportes-lab .rl-metrics { grid-template-columns:repeat(2,minmax(0,1fr)); } .reportes-lab .rl-grid { grid-template-columns:1fr; } .reportes-lab h1 { font-size:24px; } }
</style>
