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
                @endphp
                @if ($activeAudit && $activeAudit->audit_type === 'on_desk')
                    <li class="menu-item" aria-haspopup="true">
                        <a href="{{ route('vendor.audit.questionnaire', $activeAudit->id) }}"
                            class="menu-link">
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
                                            d="M12.8434797,16 L11.1565203,16 L10.9852159,16.6393167 C10.3352654,19.064965 7.84199997,20.5044524 5.41635172,19.8545019 C2.99070348,19.2045514 1.55121603,16.711286 2.20116652,14.2856378 L3.92086709,7.86762789 C4.57081758,5.44197964 7.06408298,4.00249219 9.48973122,4.65244268 C10.5421727,4.93444352 11.4089671,5.56345262 12,6.38338695 C12.5910329,5.56345262 13.4578273,4.93444352 14.5102688,4.65244268 C16.935917,4.00249219 19.4291824,5.44197964 20.0791329,7.86762789 L21.7988335,14.2856378 C22.448784,16.711286 21.0092965,19.2045514 18.5836483,19.8545019 C16.158,20.5044524 13.6647346,19.064965 13.0147841,16.6393167 L12.8434797,16 Z M17.4563502,18.1051865 C18.9630797,18.1051865 20.1845253,16.8377967 20.1845253,15.2743923 C20.1845253,13.7109878 18.9630797,12.4435981 17.4563502,12.4435981 C15.9496207,12.4435981 14.7281751,13.7109878 14.7281751,15.2743923 C14.7281751,16.8377967 15.9496207,18.1051865 17.4563502,18.1051865 Z M6.54364977,18.1051865 C8.05037928,18.1051865 9.27182488,16.8377967 9.27182488,15.2743923 C9.27182488,13.7109878 8.05037928,12.4435981 6.54364977,12.4435981 C5.03692026,12.4435981 3.81547465,13.7109878 3.81547465,15.2743923 C3.81547465,16.8377967 5.03692026,18.1051865 6.54364977,18.1051865 Z"
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
