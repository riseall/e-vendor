<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>E-Vendor - {{ $title }}</title>
    <meta name="description" content="Updates and statistics" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--end::Page Vendors Styles-->
    <!--begin::Global Theme Styles(used by all pages)-->
    <link href="{{ asset('plugins/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Global Theme Styles-->
    <!--begin::Layout Themes(used by all pages)-->
    <link href="{{ asset('css/header/light.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/header/menu/light.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/brand/dark.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/aside/dark.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Layout Themes-->
    <!--begin::Page Vendors Styles(used by this page)-->
    <link href="{{ asset('css/admin/vms.css') }}?v={{ filemtime(public_path('css/admin/vms.css')) }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Page Vendors Styles-->
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" href="{{ asset('images/evendor-logo.png') }}" sizes="32x32">
    <link rel="icon" type="image/png" href="{{ asset('images/evendor-logo.png') }}" sizes="192x192">
    <link rel="apple-touch-icon" href="{{ asset('images/evendor-logo.png') }}">

    @stack('style')
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body"
    class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">

    <div class="page-loader page-loader-logo">
        <h1 style="font-size:7rem">E-VENDOR</h1>
        <h4>Vendor Management System</h4>
        <div class="spinner spinner-primary"></div>
    </div>

    <!--begin::Main-->
    <!--begin::Header Mobile-->
    @include('layouts.partial.header-mobile')
    <!--end::Header Mobile-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Page-->
        <div class="d-flex flex-row flex-column-fluid page">
            <!--begin::Aside-->
            @include('layouts.partial.aside')
            <!--end::Aside-->
            <!--begin::Wrapper-->
            <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
                <!--begin::Header-->
                @include('layouts.partial.header')
                <!--end::Header-->
                <!--begin::Content-->
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <!--begin::Subheader-->
                    {{-- @include('layouts.partial.subheader') --}}
                    <!--end::Subheader-->
                    <!--begin::Entry-->
                    <div class="d-flex flex-column-fluid">
                        <!--begin::Container-->
                        {{-- Page Header (muncul otomatis jika @section('page_title') diisi) --}}
                        <div class="container">
                            @hasSection('page_title')
                                <div class="page-header-wrapper mb-8">

                                    {{-- Breadcrumb --}}
                                    <div class="mb-3 text-primary"
                                        style="font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase;">
                                        @hasSection('breadcrumb')
                                            <span class="mx-1"><i class="flaticon2-shield text-primary"></i></span>
                                            <span class="">@yield('step')</span>
                                        @endif
                                    </div>

                                    {{-- Title & Desc --}}
                                    <h3 class="font-weight-bolder text-dark mb-1" style="font-size: 1.5rem">
                                        @yield('page_title')
                                    </h3>
                                    @hasSection('page_desc')
                                        <p class="text-muted mb-0" style="font-size: 13px; max-width: 600px;">
                                            @yield('page_desc')
                                        </p>
                                    @endif

                                </div>
                            @endif

                            @yield('content')
                        </div>
                        <!--end::Container-->
                    </div>
                    <!--end::Entry-->
                </div>
                <!--end::Content-->
                <!--begin::Footer-->
                @include('layouts.partial.footer')
                <!--end::Footer-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::Main-->
    <!-- begin::User Panel-->
    @include('layouts.partial.userpanel')
    <!-- end::User Panel-->
    <!--begin::Scrolltop-->
    @include('layouts.partial.scroll')
    <!--end::Scrolltop-->
    <!--begin::Global Config(global config for global JS scripts)-->
    <script>
        var KTAppSettings = {
            "breakpoints": {
                "sm": 576,
                "md": 768,
                "lg": 992,
                "xl": 1200,
                "xxl": 1200
            },
            "colors": {
                "theme": {
                    "base": {
                        "white": "#ffffff",
                        "primary": "#3699FF",
                        "secondary": "#0097a7",
                        "success": "#1BC5BD",
                        "info": "#8950FC",
                        "warning": "#FFA800",
                        "danger": "#F64E60",
                        "light": "#F3F6F9",
                        "dark": "#212121"
                    },
                    "light": {
                        "white": "#ffffff",
                        "primary": "#E1F0FF",
                        "secondary": "#ECF0F3",
                        "success": "#C9F7F5",
                        "info": "#EEE5FF",
                        "warning": "#FFF4DE",
                        "danger": "#FFE2E5",
                        "light": "#F3F6F9",
                        "dark": "#D6D6E0"
                    },
                    "inverse": {
                        "white": "#ffffff",
                        "primary": "#ffffff",
                        "secondary": "#212121",
                        "success": "#ffffff",
                        "info": "#ffffff",
                        "warning": "#ffffff",
                        "danger": "#ffffff",
                        "light": "#464E5F",
                        "dark": "#ffffff"
                    }
                },
                "gray": {
                    "gray-100": "#F3F6F9",
                    "gray-200": "#ECF0F3",
                    "gray-300": "#0097a7",
                    "gray-400": "#D6D6E0",
                    "gray-500": "#B5B5C3",
                    "gray-600": "#80808F",
                    "gray-700": "#464E5F",
                    "gray-800": "#1B283F",
                    "gray-900": "#212121"
                }
            },
            "font-family": "Poppins"
        };
    </script>
    <!--end::Global Config-->
    <!--begin::Localization Config-->
    <script>
        window.i18n = @json(
            file_exists(resource_path('lang/' . app()->getLocale() . '.json'))
                ? json_decode(file_get_contents(resource_path('lang/' . app()->getLocale() . '.json')), true)
                : []
        );
        window.__ = function (key, replace = {}) {
            let text = (window.i18n && window.i18n[key]) ? window.i18n[key] : key;
            for (const [k, v] of Object.entries(replace)) {
                text = text.replace(':' + k, v);
            }
            return text;
        };
        var __ = window.__;
    </script>
    <!--end::Localization Config-->
    <!--begin::Global Theme Bundle(used by all pages)-->
    <script src="{{ asset('plugins/plugins.bundle.js') }}"></script>
    <script src="{{ asset('js/scripts.bundle.js') }}"></script>
    <script src="{{ asset('plugins/datatables/datatables.bundle.js') }}?v={{ filemtime(public_path('plugins/datatables/datatables.bundle.js')) }}"></script>
    <script>
        // Global Bootstrap-Select configuration
        if (window.jQuery && $.fn.selectpicker) {
            $.fn.selectpicker.Constructor.DEFAULTS.container = 'body';
            $.fn.selectpicker.Constructor.DEFAULTS.display = 'static';
            $.fn.selectpicker.Constructor.DEFAULTS.dropupAuto = false;
            if ($.fn.selectpicker.defaults) {
                $.fn.selectpicker.defaults.container = 'body';
                $.fn.selectpicker.defaults.display = 'static';
                $.fn.selectpicker.defaults.dropupAuto = false;
            }
        }

        // ponytail: tutup dropdown saat scroll container (tabel/window) agar posisi tidak melayang/bergeser
        window.addEventListener('scroll', function(e) {
            if (e.target && e.target.closest && e.target.closest('.dropdown-menu')) return;
            if (window.jQuery) {
                $('.bootstrap-select.show [data-toggle="dropdown"]').dropdown('toggle');
            }
        }, true);
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const MAX_BYTES = 5 * 1024 * 1024; // 5 MB, mirrors server
            const ALLOWED = /\.(pdf|jpg|jpeg|png)$/i;
            const ROUTE = @json(route('registrasi.upload.temp'));

            function statusFor(input) {
                // Cari .upload-status terdekat (sibling atau parent .custom-file)
                return input.closest('.form-group')?.querySelector('.upload-status') ||
                    input.closest('.custom-file')?.querySelector('.upload-status') ||
                    input.closest('div')?.querySelector('.upload-status') ||
                    input.parentElement.querySelector('.upload-status') ||
                    null;
            }

            function setStatus(el, color, text) {
                if (!el) return;
                el.style.display = (el.tagName === 'DIV' || el.classList.contains('d-block')) ? 'block' : 'inline';
                el.style.color = color;
                el.style.fontSize = '0.78rem';
                el.innerText = text;
            }

            const STATUS_READY_TEXT = 'Siap submit';

            function ensureHidden(input) {
                let targetId = input.getAttribute('data-target');
                if (!targetId) {
                    targetId = (input.name || input.getAttribute('data-field') || 'file') + '__hidden';
                    if (input.hasAttribute('multiple')) targetId += '[]';
                    input.setAttribute('data-target', targetId);
                }
                let hidden = document.getElementById(targetId);
                if (!hidden) {
                    hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.id = targetId;
                    hidden.name = (input.name || input.getAttribute('data-field') || 'file');
                    if (input.hasAttribute('multiple')) hidden.name += '[]';
                    input.insertAdjacentElement('afterend', hidden);
                }
                return hidden;
            }

            function uploadOne(file, fieldName) {
                const fd = new FormData();
                fd.append('file', file);
                fd.append('field_name', fieldName);
                fd.append('_token', '{{ csrf_token() }}');
                return fetch(ROUTE, {
                        method: 'POST',
                        body: fd
                    })
                    .then(async r => {
                        if (r.status === 419) throw new Error('Sesi berakhir, refresh halaman.');
                        const data = await r.json().catch(() => ({}));
                        if (!r.ok || !data.success) throw new Error(data.message || 'Upload gagal');
                        return data;
                    });
            }

            document.addEventListener('change', async function(e) {
                if (!e.target.matches('input.ajax-file-upload[type="file"]')) return;

                const files = Array.from(e.target.files || []);
                if (files.length === 0) return;

                const status = statusFor(e.target);
                const fieldName = e.target.getAttribute('data-field') || e.target.name || 'file';
                const isMulti = e.target.hasAttribute('multiple') || files.length > 1;

                // Validasi client cepat
                for (const f of files) {
                    if (f.size > MAX_BYTES) {
                        setStatus(status, 'red', `${f.name} > 5MB.`);
                        e.target.value = '';
                        return;
                    }
                    if (!ALLOWED.test(f.name)) {
                        setStatus(status, 'red', `${f.name}: format tidak didukung.`);
                        e.target.value = '';
                        return;
                    }
                }

                setStatus(status, 'blue', `Mengunggah ${files.length} file...`);
                const hidden = ensureHidden(e.target);

                try {
                    if (isMulti) {
                        // Multi: append tiap path (hidden name sudah [] )
                        hidden.value = '';
                        const paths = [];
                        for (let i = 0; i < files.length; i++) {
                            setStatus(status, 'blue',
                                `Mengunggah ${i+1}/${files.length}...`);
                            const data = await uploadOne(files[i], fieldName);
                            paths.push(data.path);
                        }
                        hidden.value = paths.join(','); // server split saat simpan

                        // AMBIL SEMUA NAMA FILE & GABUNGKAN DENGAN KOMA
                        const fileNames = files.map(f => f.name).join(', ');
                        setStatus(status, 'green', `✓ Terunggah: ${fileNames}`);

                    } else {
                        const data = await uploadOne(files[0], fieldName);
                        hidden.value = data.path;

                        // TAMPILKAN NAMA FILE TUNGGAL YANG DI-UPLOAD
                        setStatus(status, 'green', `✓ Terunggah: ${files[0].name}`);
                    }

                    e.target.value = '';
                } catch (err) {
                    setStatus(status, 'red', err.message || 'Gagal.');
                    e.target.value = '';
                }
            });
        });
    </script>
    <!--end::Global Theme Bundle-->
    @stack('scripts')
    <!--end::Page Scripts-->
</body>
<!--end::Body-->

</html>
