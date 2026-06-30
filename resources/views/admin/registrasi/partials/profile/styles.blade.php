<style>
    html {
        scroll-behavior: smooth;
    }

    .vendor-profile-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 24px;
        padding: 24px;
        margin-bottom: 24px;
        background: #fff;
        border-left: 4px solid #0f6fc6;
        border-bottom: 1px solid #e8edf3;
    }

    .vendor-profile-eyebrow,
    .vendor-profile-nav-title {
        color: #7e8299;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .vendor-profile-number {
        color: #0f6fc6;
        font-size: 22px;
        font-weight: 700;
    }

    .vendor-profile-company {
        margin-top: 4px;
        color: #181c32;
        font-size: 15px;
        font-weight: 600;
    }

    .vendor-profile-status {
        max-width: 360px;
        text-align: right;
    }

    .vendor-profile-layout {
        display: grid;
        grid-template-columns: minmax(210px, 250px) minmax(0, 1fr);
        gap: 24px;
        align-items: start;
    }

    .vendor-profile-nav {
        position: sticky;
        top: 90px;
        padding: 18px 0;
        background: #fff;
        border: 1px solid #e8edf3;
        border-radius: 6px;
    }

    .vendor-profile-nav-title {
        padding: 0 18px 12px;
    }

    .vendor-profile-nav a {
        position: relative;
        display: block;
        padding: 10px 18px;
        color: #5e6278;
        font-size: 13px;
        font-weight: 600;
        border-left: 3px solid transparent;
    }

    .vendor-profile-nav a:hover,
    .vendor-profile-nav a:focus {
        color: #0f6fc6;
        background: #f2f8fd;
        border-left-color: #0f6fc6;
        text-decoration: none;
    }

    .vendor-profile-nav a.has-revision {
        color: #b42318;
        background: #fff8e1;
        border-left-color: #dc3545;
    }

    .revision-nav-count {
        float: right;
        min-width: 20px;
        padding: 1px 6px;
        color: #fff;
        font-size: 10px;
        line-height: 18px;
        text-align: center;
        background: #dc3545;
        border-radius: 9px;
    }

    .vendor-profile-content {
        min-width: 0;
    }

    .vendor-profile-categories {
        padding: 14px 18px;
        margin-bottom: 16px;
        background: #fff;
        border: 1px solid #e8edf3;
        border-radius: 6px;
    }

    .vendor-profile-section {
        scroll-margin-top: 96px;
        padding: 24px;
        margin-bottom: 18px;
        background: #fff;
        border: 1px solid #e8edf3;
        border-radius: 6px;
    }

    .vendor-profile-section>.form-section-title:first-child {
        margin-top: 0;
    }

    .vendor-profile-section.has-revision {
        border-color: #f2b8b5;
    }

    .has-revision.form-group,
    .has-revision.doc-item,
    .revision-field-highlight {
        position: relative;
        padding: 12px;
        background: #fffbea;
        border: 1px solid #dc3545;
        border-radius: 5px;
    }

    .has-revision.form-group::before,
    .has-revision.doc-item::before,
    .revision-field-highlight::before {
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        content: "";
        background: #dc3545;
        border-radius: 4px 0 0 4px;
    }

    .has-revision,
    .revision-field-highlight,
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
        gap: 18px;
        padding: 16px 18px;
        background: #fff;
        border: 1px solid #d8e8f7;
        border-radius: 6px;
        box-shadow: 0 8px 24px rgba(24, 28, 50, 0.12);
    }

    @media (max-width: 991.98px) {
        .vendor-profile-layout {
            grid-template-columns: 1fr;
        }

        .vendor-profile-nav {
            position: static;
            display: flex;
            gap: 4px;
            overflow-x: auto;
            padding: 10px;
        }

        .vendor-profile-nav-title {
            display: none;
        }

        .vendor-profile-nav a {
            flex: 0 0 auto;
            padding: 8px 12px;
            border-left: 0;
            border-bottom: 2px solid transparent;
        }

        .revision-nav-count {
            float: none;
            margin-left: 6px;
        }

        .vendor-profile-nav a:hover,
        .vendor-profile-nav a:focus {
            border-left-color: transparent;
            border-bottom-color: #0f6fc6;
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
        }

        .vendor-profile-section {
            padding: 18px 14px;
        }

        .vendor-profile-submit .btn {
            width: 100%;
        }
    }
</style>
