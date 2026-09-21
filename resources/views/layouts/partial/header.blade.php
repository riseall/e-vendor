<div id="kt_header" class="header header-fixed">
    <!--begin::Container-->
    <div class="container-fluid d-flex align-items-center justify-content-between">
        <!--begin::Header Menu Wrapper-->
        <div class="topbar header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
            {{-- <div
                style="font-size: 11px; font-weight: 600; letter-spacing: 0.5px; color: #B5B5C3; text-transform: uppercase;">
                {{ __('dashboard') }}
                @hasSection('breadcrumb')
                    <span class="mx-1" style="color:#E4E6EF"><i class="fas fa-angle-right icon-sm"></i></span>
                    <span style="color:#7E8299">@yield('breadcrumb')</span>
                @endif
            </div> --}}
        </div>
        <!--end::Header Menu Wrapper-->
        <!--begin::Topbar-->
        <div class="topbar">
            <!--begin::Languages-->
            @include('layouts.partial.language')
            <!--end::Languages-->
            <!--begin::User-->
            <div class="topbar-item">
                <div class="btn btn-icon w-auto btn-light d-flex align-items-center btn-lg px-2"
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
