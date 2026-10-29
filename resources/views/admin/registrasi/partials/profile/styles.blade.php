<style>
    html {
        scroll-behavior: smooth;
    }

    /* Memastikan semua form readonly atau disabled selalu berbackground abu-abu konsisten */
    .form-control[readonly],
    .form-control[disabled],
    .form-control.form-control-solid {
        background-color: #F3F6F9 !important;
        opacity: 1 !important;
    }

    .vendor-profile-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        margin-bottom: 16px;
        background: #fff;
        border: 1px solid #e8edf3;
        border-left: 4px solid #0f6fc6;
        border-radius: 8px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    .vendor-profile-eyebrow {
        color: #7e8299;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .vendor-profile-number {
        color: #0f6fc6;
        font-size: 20px;
        font-weight: 700;
        line-height: 1.2;
        margin-top: 2px;
    }

    .vendor-profile-company {
        margin-top: 3px;
        color: #181c32;
        font-size: 14px;
        font-weight: 600;
    }

    .vendor-profile-status {
        max-width: 380px;
        text-align: right;
    }

    /* 2-Column Grid Layout (Default & Tablet/Desktop >= 768px) */
    .vendor-profile-layout {
        display: grid;
        grid-template-columns: minmax(190px, 220px) minmax(0, 1fr);
        gap: 16px;
        align-items: start;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }

    /* Sticky Sidebar Navigation */
    .vendor-profile-nav {
        position: sticky;
        top: 80px;
        max-height: calc(100vh - 100px);
        overflow-y: auto;
        padding: 10px 0;
        background: #fff;
        border: 1px solid #e8edf3;
        border-radius: 8px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
        min-width: 0;
        max-width: 100%;
        box-sizing: border-box;
    }

    .vendor-profile-nav::-webkit-scrollbar {
        width: 4px;
    }

    .vendor-profile-nav::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 4px;
    }

    .vendor-profile-nav-title {
        padding: 0 14px 8px;
        color: #7e8299;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #f3f6f9;
        margin-bottom: 4px;
    }

    .vendor-profile-nav a {
        position: relative;
        display: flex;
        align-items: center;
        padding: 8px 14px;
        color: #5e6278;
        font-size: 12px;
        font-weight: 600;
        border-left: 3px solid transparent;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .vendor-profile-nav a .nav-icon {
        width: 16px;
        font-size: 11.5px;
        color: #a1a5b7;
        margin-right: 8px;
        text-align: center;
        flex-shrink: 0;
        transition: color 0.15s ease;
    }

    .vendor-profile-nav a .nav-text {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .vendor-profile-nav a:hover,
    .vendor-profile-nav a:focus {
        color: #0f6fc6;
        background: #f8fafc;
        border-left-color: #cbd5e1;
        text-decoration: none;
    }

    .vendor-profile-nav a.active {
        color: #0f6fc6;
        background: #f0f7ff;
        border-left-color: #0f6fc6;
        font-weight: 700;
    }

    .vendor-profile-nav a.active .nav-icon {
        color: #0f6fc6;
    }

    .vendor-profile-nav a.has-revision {
        color: #b42318;
        background: #fff8e1;
        border-left-color: #dc3545;
    }

    .vendor-profile-nav a.has-revision .nav-icon {
        color: #dc3545;
    }

    .revision-nav-count {
        margin-left: auto;
        min-width: 18px;
        padding: 0 5px;
        color: #fff;
        font-size: 10px;
        line-height: 16px;
        text-align: center;
        background: #dc3545;
        border-radius: 8px;
        flex-shrink: 0;
    }

    .vendor-profile-content {
        min-width: 0;
        max-width: 100%;
        box-sizing: border-box;
    }

    .vendor-profile-categories {
        padding: 12px 16px;
        margin-bottom: 16px;
        background: #fff;
        border: 1px solid #e8edf3;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        max-width: 100%;
        box-sizing: border-box;
    }

    .vendor-profile-section {
        scroll-margin-top: 85px;
        padding: 18px 20px;
        margin-bottom: 16px;
        background: #fff;
        border: 1px solid #e8edf3;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        max-width: 100%;
        box-sizing: border-box;
    }

    .vendor-profile-section>.form-section-title:first-child {
        margin-top: 0;
    }

    .vendor-profile-section.has-revision {
        border-color: #f2b8b5;
    }

    .has-revision.form-group,
    .has-revision.doc-item,
    .revision-field-highlight:not(.question-wrapper):not(tr) {
        position: relative;
        padding: 12px;
        background: #fffbea;
        border: 1px solid #dc3545;
        border-radius: 5px;
    }

    .has-revision.question-wrapper,
    .question-wrapper.revision-field-highlight {
        position: relative;
        background: #fffbea !important;
        border: 1px solid #dc3545 !important;
    }

    /* Table row revision styling */
    tr.has-revision td,
    tr.revision-field-highlight td {
        background-color: #fffbea !important;
        border-top: 1px solid #dc3545 !important;
        border-bottom: 1px solid #dc3545 !important;
    }

    tr.has-revision td:first-child,
    tr.revision-field-highlight td:first-child {
        border-left: 4px solid #dc3545 !important;
    }

    tr.has-revision td:last-child,
    tr.revision-field-highlight td:last-child {
        border-right: 1px solid #dc3545 !important;
    }

    .has-revision.form-group::before,
    .has-revision.doc-item::before,
    .has-revision.question-wrapper::before,
    .has-revision:not(tr)::before,
    .revision-field-highlight:not(tr)::before {
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        content: "";
        background: #dc3545;
        border-radius: 4px 0 0 4px;
    }

    .has-revision:not(tr),
    .revision-field-highlight:not(tr),
    .revision-field-highlight .custom-file-label,
    .revision-field-highlight .bootstrap-select>.dropdown-toggle {
        border-color: #dc3545 !important;
    }

    .revision-note-message {
        display: block;
        padding: 6px 8px;
        background: #fff1f0;
        border-radius: 4px;
    }

    .revision-focus-pulse {
        animation: revisionPulse 1.6s ease-out 1;
    }

    @keyframes revisionPulse {
        0% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, .35);
        }

        100% {
            box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
        }
    }

    .vendor-profile-submit {
        position: sticky;
        bottom: 12px;
        z-index: 5;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 14px 18px;
        background: #fff;
        border: 1px solid #d8e8f7;
        border-radius: 8px;
        box-shadow: 0 6px 20px rgba(24, 28, 50, 0.1);
        max-width: 100%;
        box-sizing: border-box;
    }

    /* Khusus Smartphone (< 768px): Stack 1-kolom yang terkontrol rapi tanpa overflow */
    @media (max-width: 767.98px) {
        .vendor-profile-layout {
            display: flex !important;
            flex-direction: column !important;
            gap: 12px !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
        }

        .vendor-profile-nav {
            position: sticky;
            top: 55px;
            z-index: 90;
            display: flex;
            gap: 6px;
            overflow-x: auto;
            white-space: nowrap;
            max-height: none;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
            padding: 8px 10px;
            background: #fff;
            border: 1px solid #e8edf3;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            scrollbar-width: none;
            -ms-overflow-style: none;
            -webkit-overflow-scrolling: touch;
        }

        .vendor-profile-nav::-webkit-scrollbar {
            display: none;
        }

        .vendor-profile-nav-title {
            display: none;
        }

        .vendor-profile-nav a {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            padding: 5px 11px;
            font-size: 11.5px;
            color: #5e6278;
            background: #f3f6f9;
            border-left: none;
            border: 1px solid transparent;
            border-radius: 20px;
        }

        .vendor-profile-nav a .nav-icon {
            font-size: 11px;
            margin-right: 5px;
            width: auto;
        }

        .vendor-profile-nav a .nav-text {
            white-space: nowrap;
        }

        .vendor-profile-nav a:hover,
        .vendor-profile-nav a:focus {
            color: #0f6fc6;
            background: #e1f0ff;
            border-color: #b5d8ff;
            border-left: 1px solid #b5d8ff;
        }

        .vendor-profile-nav a.active {
            color: #fff;
            background: #0f6fc6;
            border-color: #0f6fc6;
            border-left: 1px solid #0f6fc6;
        }

        .vendor-profile-nav a.active .nav-icon {
            color: #fff;
        }

        .vendor-profile-nav a.has-revision {
            color: #b42318;
            background: #fff8e1;
            border-color: #dc3545;
            border-left: 1px solid #dc3545;
        }

        .vendor-profile-nav a.has-revision .nav-icon {
            color: #dc3545;
        }

        .revision-nav-count {
            margin-left: 6px;
        }

        .vendor-profile-section {
            scroll-margin-top: 115px;
        }
    }

    @media (max-width: 575.98px) {

        .vendor-profile-header,
        .vendor-profile-submit {
            align-items: stretch;
            flex-direction: column;
        }

        .vendor-profile-status {
            max-width: none;
            text-align: left;
            margin-top: 12px;
        }

        .vendor-profile-status .d-flex {
            justify-content: flex-start !important;
        }

        .vendor-profile-section {
            padding: 14px 12px;
        }

        .vendor-profile-submit .btn {
            width: 100%;
        }
    }
</style>
