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
