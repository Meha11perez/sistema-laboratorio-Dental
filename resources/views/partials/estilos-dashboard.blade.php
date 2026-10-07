 <style>
    .dental-dashboard {
        --dd-blue: #365b7c;
        --dd-blue-dark: #294b69;
        --dd-cyan: #278596;
        --dd-text: #253649;
        --dd-muted: #627386;
        --dd-border: #dce5ec;
        max-width: 1280px;
        margin: 0 auto;
        color: var(--dd-text);
        font-family: 'Segoe UI', system-ui, -apple-system, Arial, sans-serif;
    }

    .dental-dashboard * { box-sizing: border-box; }

    .dental-dashboard h1,
    .dental-dashboard h2,
    .dental-dashboard h3,
    .dental-dashboard p { margin: 0; }

    .dental-dashboard a { text-decoration: none; }

    .dental-dashboard a:focus-visible {
        outline: 3px solid #71c5d2;
        outline-offset: 4px;
    }

    .dental-dashboard .dd-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 22px;
    }

    .dental-dashboard .dd-eyebrow {
        color: var(--dd-cyan);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .dental-dashboard h1 {
        font-size: 28px;
        font-weight: 600;
        line-height: 1.3;
        margin: 5px 0;
    }

    .dental-dashboard .dd-subtitle {
        color: var(--dd-muted);
        font-size: 14px;
        line-height: 1.6;
    }

    .dental-dashboard .dd-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .dental-dashboard .dd-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 10px 16px;
        border: 1px solid var(--dd-blue);
        border-radius: 9px;
        background: var(--dd-blue);
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        transition: background .15s;
    }

    .dental-dashboard .dd-button:hover {
        background: var(--dd-blue-dark);
    }

    .dental-dashboard .dd-button-secondary {
        background: #fff;
        color: var(--dd-blue);
        border-color: var(--dd-border);
    }

    .dental-dashboard .dd-button-secondary:hover {
        background: #eef5f9;
    }

    .dental-dashboard .dd-metrics {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .dental-dashboard .dd-metric {
        padding: 18px 20px;
        background: #fff;
        border: 1px solid var(--dd-border);
        border-top: 3px solid #82a7bf;
        border-radius: 12px;
    }

    .dental-dashboard .dd-metric-cyan {
        border-top-color: #75b8c4;
    }

    .dental-dashboard .dd-metric-alert {
        border-top-color: #c79044;
    }

    .dental-dashboard .dd-label {
        color: var(--dd-muted);
        font-size: 12px;
        font-weight: 600;
    }

    .dental-dashboard .dd-number {
        display: block;
        margin: 6px 0;
        color: var(--dd-blue);
        font-size: 32px;
        font-weight: 600;
        line-height: 1.2;
    }

    .dental-dashboard .dd-metric-alert .dd-number {
        color: #946020;
    }

    .dental-dashboard .dd-note {
        color: var(--dd-muted);
        font-size: 12px;
        line-height: 1.5;
    }

    .dental-dashboard .dd-shortcuts {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }

    .dental-dashboard .dd-shortcut {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 15px 17px;
        background: #eef5f9;
        border: 1px solid #d9e6ee;
        border-radius: 10px;
        color: var(--dd-blue);
        font-size: 13px;
        font-weight: 600;
    }

    .dental-dashboard .dd-shortcut:hover {
        background: #e3eff5;
    }

    .dental-dashboard .dd-columns {
        display: grid;
        grid-template-columns: minmax(0, 1.25fr) minmax(0, 1fr);
        align-items: start;
        gap: 18px;
        margin-bottom: 20px;
    }

    .dental-dashboard .dd-panel {
        background: #fff;
        border: 1px solid var(--dd-border);
        border-radius: 12px;
        overflow: hidden;
    }

    .dental-dashboard .dd-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--dd-border);
    }

    .dental-dashboard h2 {
        font-size: 16px;
        font-weight: 600;
        line-height: 1.4;
    }

    .dental-dashboard .dd-panel-header .dd-note {
        margin-top: 4px;
    }

    .dental-dashboard .dd-count {
        flex-shrink: 0;
        padding: 5px 10px;
        border-radius: 7px;
        background: #edf5f8;
        color: var(--dd-blue);
        font-size: 12px;
        font-weight: 600;
    }

    .dental-dashboard .dd-areas {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        padding: 18px 20px;
    }

    .dental-dashboard .dd-area {
        border: 1px solid var(--dd-border);
        border-radius: 10px;
        padding: 14px;
    }

    .dental-dashboard .dd-area-heading {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 10px;
    }

    .dental-dashboard .dd-area-code {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 33px;
        height: 30px;
        flex-shrink: 0;
        border-radius: 6px;
        background: #eaf4f7;
        color: var(--dd-cyan);
        font-size: 11px;
        font-weight: 700;
    }

    .dental-dashboard h3 {
        font-size: 13px;
        font-weight: 600;
    }

    .dental-dashboard .dd-area-total {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 8px;
    }

    .dental-dashboard .dd-area-total strong {
        color: var(--dd-blue);
        font-size: 26px;
        font-weight: 600;
    }

    .dental-dashboard progress {
        display: block;
        width: 100%;
        height: 6px;
        border: 0;
        border-radius: 4px;
        overflow: hidden;
        background: #edf2f6;
        accent-color: #5b9eaf;
    }

    .dental-dashboard progress::-webkit-progress-bar {
        background: #edf2f6;
        border-radius: 4px;
    }

    .dental-dashboard progress::-webkit-progress-value {
        background: #5b9eaf;
        border-radius: 4px;
    }

    .dental-dashboard progress::-moz-progress-bar {
        background: #5b9eaf;
        border-radius: 4px;
    }

    .dental-dashboard .dd-area-warning {
        padding: 0 20px 18px;
        color: #946020;
        font-size: 12px;
        line-height: 1.5;
    }

    .dental-dashboard .dd-stages {
        list-style: none;
        margin: 0;
        padding: 4px 20px;
    }

    .dental-dashboard .dd-stages li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 13px 0;
        border-bottom: 1px solid #edf2f6;
        font-size: 13px;
    }

    .dental-dashboard .dd-stages li:last-child {
        border-bottom: 0;
    }

    .dental-dashboard .dd-link {
        color: var(--dd-blue);
        font-size: 13px;
        font-weight: 600;
    }

    .dental-dashboard .dd-link:hover {
        text-decoration: underline;
    }

    .dental-dashboard .dd-table-scroll {
        overflow-x: auto;
    }

    .dental-dashboard table {
        width: 100%;
        min-width: 860px;
        border-collapse: collapse;
        text-align: left;
    }

    .dental-dashboard th {
        padding: 12px 16px;
        background: #f5f8fa;
        color: var(--dd-muted);
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .dental-dashboard td {
        padding: 14px 16px;
        border-top: 1px solid #edf2f6;
        font-size: 12px;
        vertical-align: top;
    }

    .dental-dashboard td .dd-note {
        margin-top: 3px;
    }

    .dental-dashboard tbody tr:hover {
        background: #fafcfd;
    }

    .dental-dashboard .dd-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 6px;
        background: #edf5f8;
        color: var(--dd-blue);
        font-size: 11px;
        white-space: nowrap;
    }

    .dental-dashboard .dd-overdue {
        display: block;
        margin-top: 4px;
        color: #946020;
        font-size: 11px;
        font-weight: 600;
    }

    .dental-dashboard .dd-empty {
        padding: 25px 20px;
        color: var(--dd-muted);
        font-size: 13px;
        line-height: 1.6;
    }

    .dental-dashboard .dd-table-footer {
        padding: 12px 20px;
        border-top: 1px solid var(--dd-border);
        background: #fafcfd;
    }

    .dental-dashboard .dd-footer {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 18px;
        color: var(--dd-muted);
        font-size: 12px;
    }

    @media (max-width: 900px) {
        .dental-dashboard .dd-columns {
            grid-template-columns: 1fr;
        }

        .dental-dashboard .dd-shortcuts {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .dental-dashboard .dd-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 14px;
        }

        .dental-dashboard h1 {
            font-size: 24px;
        }

        .dental-dashboard .dd-metrics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .dental-dashboard .dd-metric {
            padding: 14px;
        }

        .dental-dashboard .dd-number {
            font-size: 28px;
        }

        .dental-dashboard .dd-panel-header {
            flex-wrap: wrap;
        }

        .dental-dashboard .dd-areas {
            padding: 14px;
            gap: 10px;
        }

        .dental-dashboard .dd-area {
            padding: 11px;
        }
    }

    @media (max-width: 380px) {
        .dental-dashboard .dd-metrics,
        .dental-dashboard .dd-areas,
        .dental-dashboard .dd-shortcuts {
            grid-template-columns: 1fr;
        }
    }
</style>
