    <style>
        /* ── Design Tokens ──────────────────────────────────────── */
        :root {
            --brand-primary: #4f46e5;
            /* indigo */
            --brand-primary-light: #eef2ff;
            --brand-accent: #06b6d4;
            /* cyan accent */
            --brand-success: #10b981;
            --brand-success-light: #d1fae5;
            --brand-danger: #ef4444;
            --brand-danger-light: #fee2e2;
            --brand-warning: #f59e0b;
            --brand-warning-light: #fef3c7;
            --surface-0: #ffffff;
            --surface-1: #f8fafc;
            --surface-2: #f1f5f9;
            --border-light: #e2e8f0;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 14px;
            --shadow-sm: 0 1px 3px rgba(15, 23, 42, .06), 0 1px 2px rgba(15, 23, 42, .04);
            --shadow-md: 0 4px 16px rgba(15, 23, 42, .08), 0 1px 4px rgba(15, 23, 42, .04);
            --shadow-focus: 0 0 0 3px rgba(79, 70, 229, .18);
        }

        /* ── Header Card ─────────────────────────────────────────── */
        .verif-header-card {
            background: var(--surface-0);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            border: none;
            overflow: hidden;
        }

        .verif-header-card::before {
            content: '';
            display: block;
            height: 4px;
            background: linear-gradient(90deg, var(--brand-primary) 0%, var(--brand-accent) 100%);
        }

        /* ── Stat Boxes ──────────────────────────────────────────── */
        .stat-box {
            background: var(--surface-1);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            padding: .65rem 1.1rem;
            text-align: center;
            min-width: 110px;
            transition: box-shadow .15s ease, transform .15s ease;
        }

        .stat-box:hover {
            box-shadow: var(--shadow-sm);
            transform: translateY(-1px);
        }

        .stat-box .stat-label {
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .55px;
            color: var(--text-muted);
            margin-bottom: .3rem;
        }

        .stat-box .stat-value {
            font-size: .9rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .stat-box .stat-sub {
            font-size: .7rem;
            color: var(--text-muted);
            margin-top: .15rem;
        }

        /* ── Summary Pills ───────────────────────────────────────── */
        .verif-summary-row {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            padding: 1rem 1.5rem 1.25rem;
            border-top: 1px solid var(--border-light);
        }

        .verif-pill {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .3rem .85rem;
            border-radius: 99px;
            font-size: .76rem;
            font-weight: 700;
        }

        .verif-pill.pending {
            background: var(--brand-primary-light);
            color: var(--brand-primary);
        }

        .verif-pill.approved {
            background: var(--brand-success-light);
            color: #059669;
        }

        .verif-pill.warning {
            background: var(--brand-warning-light);
            color: #92400e;
        }

        .swal-verif-loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: .9rem;
            padding: .25rem 0 .5rem;
        }

        .swal-verif-loader {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            border: 4px solid var(--brand-primary-light);
            border-top-color: var(--brand-primary);
            border-right-color: var(--brand-accent);
            animation: verif-loader-spin .8s linear infinite;
        }

        .swal-verif-loading-text {
            color: var(--text-secondary);
            font-size: .9rem;
            font-weight: 600;
        }

        .swal-verif-loading-dots {
            display: inline-flex;
            gap: .25rem;
            margin-left: .15rem;
            vertical-align: middle;
        }

        .swal-verif-loading-dots span {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--brand-primary);
            animation: verif-loader-dot 1s ease-in-out infinite;
        }

        .swal-verif-loading-dots span:nth-child(2) {
            animation-delay: .15s;
        }

        .swal-verif-loading-dots span:nth-child(3) {
            animation-delay: .3s;
        }

        @keyframes verif-loader-spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes verif-loader-dot {
            0%,
            80%,
            100% {
                opacity: .35;
                transform: translateY(0);
            }

            40% {
                opacity: 1;
                transform: translateY(-4px);
            }
        }

        .verif-pill.rejected {
            background: var(--brand-danger-light);
            color: #dc2626;
        }

        /* ── Progress Bar ────────────────────────────────────────── */
        .verif-progress-wrap {
            background: var(--surface-2);
            border-radius: 99px;
            height: 7px;
            overflow: hidden;
        }

        .verif-progress-bar {
            height: 100%;
            border-radius: 99px;
            background: linear-gradient(90deg, var(--brand-primary), var(--brand-accent));
            transition: width .5s cubic-bezier(.4, 0, .2, 1);
        }

        .verif-progress-bar.has-rejected {
            background: linear-gradient(90deg, var(--brand-danger), #f97316);
        }

        /* ── Tab Nav ─────────────────────────────────────────────── */
        .tab-nav-wrap {
            position: relative;
        }

        .tab-nav-wrap::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 48px;
            height: 100%;
            background: linear-gradient(to right, transparent, #fff);
            pointer-events: none;
            z-index: 1;
        }

        .verif-tabs.nav-tabs {
            border-bottom: 2px solid var(--border-light);
            scrollbar-width: none;
            gap: .15rem;
        }

        .verif-tabs.nav-tabs::-webkit-scrollbar {
            display: none;
        }

        .verif-tabs .nav-link {
            font-size: .82rem;
            font-weight: 600;
            color: var(--text-secondary);
            padding: .8rem 1rem;
            border: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            border-radius: var(--radius-sm) var(--radius-sm) 0 0;
            white-space: nowrap;
            transition: color .15s, border-color .15s, background .15s;
        }

        .verif-tabs .nav-link:hover {
            color: var(--brand-primary);
            background: var(--brand-primary-light);
        }

        .reject-field-list {
            max-height: 190px;
            overflow: auto;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-sm);
            padding: .5rem;
            margin-bottom: 1rem;
            background: #fff;
        }

        .reject-field-option {
            display: flex;
            align-items: flex-start;
            gap: .65rem;
            padding: .55rem .6rem;
            margin: 0;
            border-radius: var(--radius-sm);
            cursor: pointer;
            line-height: 1.35;
        }

        .reject-field-option:hover {
            background: var(--surface-1);
        }

        .reject-field-option input {
            flex: 0 0 auto;
            width: 16px;
            height: 16px;
            margin-top: .1rem;
        }

        .reject-field-option span {
            min-width: 0;
            color: var(--text-primary);
            font-size: .82rem;
            font-weight: 700;
            word-break: break-word;
        }

        .verif-tabs .nav-link.active {
            color: var(--brand-primary);
            border-bottom-color: var(--brand-primary);
            background: var(--brand-primary-light);
        }

        .verif-tab-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            border-radius: 99px;
            font-size: .62rem;
            font-weight: 700;
            margin-left: .4rem;
        }

        .verif-tab-badge.is-pending {
            background: var(--brand-primary);
            color: #fff;
        }

        .verif-tab-badge.is-rejected {
            background: var(--brand-danger);
            color: #fff;
        }

        .verif-tab-badge.is-done {
            background: var(--brand-success);
            color: #fff;
        }

        /* ── Verification Panel ──────────────────────────────────── */
        .verification-panel {
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            overflow: hidden;
            background: var(--surface-0);
            box-shadow: var(--shadow-sm);
            transition: box-shadow .2s ease, transform .2s ease;
        }

        .verification-panel:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .verification-panel.is-approved {
            border-color: #a7f3d0;
            box-shadow: 0 0 0 1px #a7f3d0, var(--shadow-sm);
        }

        .verification-panel.is-rejected {
            border-color: #fca5a5;
            box-shadow: 0 0 0 1px #fca5a5, var(--shadow-sm);
        }

        /* ── Panel Head ──────────────────────────────────────────── */
        .verification-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.25rem;
            background: var(--surface-1);
            border-bottom: 1px solid var(--border-light);
        }

        .verification-panel.is-approved .verification-panel-head {
            background: #f0fdf4;
            border-bottom-color: #bbf7d0;
        }

        .verification-panel.is-rejected .verification-panel-head {
            background: #fff5f5;
            border-bottom-color: #fecaca;
        }

        .panel-head-title {
            font-size: .9rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: .2rem;
            line-height: 1.3;
        }

        .panel-head-meta {
            font-size: .73rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: .3rem;
        }

        /* ── Status Badge ────────────────────────────────────────── */
        .verif-status-badge {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .28rem .75rem;
            border-radius: 99px;
            font-size: .72rem;
            font-weight: 700;
        }

        .verif-status-badge.is-pending {
            background: var(--brand-primary-light);
            color: var(--brand-primary);
        }

        .verif-status-badge.is-approved {
            background: var(--brand-success-light);
            color: #065f46;
        }

        .verif-status-badge.is-rejected {
            background: var(--brand-danger-light);
            color: #991b1b;
        }

        /* ── Action Buttons ──────────────────────────────────────── */
        .btn-approve {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .38rem .9rem;
            border-radius: var(--radius-sm);
            font-size: .78rem;
            font-weight: 700;
            background: var(--brand-success);
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background .15s, transform .1s, box-shadow .15s;
        }

        .btn-approve:hover {
            background: #059669;
            box-shadow: 0 3px 10px rgba(16, 185, 129, .3);
            transform: translateY(-1px);
        }

        .btn-reject {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .38rem .9rem;
            border-radius: var(--radius-sm);
            font-size: .78rem;
            font-weight: 700;
            background: var(--brand-danger-light);
            color: var(--brand-danger);
            border: 1px solid #fca5a5;
            cursor: pointer;
            transition: background .15s, transform .1s, box-shadow .15s;
        }

        .btn-reject:hover {
            background: var(--brand-danger);
            color: #fff;
            box-shadow: 0 3px 10px rgba(239, 68, 68, .25);
            transform: translateY(-1px);
        }

        .btn-undo {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .28rem .7rem;
            border-radius: var(--radius-sm);
            font-size: .72rem;
            font-weight: 600;
            background: var(--surface-2);
            color: var(--text-secondary);
            border: 1px solid var(--border-light);
            cursor: pointer;
            transition: background .15s;
        }

        .btn-undo:hover {
            background: var(--border-light);
        }

        /* ── Rejection Note ──────────────────────────────────────── */
        .rejection-note {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            padding: .85rem 1.25rem;
            background: #fff5f5;
            border-bottom: 1px solid #fecaca;
            font-size: .82rem;
        }

        .rejection-note-icon {
            width: 24px;
            height: 24px;
            border-radius: 99px;
            background: var(--brand-danger);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: .65rem;
            margin-top: .05rem;
        }

        .rejection-note-text {
            color: #7f1d1d;
            line-height: 1.5;
        }

        /* ── Field Grid ──────────────────────────────────────────── */
        .verification-field-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .verification-field {
            display: grid;
            grid-template-columns: minmax(140px, 32%) minmax(0, 1fr);
            min-height: 46px;
            border-bottom: 1px solid #f1f5f9;
        }

        .verification-field:nth-child(odd) {
            border-right: 1px solid #f1f5f9;
        }

        .verification-field-label {
            padding: .65rem 1rem;
            color: var(--text-muted);
            font-size: .72rem;
            font-weight: 700;
            background: var(--surface-1);
            border-right: 1px solid #f1f5f9;
            text-transform: uppercase;
            letter-spacing: .4px;
            display: flex;
            align-items: center;
        }

        .verification-field-value {
            min-width: 0;
            padding: .65rem 1rem;
            color: var(--text-primary);
            font-size: .85rem;
            word-break: break-word;
            white-space: normal;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: .35rem;
        }

        /* ── Subsection / Category Headers ───────────────────────── */
        .verification-subsection-title {
            grid-column: 1 / -1;
            padding: .7rem 1rem;
            color: var(--brand-primary);
            font-size: .8rem;
            font-weight: 700;
            background: var(--brand-primary-light);
            border-top: 1px solid #c7d2fe;
            border-bottom: 1px solid #c7d2fe;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .specific-category-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            margin: 1.5rem 0 .6rem;
            padding: .75rem 1.1rem;
            background: linear-gradient(135deg, #f0f4ff 0%, #e8eeff 100%);
            border-left: 3px solid var(--brand-primary);
            border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
            color: var(--text-primary);
            font-weight: 700;
            font-size: .88rem;
        }

        /* ── Table Wrap ──────────────────────────────────────────── */
        .verification-table-wrap {
            grid-column: 1 / -1;
            overflow-x: auto;
        }

        .verification-table-wrap .table {
            min-width: 980px;
            margin-bottom: 0;
        }

        .verification-table-wrap .table thead th {
            background: var(--surface-1);
            border-bottom: 2px solid var(--border-light);
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: var(--text-secondary);
            padding: .7rem 1rem;
        }

        .verification-table-wrap .table tbody td {
            font-size: .82rem;
            padding: .65rem 1rem;
            vertical-align: middle;
            border-color: #f1f5f9;
        }

        .verification-table-wrap .table tbody tr:hover td {
            background: var(--surface-1);
        }

        /* ── Doc Preview Button ──────────────────────────────────── */
        .btn-preview-doc {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .25rem .65rem;
            border-radius: var(--radius-sm);
            font-size: .72rem;
            font-weight: 700;
            background: var(--brand-primary-light);
            color: var(--brand-primary);
            border: 1px solid #c7d2fe;
            cursor: pointer;
            transition: background .15s, box-shadow .15s;
        }

        .btn-preview-doc:hover {
            background: var(--brand-primary);
            color: #fff;
            box-shadow: 0 2px 8px rgba(79, 70, 229, .25);
        }

        /* ── Min-w utility ───────────────────────────────────────── */
        .min-w-0 {
            min-width: 0;
        }

        /* ── Responsive ──────────────────────────────────────────── */
        @media (max-width: 991.98px) {
            .verification-panel-head {
                align-items: flex-start;
                flex-direction: column;
            }

            .verification-field-grid,
            .verification-field {
                grid-template-columns: 1fr;
            }

            .verification-field:nth-child(odd) {
                border-right: 0;
            }

            .verification-field-label {
                border-right: 0;
                border-bottom: 1px solid #f1f5f9;
                padding-bottom: .4rem;
            }
        }
    </style>
