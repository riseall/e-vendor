<div id="kt_header" class="header header-fixed">
    <!--begin::Container-->
    <div class="container-fluid d-flex align-items-stretch justify-content-between">
        <!--begin::Header Menu Wrapper-->
        <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
        </div>
        <!--end::Header Menu Wrapper-->
        <!--begin::Topbar-->
        <div class="topbar">
            <!--begin::Languages-->
            <div class="dropdown">
                <!--begin::Toggle-->
                <div class="topbar-item" data-toggle="dropdown" data-offset="10px,0px">
                    <div class="btn btn-icon btn-clean btn-dropdown btn-lg mr-1">
                        @if (App::getLocale() == 'id')
                            <img class="h-20px w-20px rounded-sm" src="{{ asset('icon/004-indonesia.svg') }}"
                                alt="Indonesia" />
                        @elseif (App::getLocale() == 'en')
                            <img class="h-20px w-20px rounded-sm" src="{{ asset('icon/012-uk.svg') }}" alt="English" />
                        @elseif (App::getLocale() == 'zh')
                            <img class="h-20px w-20px rounded-sm" src="{{ asset('icon/015-china.svg') }}"
                                alt="中国人" />
                        @endif
                    </div>
                </div>
                <!--end::Toggle-->
                <!--begin::Dropdown-->
                <div class="dropdown">
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
                </div>
                <!--end::Dropdown-->
            </div>
            <!--end::Languages-->
            <!--begin::User-->
            <div class="topbar-item">
                <div class="btn btn-icon w-auto btn-clean d-flex align-items-center btn-lg px-2"
                    id="kt_quick_user_toggle">
                    {{-- <span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span> --}}
                    <span
                        class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3">{{ Auth::user()->name }}</span>
                    <span class="symbol symbol-35 symbol-success">
                        <span
                            class="symbol-label font-size-h5 font-weight-bold">{{ collect(explode(' ', Auth::user()->name))->take(2)->map(fn($word) => strtoupper($word[0]))->implode('') }}</span>
                    </span>
                </div>
            </div>
            <!--end::User-->
        </div>
        <!--end::Topbar-->
    </div>
    <!--end::Container-->
</div>
