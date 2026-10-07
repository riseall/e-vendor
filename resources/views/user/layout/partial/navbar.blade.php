<header id="topnav" class="defaultscroll sticky">
    <div class="container">
        <!-- Logo container-->
        <a class="logo" href="{{ route('welcome') }}">
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
                @include('layouts.partial.language')
            </li>

            @auth
                @php
                    $navUser = Auth::user();
                    $navUserName = $navUser->name ?? 'User';
                @endphp
                <li class="list-inline-item mb-0">
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle text-decoration-none d-inline-flex align-items-center" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <div class="login-btn-primary">
                                <span class="d-inline-flex align-items-center font-weight-bold font-size-h6 text-primary">
                                    <span class="text-truncate mr-1" style="max-width: 160px;">{{ $navUserName }}</span>
                                    <i class="fas fa-chevron-down font-size-xs ml-1"></i>
                                </span>
                            </div>
                            <div class="login-btn-light">
                                <span class="d-inline-flex align-items-center font-weight-bold font-size-h6 text-white text-hover-primary">
                                    <span class="text-truncate mr-1" style="max-width: 160px;">{{ $navUserName }}</span>
                                    <i class="fas fa-chevron-down font-size-xs ml-1"></i>
                                </span>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right p-0 dropdown-menu-anim-up shadow-sm border" style="min-width: 220px; line-height: normal; margin-top: 10px;">
                            <div class="px-4 py-3 border-bottom">
                                <div class="font-weight-bolder text-dark font-size-sm text-truncate">{{ $navUserName }}</div>
                                <div class="text-muted font-size-xs text-truncate">{{ $navUser->email }}</div>
                            </div>
                            <ul class="navi navi-hover py-2">
                                <li class="navi-item">
                                    <a href="{{ route('dashboard') }}" class="navi-link">
                                        <span class="navi-icon mr-3">
                                            <i class="fas fa-th-large text-primary"></i>
                                        </span>
                                        <span class="navi-text font-weight-bold text-dark">{{ __('dashboard') }}</span>
                                    </a>
                                </li>
                                <li class="dropdown-divider my-1"></li>
                                <li class="navi-item">
                                    <a href="{{ route('logout') }}" class="navi-link" onclick="event.preventDefault(); document.getElementById('nav-logout-form').submit();">
                                        <span class="navi-icon mr-3">
                                            <i class="fas fa-sign-out-alt text-danger"></i>
                                        </span>
                                        <span class="navi-text font-weight-bold text-danger">{{ __('logout') }}</span>
                                    </a>
                                </li>
                            </ul>
                            <form id="nav-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </li>
            @else
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
                    <a href="{{ route('login') }}#signup">
                        <div class="login-btn-primary"><span
                                class="btn btn-primary font-weight-bolder btn-pill font-size-h6 px-7">{{ __('register') }}</span>
                        </div>
                        <div class="login-btn-light"><span
                                class="btn btn-light font-weight-bolder btn-pill font-size-h6 px-7 btn-text-dark btn-hover-text-primary">{{ __('register') }}</span>
                        </div>
                    </a>
                </li>
            @endauth
        </ul>
        <!--Login button End-->

        <div id="navigation">
            <!-- Navigation Menu-->
            <ul class="navigation-menu nav-light">
                <li><a href="{{ route('welcome') }}" class="sub-menu-item">{{ __('home') }}</a></li>
                <li><a href="{{ route('tutorial') }}" class="sub-menu-item">{{ __('tutorial') }}</a></li>
                <li><a href="{{ route('contact') }}" class="sub-menu-item">{{ __('contact') }}</a></li>
            </ul><!--end navigation menu-->
        </div><!--end navigation-->
    </div><!--end container-->
</header><!--end header-->
