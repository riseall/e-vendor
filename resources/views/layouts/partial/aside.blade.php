<div class="aside aside-left aside-fixed d-flex flex-column flex-row-auto" id="kt_aside">
    <!--begin::Brand-->
    <div class="brand flex-column-auto" id="kt_brand">
        <!--begin::Logo-->
        <a href="{{ route('dashboard') }}" class="brand-logo">
            <img alt="Logo" class="max-w-125px" src="{{ asset('images/evendor-wht.png') }}" />
        </a>
        <!--end::Logo-->
        <!--begin::Toggle-->
        <button class="brand-toggle btn btn-sm px-0" id="kt_aside_toggle">
            <span class="svg-icon svg-icon svg-icon-xl">
                <!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Angle-double-left.svg-->
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                    height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24" />
                        <path
                            d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z"
                            fill="#000000" fill-rule="nonzero"
                            transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999)" />
                        <path
                            d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z"
                            fill="#000000" fill-rule="nonzero" opacity="0.3"
                            transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999)" />
                    </g>
                </svg>
                <!--end::Svg Icon-->
            </span>
        </button>
        <!--end::Toolbar-->
    </div>
    <!--end::Brand-->
    <!--begin::Aside Menu-->
    <div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
        <!--begin::Menu Container-->
        <div id="kt_aside_menu" class="aside-menu my-4 mx-3" data-menu-vertical="1" data-menu-scroll="1"
            data-menu-dropdown-timeout="500">
            <!--begin::Menu Nav-->
            <ul class="menu-nav">
                <li class="menu-item menu-item-active" aria-haspopup="true">
                    <a href="{{ route('dashboard') }}" class="menu-link">
                        <span class="svg-icon menu-icon">
                            <!--begin::Svg Icon | path:assets/media/svg/icons/Design/Layers.svg-->
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24" />
                                    <path
                                        d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z"
                                        fill="#000000" fill-rule="nonzero" />
                                    <path
                                        d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z"
                                        fill="#000000" opacity="0.3" />
                                </g>
                            </svg>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-text">{{ __('dashboard') }}</span>
                    </a>
                </li>

                @php
                    $latestVendorApplication = \App\Models\VendorApplication::where('user_id', Auth::id())
                        ->where('status', '!=', \App\Models\VendorApplication::STATUS_DRAFT)
                        ->latest('submitted_at')
                        ->latest()
                        ->first();
                    $status = $application->status ?? ($latestVendorApplication->status ?? 'new');
                @endphp
                <li class="menu-item" aria-haspopup="true">
                    <a href="{{ route('registrasi.index') }}" class="menu-link">
                        <span class="svg-icon menu-icon">
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24px" height="24px">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <path fill="currentColor"
                                        d="M 20 3 C 21.105 3 22 3.895 22 5 L 22 19 C 22 20.105 21.105 21 20 21 L 4 21 C 2.895 21 2 20.105 2 19 L 2 5 C 2 3.895 2.895 3 4 3 L 20 3 Z"
                                        opacity=".3" class="duo-icons-secondary-layer" />
                                    <path fill="currentColor"
                                        d="M 17 7 L 14 7 C 13.23 7 12.749 7.833 13.134 8.5 C 13.313 8.809 13.643 9 14 9 L 17 9 C 17.77 9 18.251 8.167 17.866 7.5 C 17.687 7.191 17.357 7 17 7 Z"
                                        style="visibility: hidden;" class="duo-icons-primary-layer" />
                                    <path fill="currentColor" d="M 10 9 L 10 11 L 8 11 L 8 9 L 10 9 Z"
                                        class="duo-icons-primary-layer" />
                                    <path fill="currentColor"
                                        d="M 17 11 L 14 11 C 13.23 11.001 12.75 11.835 13.136 12.501 C 13.293 12.773 13.57 12.956 13.883 12.993 L 14 13 L 17 13 C 17.77 12.999 18.25 12.165 17.864 11.499 C 17.707 11.227 17.43 11.044 17.117 11.007 L 17 11 Z"
                                        class="duo-icons-primary-layer" />
                                    <path fill="currentColor"
                                        d="M 10 7 L 8 7 C 6.953 7 6.083 7.806 6.005 8.85 L 6 9 L 6 11 C 6 12.047 6.806 12.917 7.85 12.995 L 8 13 L 10 13 C 11.047 13 11.917 12.194 11.995 11.15 L 12 11 L 12 9 C 12 7.953 11.194 7.083 10.15 7.005 L 10 7 Z"
                                        class="duo-icons-primary-layer" />
                                    <path fill="currentColor"
                                        d="M 17 15 L 7 15 C 6.23 15 5.749 15.833 6.134 16.5 C 6.313 16.809 6.643 17 7 17 L 17 17 C 17.77 17 18.251 16.167 17.866 15.5 C 17.687 15.191 17.357 15 17 15 Z"
                                        class="duo-icons-primary-layer" />
                                </g>
                            </svg>
                        </span>
                        <span
                            class="menu-text">{{ in_array($status, ['approved', 'verified']) ? 'Profil' : 'Registrasi' }}</span>
                    </a>
                </li>

                {{-- @if ($latestVendorApplication)
                    <li class="menu-item" aria-haspopup="true">
                        <a href="{{ route('registrasi.tracking', $latestVendorApplication->application_number) }}"
                            class="menu-link">
                            <span class="svg-icon menu-icon">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24px"
                                    height="24px">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path fill="currentColor" opacity=".3"
                                            d="M5 4h14c1.105 0 2 .895 2 2v11c0 1.105-.895 2-2 2H5c-1.105 0-2-.895-2-2V6c0-1.105.895-2 2-2z" />
                                        <path fill="currentColor"
                                            d="M7 8h7c.552 0 1 .448 1 1s-.448 1-1 1H7c-.552 0-1-.448-1-1s.448-1 1-1zm0 4h5c.552 0 1 .448 1 1s-.448 1-1 1H7c-.552 0-1-.448-1-1s.448-1 1-1zm11.707-1.707c.391.391.391 1.024 0 1.414l-3 3c-.391.391-1.024.391-1.414 0l-1-1c-.391-.391-.391-1.024 0-1.414s1.024-.391 1.414 0l.293.293 2.293-2.293c.391-.391 1.024-.391 1.414 0z" />
                                    </g>
                                </svg>
                            </span>
                            <span class="menu-text">Tracking Permohonan</span>
                        </a>
                    </li>
                @endif --}}

                @php
                    $activeAudit = \App\Models\VendorAudit::whereHas('application', function ($q) {
                        $q->where('user_id', Auth::id());
                    })
                        ->whereNotIn('status', [
                            \App\Models\VendorAudit::STATUS_COMPLETED,
                            \App\Models\VendorAudit::STATUS_REJECTED,
                        ])
                        ->latest('id')
                        ->first();

                    $hasAuditRecord = \App\Models\VendorAudit::whereHas('application', function ($q) {
                        $q->where('user_id', Auth::id());
                    })->exists();
                @endphp
                @if ($activeAudit && $activeAudit->audit_type === 'on_desk')
                    <li class="menu-item" aria-haspopup="true">
                        <a href="{{ route('vendor.audit.questionnaire', $activeAudit->id) }}" class="menu-link">
                            <span class="svg-icon menu-icon">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24px"
                                    height="24px">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path fill="currentColor"
                                            d="M 7 3 L 7 4 C 7 5.105 7.895 6 9 6 L 15 6 C 16.105 6 17 5.105 17 4 L 17 3 L 18 3 C 19.105 3 20 3.895 20 5 L 20 16 C 20 19.314 17.314 22 14 22 L 6 22 C 4.895 22 4 21.105 4 20 L 4 5 C 4 3.895 4.895 3 6 3 L 7 3 Z"
                                            opacity=".3" class="duo-icons-secondary-layer" />
                                        <path fill="currentColor"
                                            d="M 14 2 C 14.77 2.001 15.25 2.835 14.864 3.501 C 14.707 3.773 14.43 3.956 14.117 3.993 L 14 4 L 10 4 C 9.23 3.999 8.75 3.165 9.136 2.499 C 9.293 2.227 9.57 2.044 9.883 2.007 L 10 2 L 14 2 Z"
                                            class="duo-icons-primary-layer" />
                                        <path fill="currentColor"
                                            d="M 15 10 L 9 10 C 8.23 10.001 7.75 10.835 8.136 11.501 C 8.293 11.773 8.57 11.956 8.883 11.993 L 9 12 L 15 12 C 15.77 12 16.251 11.167 15.866 10.5 C 15.687 10.191 15.357 10 15 10 Z"
                                            class="duo-icons-primary-layer" />
                                        <path fill="currentColor"
                                            d="M 12 14 L 9 14 C 8.23 14 7.749 14.833 8.134 15.5 C 8.313 15.809 8.643 16 9 16 L 12 16 C 12.77 16 13.251 15.167 12.866 14.5 C 12.687 14.191 12.357 14 12 14 Z"
                                            class="duo-icons-primary-layer" />
                                    </g>
                                </svg>
                            </span>
                            <span class="menu-text">
                                Questionnaire
                            </span>
                        </a>
                    </li>
                @endif

                @if ($hasAuditRecord)
                    <li class="menu-item {{ request()->routeIs('vendor.audit.results') ? 'menu-item-active' : '' }}"
                        aria-haspopup="true">
                        <a href="{{ route('vendor.audit.results') }}" class="menu-link">
                            <span class="svg-icon menu-icon">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24px"
                                    height="24px">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path fill="currentColor" opacity=".3"
                                            d="M 6 4 C 4.895 4 4 4.895 4 6 L 4 18 C 4 19.105 4.895 20 6 20 L 18 20 C 19.105 20 20 19.105 20 18 L 20 6 C 20 4.895 19.105 4 18 4 L 6 4 Z"
                                            class="duo-icons-secondary-layer" />
                                        <path fill="currentColor"
                                            d="M 10.5 15 L 7.5 12 C 7.114 11.614 7.114 10.986 7.5 10.6 C 7.886 10.214 8.514 10.214 8.9 10.6 L 10.5 12.2 L 15.1 7.6 C 15.486 7.214 16.114 7.214 16.5 7.6 C 16.886 7.986 16.886 8.614 16.5 9 L 11.2 14.3 C 11.007 14.493 10.745 14.601 10.471 14.601 C 10.198 14.601 9.936 14.493 9.743 14.3 L 10.5 15 Z"
                                            class="duo-icons-primary-layer" />
                                    </g>
                                </svg>
                            </span>
                            <span class="menu-text">Hasil Audit</span>
                        </a>
                    </li>
                @endif

                @hasanyrole(['Super Admin', 'Admin IT'])
                    <li class="menu-section">
                        <h4 class="menu-text">Master</h4>
                        <i class="menu-icon ki ki-bold-more-hor icon-md"></i>
                    </li>
                    <li class="menu-item" aria-haspopup="true">
                        <a href="{{ route('user.index') }}" class="menu-link">
                            <span class="svg-icon menu-icon">
                                <!--begin::Svg Icon | path:assets/media/svg/icons/Home/Library.svg-->
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24" />
                                        <path
                                            d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z"
                                            fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                        <path
                                            d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z"
                                            fill="#000000" fill-rule="nonzero" />
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                            <span class="menu-text">{{ __('users') }}</span>
                        </a>
                    </li>
                    <li class="menu-item" aria-haspopup="true">
                        <a href="{{ route('questionnaire-form.index') }}" class="menu-link">
                            <span class="svg-icon menu-icon">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24px"
                                    height="24px">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M11.5,5 L18.5,5 C19.3284271,5 20,5.67157288 20,6.5 C20,7.32842712 19.3284271,8 18.5,8 L11.5,8 C10.6715729,8 10,7.32842712 10,6.5 C10,5.67157288 10.6715729,5 11.5,5 Z M5.5,17 L18.5,17 C19.3284271,17 20,17.6715729 20,18.5 C20,19.3284271 19.3284271,20 18.5,20 L5.5,20 C4.67157288,20 4,19.3284271 4,18.5 C4,17.6715729 4.67157288,17 5.5,17 Z M5.5,11 L18.5,11 C19.3284271,11 20,11.6715729 20,12.5 C20,13.3284271 19.3284271,14 18.5,14 L5.5,14 C4.67157288,14 4,13.3284271 4,12.5 C4,11.6715729 4.67157288,11 5.5,11 Z"
                                            fill="#000000" opacity="0.3" />
                                        <path
                                            d="M4.82866499,9.40751652 L7.70335558,6.90006821 C7.91145727,6.71855155 7.9330087,6.40270347 7.75149204,6.19460178 C7.73690043,6.17787308 7.72121098,6.16213467 7.70452782,6.14749103 L4.82983723,3.6242308 C4.62230202,3.44206673 4.30638833,3.4626341 4.12422426,3.67016931 C4.04415337,3.76139218 4,3.87862714 4,4.00000654 L4,9.03071508 C4,9.30685745 4.22385763,9.53071508 4.5,9.53071508 C4.62084305,9.53071508 4.73759731,9.48695028 4.82866499,9.40751652 Z"
                                            fill="#000000" />
                                    </g>
                                </svg>
                            </span>
                            <span class="menu-text">Questionnaire</span>
                        </a>
                    </li>
                @endhasanyrole

                @hasanyrole(['Super Admin', 'Admin IT', 'Procurement', 'Verifikator'])
                    <li class="menu-section">
                        <h4 class="menu-text">Procurement</h4>
                        <i class="menu-icon ki ki-bold-more-hor icon-md"></i>
                    </li>
                    <li class="menu-item" aria-haspopup="true">
                        <a href="{{ route('verifikasi.index') }}" class="menu-link">
                            <span class="svg-icon menu-icon">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24px"
                                    height="24px">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z"
                                            fill="#000000" opacity="0.3" />
                                        <path
                                            d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z"
                                            fill="#000000" />
                                        <rect fill="#000000" opacity="0.3" x="10" y="9" width="7" height="2"
                                            rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="7" y="9" width="2" height="2"
                                            rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="7" y="13" width="2" height="2"
                                            rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="10" y="13" width="7" height="2"
                                            rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="7" y="17" width="2" height="2"
                                            rx="1" />
                                        <rect fill="#000000" opacity="0.3" x="10" y="17" width="7" height="2"
                                            rx="1" />
                                    </g>
                                </svg>
                            </span>
                            <span class="menu-text">Verifikasi Vendor</span>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('pengadaan.supplier.*') ? 'menu-item-active' : '' }}"
                        aria-haspopup="true">
                        <a href="{{ route('supplier.index') }}" class="menu-link">
                            <span class="svg-icon menu-icon">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24px"
                                    height="24px">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M6,2 L18,2 C19.6568542,2 21,3.34314575 21,5 L21,19 C21,20.6568542 19.6568542,22 18,22 L6,22 C4.34314575,22 3,20.6568542 3,19 L3,5 C3,3.34314575 4.34314575,2 6,2 Z M12,11 C13.1045695,11 14,10.1045695 14,9 C14,7.8954305 13.1045695,7 12,7 C10.8954305,7 10,7.8954305 10,9 C10,10.1045695 10.8954305,11 12,11 Z M7.00036205,16.4995035 C6.98863236,16.6619875 7.26484009,17 7.4041679,17 C11.463736,17 14.5228466,17 16.5815,17 C16.9988413,17 17.0053266,16.6221713 16.9988413,16.5 C16.8360465,13.4332455 14.6506758,12 11.9907452,12 C9.36772908,12 7.21569918,13.5165724 7.00036205,16.4995035 Z"
                                            fill="#000000" />
                                    </g>
                                </svg>
                            </span>
                            <span class="menu-text">Supplier Terekomendasi</span>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('rekualifikasi.*') ? 'menu-item-active' : '' }}"
                        aria-haspopup="true">
                        <a href="{{ route('rekualifikasi.index') }}" class="menu-link">
                            <span class="svg-icon menu-icon">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24px"
                                    height="24px">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M7.14319965,19.3575259 C7.67122143,19.7615175 8.25104409,20.1012165 8.87097532,20.3649307 L7.89205065,22.0604779 C7.61590828,22.5387706 7.00431787,22.7026457 6.52602525,22.4265033 C6.04773263,22.150361 5.88385747,21.5387706 6.15999985,21.0604779 L7.14319965,19.3575259 Z M15.1367085,20.3616573 C15.756345,20.0972995 16.3358198,19.7569961 16.8634386,19.3524415 L17.8320512,21.0301278 C18.1081936,21.5084204 17.9443184,22.1200108 17.4660258,22.3961532 C16.9877332,22.6722956 16.3761428,22.5084204 16.1000004,22.0301278 L15.1367085,20.3616573 Z"
                                            fill="#000000" />
                                        <path
                                            d="M12,21 C7.581722,21 4,17.418278 4,13 C4,8.581722 7.581722,5 12,5 C16.418278,5 20,8.581722 20,13 C20,17.418278 16.418278,21 12,21 Z M19.068812,3.25407593 L20.8181344,5.00339833 C21.4039208,5.58918477 21.4039208,6.53893224 20.8181344,7.12471868 C20.2323479,7.71050512 19.2826005,7.71050512 18.696814,7.12471868 L16.9474916,5.37539627 C16.3617052,4.78960984 16.3617052,3.83986237 16.9474916,3.25407593 C17.5332781,2.66828949 18.4830255,2.66828949 19.068812,3.25407593 Z M5.29862906,2.88207799 C5.8844155,2.29629155 6.83416297,2.29629155 7.41994941,2.88207799 C8.00573585,3.46786443 8.00573585,4.4176119 7.41994941,5.00339833 L5.29862906,7.12471868 C4.71284263,7.71050512 3.76309516,7.71050512 3.17730872,7.12471868 C2.59152228,6.53893224 2.59152228,5.58918477 3.17730872,5.00339833 L5.29862906,2.88207799 Z"
                                            fill="#000000" opacity="0.3" />
                                        <path
                                            d="M11.9630156,7.5 L12.0475062,7.5 C12.3043819,7.5 12.5194647,7.69464724 12.5450248,7.95024814 L13,12.5 L16.2480695,14.3560397 C16.403857,14.4450611 16.5,14.6107328 16.5,14.7901613 L16.5,15 C16.5,15.2109164 16.3290185,15.3818979 16.1181021,15.3818979 C16.0841582,15.3818979 16.0503659,15.3773725 16.0176181,15.3684413 L11.3986612,14.1087258 C11.1672824,14.0456225 11.0132986,13.8271186 11.0316926,13.5879956 L11.4644883,7.96165175 C11.4845267,7.70115317 11.7017474,7.5 11.9630156,7.5 Z"
                                            fill="#000000" />
                                    </g>
                                </svg>
                            </span>
                            <span class="menu-text">Rekualifikasi Vendor</span>
                        </a>
                    </li>
                @endhasanyrole

                @hasanyrole(['Super Admin', 'Admin IT', 'Quality Assurance', 'Apoteker', 'Specialist'])
                    <li class="menu-section">
                        <h4 class="menu-text">Quality Assurance</h4>
                        <i class="menu-icon ki ki-bold-more-hor icon-md"></i>
                    </li>
                    <li class="menu-item" aria-haspopup="true">
                        <a href="{{ route('qa.risk-assessment.index') }}" class="menu-link">
                            <span class="svg-icon menu-icon">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24px"
                                    height="24px">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M9.07117914,12.5710461 L13.8326627,12.5710461 C14.108805,12.5710461 14.3326627,12.3471885 14.3326627,12.0710461 L14.3326627,0.16733734 C14.3326627,-0.108805035 14.108805,-0.33266266 13.8326627,-0.33266266 C13.6282104,-0.33266266 13.444356,-0.208187188 13.3684243,-0.0183579985 L8.6069408,11.8853508 C8.50438409,12.1417426 8.62909204,12.4327278 8.8854838,12.5352845 C8.94454394,12.5589085 9.00756943,12.5710461 9.07117914,12.5710461 Z"
                                            fill="#000000" opacity="0.3"
                                            transform="translate(11.451854, 6.119192) rotate(-270.000000) translate(-11.451854, -6.119192) " />
                                        <path
                                            d="M9.23851648,24.5 L14,24.5 C14.2761424,24.5 14.5,24.2761424 14.5,24 L14.5,12.0962912 C14.5,11.8201488 14.2761424,11.5962912 14,11.5962912 C13.7955477,11.5962912 13.6116933,11.7207667 13.5357617,11.9105959 L8.77427814,23.8143047 C8.67172143,24.0706964 8.79642938,24.3616816 9.05282114,24.4642383 C9.11188128,24.4878624 9.17490677,24.5 9.23851648,24.5 Z"
                                            fill="#000000"
                                            transform="translate(11.500000, 18.000000) scale(1, -1) rotate(-270.000000) translate(-11.500000, -18.000000) " />
                                        <rect fill="#000000" opacity="0.3"
                                            transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                            x="11" y="2" width="2" height="20" rx="1" />
                                    </g>
                                </svg>
                            </span>
                            <span class="menu-text">Risk Assessment</span>
                        </a>
                    </li>
                    <li class="menu-item" aria-haspopup="true">
                        <a href="{{ route('qa.audit.index') }}" class="menu-link">
                            <span class="svg-icon menu-icon">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24px"
                                    height="24px">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M6.5,16 L7.5,16 C8.32842712,16 9,16.6715729 9,17.5 L9,19.5 C9,20.3284271 8.32842712,21 7.5,21 L6.5,21 C5.67157288,21 5,20.3284271 5,19.5 L5,17.5 C5,16.6715729 5.67157288,16 6.5,16 Z M16.5,16 L17.5,16 C18.3284271,16 19,16.6715729 19,17.5 L19,19.5 C19,20.3284271 18.3284271,21 17.5,21 L16.5,21 C15.6715729,21 15,20.3284271 15,19.5 L15,17.5 C15,16.6715729 15.6715729,16 16.5,16 Z"
                                            fill="#000000" opacity="0.3" />
                                        <path
                                            d="M5,4 L19,4 C20.1045695,4 21,4.8954305 21,6 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,6 C3,4.8954305 3.8954305,4 5,4 Z M15.5,15 C17.4329966,15 19,13.4329966 19,11.5 C19,9.56700338 17.4329966,8 15.5,8 C13.5670034,8 12,9.56700338 12,11.5 C12,13.4329966 13.5670034,15 15.5,15 Z M15.5,13 C16.3284271,13 17,12.3284271 17,11.5 C17,10.6715729 16.3284271,10 15.5,10 C14.6715729,10 14,10.6715729 14,11.5 C14,12.3284271 14.6715729,13 15.5,13 Z M7,8 L7,8 C7.55228475,8 8,8.44771525 8,9 L8,11 C8,11.5522847 7.55228475,12 7,12 L7,12 C6.44771525,12 6,11.5522847 6,11 L6,9 C6,8.44771525 6.44771525,8 7,8 Z"
                                            fill="#000000" />
                                    </g>
                                </svg>
                            </span>

                            <span class="menu-text">Audit Vendor</span>
                        </a>
                    </li>
                @endhasanyrole
            </ul>
            <!--end::Menu Nav-->
        </div>
        <!--end::Menu Container-->
    </div>
    <!--end::Aside Menu-->
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ambil semua item menu
        const menuItems = document.querySelectorAll('.menu-item');
        // ambil url hal saat ini
        const currentUrl = window.location.href;

        menuItems.forEach(item => {

            const link = item.querySelector('a');

            if (link && currentUrl.includes(link.getAttribute('href'))) {
                // Hapus class 'menu-item-submenu' dari semua item menu
                menuItems.forEach(i => {
                    i.classList.remove('menu-item-active');
                });

                // Tambahkan class 'active' ke item yang sesuai
                item.classList.add('menu-item-active');

                // Tambahkan logika untuk menu induk jika item ini adalah submenu
                const parentSubMenu = item.closest('.menu-item-submenu');
                if (parentSubMenu) {
                    parentSubMenu.classList.add(
                        'menu-item-open');
                }
            }

            item.addEventListener('click', () => {
                menuItems.forEach(item => {
                    item.classList.remove('menu-item-active');
                });

                this.classList.add('menu-item-active');

                // Tambahkan logika untuk menu induk jika item ini adalah submenu
                const parentSubMenu = this.closest('.menu-item-submenu');
                if (parentSubMenu) {
                    parentSubMenu.classList.add('menu-item-open');
                }
            });
        });
    })
</script>
