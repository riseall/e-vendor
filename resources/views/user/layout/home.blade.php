<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>E-Vendor - Vendor Management System PT Phapros Tbk.</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- favicon -->
    <link rel="shortcut icon" href="{{ asset('images/evendor-logo.png') }}">

    <!-- Bootstrap Css -->
    <link href="{{ asset('plugins/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/style.bundle.css') }}" id="bootstrap-style" class="theme-opt" rel="stylesheet"
        type="text/css">
    <!-- Style Css-->
    <link href="{{ asset('css/style.css') }}" id="color-opt" class="theme-opt" rel="stylesheet" type="text/css">
    @stack('styles')

</head>

<body>
    <!-- Navbar Start -->
    @include('user.layout.partial.navbar')
    <!-- Navbar End -->

    <!-- Hero Start -->
    @if (request()->routeIs('welcome'))
        @include('user.layout.partial.hero')
    @else
        @include('user.layout.partial.hero2')
    @endif
    <!-- Hero End -->

    <!-- Shape Start -->
    <div class="position-relative">
        <div class="shape overflow-hidden text-color-white">
            <svg viewBox="0 0 2880 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 48H1437.5H2880V0H2160C1442.5 52 720 0 720 0H0V48Z" fill="currentColor"></path>
            </svg>
        </div>
    </div>
    <!--Shape End-->

    <!-- Feature Start -->
    <section class="section">
        <div class="container font-size-h6">
            @yield('content')
        </div><!--end container-->
    </section><!--end section-->
    <!-- Feature End -->

    <!-- Shape Start -->
    <div class="position-relative">
        <div class="shape overflow-hidden text-footer">
            <svg viewBox="0 0 2880 48" fill="#202942" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 48H1437.5H2880V0H2160C1442.5 52 720 0 720 0H0V48Z" fill="#202942"></path>
            </svg>
        </div>
    </div>
    <!--Shape End-->


    <!-- Footer Start -->
    @include('user.layout.partial.footer')
    <!-- Footer End -->

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('plugins/plugins.bundle.js') }}"></script>
    <!--Note: All important javascript like page loader, menu, sticky menu, menu-toggler, one page menu etc. -->
</body>

</html>
