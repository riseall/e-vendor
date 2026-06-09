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
    <!--begin::Global Theme Bundle(used by all pages)-->
    <script src="{{ asset('plugins/plugins.bundle.js') }}"></script>
    <script src="{{ asset('js/scripts.bundle.js') }}"></script>
    <!--end::Global Theme Bundle-->
    @stack('scripts')
    <!--end::Page Scripts-->
</body>
<!--end::Body-->

</html>
