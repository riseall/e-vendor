    <style>
        /* ══════════════════════════════════════════════
               DESIGN TOKENS — warna utama tetap pakai
               primary #005db6 dari sistem Metronic existing
            ══════════════════════════════════════════════ */
        :root {
            --vp-blue: #005db6;
            --vp-blue-lt: #e8f1fb;
            --vp-amber: #f59e0b;
            --vp-amber-lt: #fff8e7;
            --vp-green: #2e7d32;
            --vp-green-lt: #e8f5e9;
            --vp-red: #c62828;
            --vp-red-lt: #fdecea;
            --vp-ink: #1e2a3b;
            --vp-muted: #6b7a96;
            --vp-border: #e6eaf2;
            --vp-surface: #f5f7fb;
            --vp-white: #ffffff;
        }

        /* ── Stat Cards ─────────────────────────────── */
        .vp-stat {
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            border: 1.5px solid transparent;
            transition: transform .15s, box-shadow .15s;
        }

        .vp-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, .08);
        }

        .vp-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.3rem;
        }

        .vp-stat-num {
            font-size: 1.65rem;
            font-weight: 800;
            line-height: 1;
        }

        .vp-stat-lbl {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            margin-top: .2rem;
        }

        .vp-stat--total {
            background: var(--vp-blue-lt);
            border-color: #c2d9f5;
        }

        .vp-stat--total .vp-stat-icon {
            background: var(--vp-blue);
        }

        .vp-stat--total .vp-stat-num {
            color: var(--vp-blue);
        }

        .vp-stat--total .vp-stat-lbl {
            color: #3d6fa8;
        }

        .vp-stat--pending {
            background: var(--vp-amber-lt);
            border-color: #fcd97a;
        }

        .vp-stat--pending .vp-stat-icon {
            background: var(--vp-amber);
        }

        .vp-stat--pending .vp-stat-num {
            color: #b45309;
        }

        .vp-stat--pending .vp-stat-lbl {
            color: #92620a;
        }

        .vp-stat--revision {
            background: var(--vp-red-lt);
            border-color: #f5b5b5;
        }

        .vp-stat--revision .vp-stat-icon {
            background: var(--vp-red);
        }

        .vp-stat--revision .vp-stat-num {
            color: var(--vp-red);
        }

        .vp-stat--revision .vp-stat-lbl {
            color: #9f2b2b;
        }

        .vp-stat--done {
            background: var(--vp-green-lt);
            border-color: #a5d6a7;
        }

        .vp-stat--done .vp-stat-icon {
            background: var(--vp-green);
        }

        .vp-stat--done .vp-stat-num {
            color: var(--vp-green);
        }

        .vp-stat--done .vp-stat-lbl {
            color: #2d6e30;
        }

        /* ── Main Card ──────────────────────────────── */
        .vp-card {
            border: 1.5px solid var(--vp-border);
            border-radius: 16px;
            background: var(--vp-white);
            box-shadow: 0 2px 12px rgba(0, 0, 0, .05);
            overflow: hidden;
        }

        .vp-card-head {
            padding: 1.1rem 1.5rem;
            border-bottom: 1.5px solid var(--vp-border);
            background: var(--vp-surface);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .vp-card-title {
            font-size: .88rem;
            font-weight: 800;
            color: var(--vp-ink);
            text-transform: uppercase;
            letter-spacing: .8px;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .vp-card-title-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--vp-blue);
            flex-shrink: 0;
        }

        /* ── Filter Bar ─────────────────────────────── */
        .vp-filter {
            display: flex;
            align-items: center;
            gap: .6rem;
            flex-wrap: wrap;
        }

        .vp-filter .form-control,
        .vp-filter .form-control-sm {
            border-radius: 8px;
            border: 1.5px solid var(--vp-border);
            font-size: .82rem;
            height: 34px;
            color: var(--vp-ink);
            background: var(--vp-white);
            transition: border-color .15s, box-shadow .15s;
        }

        .vp-filter .form-control:focus {
            border-color: var(--vp-blue);
            box-shadow: 0 0 0 3px rgba(0, 93, 182, .12);
        }

        .vp-filter .input-group-text {
            border: 1.5px solid var(--vp-border);
            border-left: 0;
            border-radius: 0 8px 8px 0;
            background: var(--vp-surface);
            color: var(--vp-muted);
        }

        .vp-filter .input-group input {
            border-right: 0;
            border-radius: 8px 0 0 8px;
        }

        .vp-btn-filter {
            height: 34px;
            padding: 0 1rem;
            border-radius: 8px;
            font-size: .82rem;
            font-weight: 700;
            border: none;
            background: var(--vp-blue);
            color: #fff;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            transition: background .15s, box-shadow .15s;
        }

        .vp-btn-filter:hover {
            background: #004f9e;
            box-shadow: 0 3px 8px rgba(0, 93, 182, .3);
        }

        .vp-btn-reset {
            height: 34px;
            padding: 0 .9rem;
            border-radius: 8px;
            font-size: .82rem;
            font-weight: 700;
            border: 1.5px solid var(--vp-border);
            background: var(--vp-white);
            color: var(--vp-muted);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: border-color .15s, color .15s;
        }

        .vp-btn-reset:hover {
            border-color: var(--vp-blue);
            color: var(--vp-blue);
            text-decoration: none;
        }

        /* ── Table ──────────────────────────────────── */
        #tbl-permohonan {
            width: 100% !important;
        }

        #tbl-permohonan thead th {
            background: var(--vp-surface);
            border-bottom: 2px solid var(--vp-border);
            color: var(--vp-muted);
            font-size: .7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .6px;
            padding: .85rem 1rem;
            white-space: nowrap;
            vertical-align: middle;
        }

        #tbl-permohonan thead th.sorting::after,
        #tbl-permohonan thead th.sorting_asc::after,
        #tbl-permohonan thead th.sorting_desc::after {
            opacity: .5;
        }

        #tbl-permohonan tbody tr {
            transition: background .1s;
        }

        #tbl-permohonan tbody tr:hover {
            background: #f0f5ff !important;
        }

        #tbl-permohonan tbody td {
            padding: .9rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--vp-border);
            border-top: none;
            color: var(--vp-ink);
            font-size: .85rem;
        }

        /* ── App Number ─────────────────────────────── */
        .vp-appnum {
            font-family: 'SFMono-Regular', Consolas, monospace;
            font-size: .78rem;
            font-weight: 700;
            color: var(--vp-blue);
            background: var(--vp-blue-lt);
            border-radius: 6px;
            padding: .25rem .55rem;
            letter-spacing: .3px;
        }

        /* ── Avatar Chip ─────────────────────────────── */
        .vp-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #005db6, #3b82f6);
            color: #fff;
            font-size: .72rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            text-transform: uppercase;
        }

        /* ── Status Badge ───────────────────────────── */
        .vp-badge {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            border-radius: 20px;
            padding: .3rem .75rem;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            white-space: nowrap;
        }

        .vp-badge i {
            font-size: .55rem;
        }

        .vp-badge--submitted {
            background: var(--vp-blue-lt);
            color: var(--vp-blue);
        }

        .vp-badge--revision {
            background: var(--vp-amber-lt);
            color: #b45309;
        }

        .vp-badge--verified {
            background: var(--vp-green-lt);
            color: var(--vp-green);
        }

        .vp-badge--in-progress {
            background: #e0f2fe;
            color: #0369a1;
        }

        .vp-progress-note {
            margin-top: .45rem;
            width: 126px;
        }

        .vp-progress-text {
            display: flex;
            justify-content: space-between;
            margin-bottom: .2rem;
            font-size: .68rem;
            font-weight: 700;
            color: var(--vp-muted);
        }

        .vp-progress-track {
            height: 5px;
            overflow: hidden;
            border-radius: 999px;
            background: #e5e7eb;
        }

        .vp-progress-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--vp-blue), #0ea5e9);
        }

        /* Deadline chip */
        .vp-hk {
            display: inline-block;
            border-radius: 5px;
            padding: .2rem .5rem;
            font-size: .7rem;
            font-weight: 700;
            margin-top: .25rem;
        }

        .vp-hk--ok {
            background: var(--vp-blue-lt);
            color: var(--vp-blue);
        }

        .vp-hk--warn {
            background: var(--vp-amber-lt);
            color: #b45309;
        }

        .vp-hk--overdue {
            background: var(--vp-red-lt);
            color: var(--vp-red);
        }

        .vp-hk--done {
            background: var(--vp-green-lt);
            color: var(--vp-green);
        }

        /* ── Detail Button ──────────────────────────── */
        .vp-btn-detail {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .35rem .85rem;
            border-radius: 8px;
            font-size: .78rem;
            font-weight: 700;
            color: var(--vp-blue);
            background: var(--vp-blue-lt);
            border: 1.5px solid #c2d9f5;
            text-decoration: none;
            white-space: nowrap;
            transition: background .15s, box-shadow .15s;
        }

        .vp-btn-detail:hover {
            background: #d4e8fb;
            box-shadow: 0 2px 8px rgba(0, 93, 182, .18);
            text-decoration: none;
            color: var(--vp-blue);
        }

        /* ── DataTables override ────────────────────── */
        div.dataTables_wrapper div.dataTables_length select,
        div.dataTables_wrapper div.dataTables_filter input {
            border-radius: 8px;
            border: 1.5px solid var(--vp-border);
            font-size: .82rem;
            padding: .25rem .5rem;
            color: var(--vp-ink);
        }

        div.dataTables_wrapper div.dataTables_filter input:focus {
            border-color: var(--vp-blue);
            box-shadow: 0 0 0 3px rgba(0, 93, 182, .12);
            outline: none;
        }

        div.dataTables_wrapper div.dataTables_info {
            font-size: .78rem;
            color: var(--vp-muted);
            padding-top: .7rem;
        }

        div.dataTables_wrapper div.dataTables_paginate {
            padding-top: .4rem;
        }

        div.dataTables_wrapper div.dataTables_paginate .paginate_button {
            border-radius: 8px !important;
            font-size: .78rem;
            font-weight: 600;
        }

        div.dataTables_wrapper div.dataTables_paginate .paginate_button.current,
        div.dataTables_wrapper div.dataTables_paginate .paginate_button.current:hover {
            background: var(--vp-blue) !important;
            border-color: var(--vp-blue) !important;
            color: #fff !important;
        }

        div.dataTables_wrapper div.dataTables_paginate .paginate_button:hover {
            background: var(--vp-blue-lt) !important;
            border-color: var(--vp-border) !important;
            color: var(--vp-blue) !important;
        }

        /* ── Empty State ────────────────────────────── */
        .vp-empty {
            text-align: center;
            padding: 3.5rem 1rem;
            color: var(--vp-muted);
        }

        .vp-empty-icon {
            font-size: 2.5rem;
            margin-bottom: .75rem;
            opacity: .35;
        }

        .vp-empty-title {
            font-size: .95rem;
            font-weight: 700;
            margin-bottom: .25rem;
            color: var(--vp-ink);
        }

        .vp-empty-sub {
            font-size: .8rem;
        }
    </style>
