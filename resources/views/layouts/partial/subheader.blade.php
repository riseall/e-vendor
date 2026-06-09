<div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <!--begin::Info-->
        <div class="d-flex align-items-center flex-wrap mr-2">
            <!--begin::Page Title-->
            <div class="mb-3"
                style="font-size: 11px; font-weight: 600; letter-spacing: 0.5px; color: #B5B5C3; text-transform: uppercase;">
                {{ __('dashboard') }}
                @hasSection('breadcrumb')
                    <span class="mx-1" style="color:#E4E6EF">·</span>
                    <span style="color:#7E8299">@yield('breadcrumb')</span>
                @endif
            </div>
            <!--end::Page Title-->
        </div>
        <!--end::Info-->
        <!--begin::Toolbar-->
        <div class="d-flex align-items-center">
        </div>
        <!--end::Toolbar-->
    </div>
</div>
