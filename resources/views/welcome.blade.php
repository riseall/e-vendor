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

</head>

<body>
    <!-- Navbar Start -->
    <header id="topnav" class="defaultscroll sticky">
        <div class="container">
            <!-- Logo container-->
            <a class="logo" href="index.html">
                <span class="logo-light-mode">
                    <img src="{{ asset('images/evendor-clr.png') }}" class="l-dark" height="40" alt="">
                    <img src="{{ asset('images/evendor-wht.png') }}" class="l-light" height="40" alt="">
                </span>
                <img src="{{ asset('images/evendor-wht.png') }}" height="40" class="logo-dark-mode" alt="">
            </a>

            <!-- End Logo container-->
            <div class="menu-extras">
                <div class="menu-item">
                    <!-- Mobile menu toggle-->
                    <a class="navbar-toggle" id="isToggle" onclick="toggleMenu()">
                        <div class="lines">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </a>
                    <!-- End mobile menu toggle-->
                </div>
            </div>

            <!--Login button Start-->
            <ul class="buy-button list-inline mb-0">

                <li class="list-inline-item mb-0">
                    <div class="dropdown">
                        <!--begin::Toggle-->
                        <div class="topbar-item" data-toggle="dropdown" data-offset="10px,0px">
                            <div class="btn btn-icon btn-pill btn-dropdown btn-lg mr-1 flag">
                                @if (App::getLocale() == 'id')
                                    <img class="h-20px w-20px rounded-sm" src="{{ asset('icon/004-indonesia.svg') }}"
                                        alt="Indonesia" />
                                @elseif (App::getLocale() == 'en')
                                    <img class="h-20px w-20px rounded-sm" src="{{ asset('icon/012-uk.svg') }}"
                                        alt="English" />
                                @elseif (App::getLocale() == 'zh')
                                    <img class="h-20px w-20px rounded-sm" src="{{ asset('icon/015-china.svg') }}"
                                        alt="中国人" />
                                @endif
                            </div>
                        </div>
                        <!--end::Toggle-->
                        <!--begin::Dropdown-->
                        <div class="dropdown-menu p-0 m-0 dropdown-menu-anim-up dropdown-menu-sm dropdown-menu-right">
                            <ul class="navi navi-hover py-4">

                                <li class="navi-item {{ App::getLocale() == 'id' ? 'active' : '' }}">
                                    <a rel="alternate" hreflang="id"
                                        href="{{ LaravelLocalization::getLocalizedURL('id', null, [], true) }}"
                                        class="navi-link">
                                        <span class="symbol symbol-20 mr-3">
                                            <img src="{{ asset('icon/004-indonesia.svg') }}" alt="Indonesia" />
                                        </span>
                                        <span class="navi-text">Bahasa</span>
                                    </a>
                                </li>

                                <li class="navi-item {{ App::getLocale() == 'en' ? 'active' : '' }}">
                                    <a rel="alternate" hreflang="en"
                                        href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true) }}"
                                        class="navi-link">
                                        <span class="symbol symbol-20 mr-3">
                                            <img src="{{ asset('icon/012-uk.svg') }}" alt="English" />
                                        </span>
                                        <span class="navi-text">English</span>
                                    </a>
                                </li>

                                <li class="navi-item {{ App::getLocale() == 'zh' ? 'active' : '' }}">
                                    <a rel="alternate" hreflang="zh"
                                        href="{{ LaravelLocalization::getLocalizedURL('zh', null, [], true) }}"
                                        class="navi-link">
                                        <span class="symbol symbol-20 mr-3">
                                            <img src="{{ asset('icon/015-china.svg') }}" alt="中国人" />
                                        </span>
                                        <span class="navi-text">中国人</span>
                                    </a>
                                </li>

                            </ul>
                        </div>
                        <!--end::Dropdown-->
                    </div>
                </li>

                <li class="list-inline-item mb-0">
                    <a href="{{ route('login') }}">
                        <div class="login-btn-primary"><span
                                class="btn btn-light-primary font-weight-bolder btn-pill font-size-h6 px-7">{{ __('login') }}</span>
                        </div>
                        <div class="login-btn-light"><span
                                class="btn btn-light font-weight-bolder btn-pill font-size-h6 px-7 btn-text-dark btn-hover-text-primary">{{ __('login') }}</span>
                        </div>
                    </a>
                </li>

                <li class="list-inline-item ps-1 mb-0">
                    <a href="{{ route('register') }}">
                        <div class="login-btn-primary"><span
                                class="btn btn-primary font-weight-bolder btn-pill font-size-h6 px-7">{{ __('register') }}</span>
                        </div>
                        <div class="login-btn-light"><span
                                class="btn btn-light font-weight-bolder btn-pill font-size-h6 px-7 btn-text-dark btn-hover-text-primary">{{ __('register') }}</span>
                        </div>
                    </a>
                </li>
            </ul>
            <!--Login button End-->

            <div id="navigation">
                <!-- Navigation Menu-->
                <ul class="navigation-menu nav-light">
                    <li><a href="{{ route('welcome') }}" class="sub-menu-item">{{ __('home') }}</a></li>
                    <li><a href="javascript:;" class="sub-menu-item">{{ __('tutorial') }}</a></li>
                    <li><a href="javascript:;" class="sub-menu-item">{{ __('contact') }}</a></li>
                </ul><!--end navigation menu-->
            </div><!--end navigation-->
        </div><!--end container-->
    </header><!--end header-->
    <!-- Navbar End -->

    <!-- Hero Start -->
    <section class="bg-half-260 bg-primary d-table w-100"
        style="background: url({{ asset('images/stock.jpg') }}) center center;">
        <div class="bg-overlay"></div>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="title-heading text-center mt-4">
                        <h1 class="display-4 title-dark font-weight-very-bold text-white mb-3">Portal Resmi Mitra
                            Vendor<br>PT
                            Phapros Tbk.</h1>
                        <p class="para-desc mx-auto text-white-50 font-size-h5">Tingkatkan kolaborasi dan kembangkan
                            bisnis Anda
                            bersama kami melalui
                            sistem pengadaan barang dan jasa yang terintegrasi, transparan, dan efisien.</p>
                        <div class="mt-4 pt-2">
                            <a href="{{ route('register') }}"
                                class="btn btn-primary font-weight-bolder btn-pill font-size-h6 px-7"><i
                                    class="uil uil-user-plus"></i>Daftar Sekarang</a>
                        </div>
                    </div>
                </div><!--end col-->
            </div><!--end row-->
        </div><!--end container-->
    </section><!--end section-->
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
            <div class="row justify-content-center">
                <div class="col-12 text-center">
                    <div class="section-title mb-4 pb-2">
                        <h4 class="title mb-4">Mengapa Menggunakan E-Vendor?</h4>
                        <p class="text-muted para-desc mx-auto mb-0">Platform digital kami dirancang
                            dengan prinsip
                            <span class="text-primary font-weight-bold">Good Corporate Governance (GCG)</span> untuk
                            memastikan
                            kenyamanan dan
                            keamanan transaksi mitra kerja kami.
                        </p>
                    </div>
                </div>
            </div>

            <div class="row mt-15">
                <div class="col-md-4 col-12 mt-5 pt-4">
                    <div class="features feature-primary text-center">
                        <div class="image position-relative d-inline-block">
                            <i class="far fa-address-card font-size-xxl text-primary"></i>
                        </div>
                        <div class="content mt-4">
                            <h5>Registrasi Mandiri</h5>
                            <p class="text-muted mb-0">Proses pendaftaran vendor yang mudah. Kelola profil perusahaan
                                dan perbarui
                                dokumen legalitas Anda secara mandiri di dalam sistem.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-12 mt-5 pt-4">
                    <div class="features feature-primary text-center">
                        <div class="image position-relative d-inline-block">
                            <i class="fas fa-clipboard-list font-size-xxl text-primary"></i>
                        </div>
                        <div class="content mt-4">
                            <h5>Transparansi Pengadaan</h5>
                            <p class="text-muted mb-0">Akses informasi pengadaan, evaluasi penawaran, hingga pengumuman
                                pemenang
                                secara terbuka dan dapat dipertanggungjawabkan.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-12 mt-5 pt-4">
                    <div class="features feature-primary text-center">
                        <div class="image position-relative d-inline-block">
                            <i class="fab fa-delicious font-size-xxl text-primary"></i>
                        </div>
                        <div class="content mt-4">
                            <h5>Manajemen Dokumen & Data</h5>
                            <p class="text-muted mb-0">Sistem penyimpanan terpusat (*cloud-based*) yang aman untuk
                                melindungi setiap
                                dokumen kualifikasi dan penawaran harga Anda.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-12 mt-5 pt-4">
                    <div class="features feature-primary text-center">
                        <div class="image position-relative d-inline-block">
                            <i class="fab fa-delicious font-size-xxl text-primary"></i>
                        </div>
                        <div class="content mt-4">
                            <h5>Antarmuka Ramah Pengguna</h5>
                            <p class="text-muted mb-0">Dirancang dengan antarmuka yang intuitif sehingga mudah
                                dioperasikan oleh
                                seluruh mitra vendor dari berbagai perangkat.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-12 mt-5 pt-4">
                    <div class="features feature-primary text-center">
                        <div class="image position-relative d-inline-block">
                            <i class="fab fa-delicious font-size-xxl text-primary"></i>
                        </div>
                        <div class="content mt-4">
                            <h5>Informasi Terjadwal</h5>
                            <p class="text-muted mb-0">Dapatkan notifikasi dan pantau jadwal tahapan tender secara
                                berkala agar Anda
                                tidak tertinggal informasi penting.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-12 mt-5 pt-4">
                    <div class="features feature-primary text-center">
                        <div class="image position-relative d-inline-block">
                            <i class="fab fa-delicious font-size-xxl text-primary"></i>
                        </div>
                        <div class="content mt-4">
                            <h5>Efisiensi Waktu</h5>
                            <p class="text-muted mb-0">Pangkas birokrasi dan administrasi manual. Seluruh alur
                                persetujuan dan
                                pengiriman dokumen dilakukan secara digital.</p>
                        </div>
                    </div>
                </div>
            </div>
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
    <footer class="footer">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 py-lg-5">
                    <div class="footer-py-60 text-center">
                        <a href="javascript:void(0)" class="logo-footer">
                            <img src="{{ asset('images/evendor-wht.png') }}" height="40" alt="E-Vendor Phapros">
                        </a>
                        <p class="mt-4 para-desc mx-auto text-light font-size-lg">E-Vendor adalah Portal Pengadaan
                            Barang & Jasa
                            resmi dari PT
                            Phapros Tbk. Kami berkomitmen untuk membangun kemitraan bisnis yang berintegritas,
                            transparan, dan saling
                            menguntungkan.</p>

                        <ul class="list-unstyled social-icon foot-social-icon mb-0 mt-4">
                            <li class="list-inline-item mb-0"><a href="https://www.facebook.com/Phapros"
                                    target="_blank" class="btn btn-icon btn-facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a></li>
                            <li class="list-inline-item mb-0"><a href="https://www.instagram.com/ptphaprostbk"
                                    target="_blank" class="btn btn-icon btn-instagram">
                                    <i class="fab fa-instagram"></i></a></li>
                            <li class="list-inline-item mb-0"><a
                                    href="https://www.linkedin.com/company/pt-phapros-tbk/" target="_blank"
                                    class="btn btn-icon btn-linkedin">
                                    <i class="fab fa-linkedin-in"></i></a></li>
                        </ul>
                    </div>
                </div><!--end col-->
            </div><!--end row-->
        </div><!--end container-->

        <div class="footer-py-30 footer-border">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0">©
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> <span class="text-muted font-weight-bold mr-2">PT. Phapros,
                                    Tbk. - All rights reserved.</span>
                            </p>
                        </div>
                    </div><!--end col-->
                </div><!--end row-->
            </div><!--end container-->
        </div>
    </footer><!--end footer-->
    <!-- Footer End -->

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('plugins/plugins.bundle.js') }}"></script>
    <!--Note: All important javascript like page loader, menu, sticky menu, menu-toggler, one page menu etc. -->
</body>

</html>
