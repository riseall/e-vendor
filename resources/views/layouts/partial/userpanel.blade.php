<div id="kt_quick_user" class="offcanvas offcanvas-right p-10">
    <!--begin::Header-->
    <div class="offcanvas-header d-flex align-items-center justify-content-between pb-5">
        <h3 class="font-weight-bold m-0">User Profile</h3>
        <a href="javascript:void(0);" class="btn btn-xs btn-icon btn-light btn-hover-danger" id="kt_quick_user_close">
            <i class="ki ki-close icon-xs text-muted"></i>
        </a>
    </div>
    <!--end::Header-->
    <!--begin::Content-->
    <div class="offcanvas-content pr-5 mr-n5">
        <!--begin::Header-->
        <div class="d-flex align-items-center mt-5">
            <div class="symbol symbol-100 mr-5">
                <span class="symbol symbol-75 symbol-success">
                    <span
                        class="symbol-label font-size-h1 font-weight-bold">{{ collect(explode(' ', Auth::user()->name))->take(2)->map(fn($word) => strtoupper($word[0]))->implode('') }}</span>
                </span>
                <i class="symbol-badge bg-success"></i>
            </div>
            <div class="d-flex flex-column">
                <a href="#"
                    class="font-weight-bold font-size-h5 text-dark-75 text-hover-primary">{{ Auth::user()->name }}</a>
                <div class="text-dark-50 font-weight-bold mt-1">{{ session('spk_jabatan') }}</div>
                <div class="navi mt-2">
                    <a href="#" class="navi-item">
                        <span class="navi-link p-0 pb-2">
                            <span class="navi-icon mr-1">
                                <span class="svg-icon svg-icon-lg svg-icon-primary">
                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Mail-notification.svg-->
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24" />
                                            <path
                                                d="M21,12.0829584 C20.6747915,12.0283988 20.3407122,12 20,12 C16.6862915,12 14,14.6862915 14,18 C14,18.3407122 14.0283988,18.6747915 14.0829584,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,8 C3,6.8954305 3.8954305,6 5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,12.0829584 Z M18.1444251,7.83964668 L12,11.1481833 L5.85557487,7.83964668 C5.4908718,7.6432681 5.03602525,7.77972206 4.83964668,8.14442513 C4.6432681,8.5091282 4.77972206,8.96397475 5.14442513,9.16035332 L11.6444251,12.6603533 C11.8664074,12.7798822 12.1335926,12.7798822 12.3555749,12.6603533 L18.8555749,9.16035332 C19.2202779,8.96397475 19.3567319,8.5091282 19.1603533,8.14442513 C18.9639747,7.77972206 18.5091282,7.6432681 18.1444251,7.83964668 Z"
                                                fill="#000000" />
                                            <circle fill="#000000" opacity="0.3" cx="19.5" cy="17.5"
                                                r="2.5" />
                                        </g>
                                    </svg>
                                    <!--end::Svg Icon-->
                                </span>
                            </span>
                            <span class="navi-text text-muted text-hover-primary text-wrap max-w-15">
                                {{ Auth::user()->email }}
                            </span>
                        </span>
                    </a>
                    <a href="{{ route('logout') }}" class="btn btn-sm btn-light-danger font-weight-bolder py-2 px-5"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign
                        Out</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
        <!--end::Header-->
        <div class="separator separator-dashed mt-8 mb-5"></div>
        <!--end::Separator-->
        <!--begin::Nav-->
        <div class="navi navi-spacer-x-0 p-0">
            <!--begin::Item-->
            <a href="custom/apps/user/profile-1/personal-information.html" class="navi-item">
                <div class="navi-link">
                    <div class="symbol symbol-40 bg-light mr-3">
                        <div class="symbol-label">
                            <span class="svg-icon svg-icon-md svg-icon-success">
                                <!--begin::Svg Icon | path:assets/media/svg/icons/General/Notification2.svg-->
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M13.2070325,4 C13.0721672,4.47683179 13,4.97998812 13,5.5 C13,8.53756612 15.4624339,11 18.5,11 C19.0200119,11 19.5231682,10.9278328 20,10.7929675 L20,17 C20,18.6568542 18.6568542,20 17,20 L7,20 C5.34314575,20 4,18.6568542 4,17 L4,7 C4,5.34314575 5.34314575,4 7,4 L13.2070325,4 Z"
                                            fill="#000000" />
                                        <circle fill="#000000" opacity="0.3" cx="18.5" cy="5.5" r="2.5" />
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                        </div>
                    </div>
                    <div class="navi-text">
                        <div class="font-weight-bold">My Profile</div>
                        <div class="text-muted">Account settings and more</div>
                    </div>
                </div>
            </a>
        </div>
        <!--begin::Separator-->
        <div class="separator separator-dashed my-7"></div>
        <!--end::Separator-->
        <!--begin::Notifications-->
        <div>
            <!--begin:Heading-->
            <h5 class="mb-5">Recent Notifications</h5>
            <!--end:Heading-->
            <div class="navi navi-hover scroll my-4" data-scroll="true" data-height="500" data-mobile-height="400">
                <!--begin::Item-->
                <a href="#" class="navi-item">
                    <div class="navi-link">
                        <div class="navi-icon mr-2">
                            <i class="flaticon2-line-chart text-success"></i>
                        </div>
                        <div class="navi-text">
                            <div class="font-weight-bold">New report has been received</div>
                            <div class="text-muted">23 hrs ago</div>
                        </div>
                    </div>
                </a>
                <!--end::Item-->
                <!--begin::Item-->
                <a href="#" class="navi-item">
                    <div class="navi-link">
                        <div class="navi-icon mr-2">
                            <i class="flaticon2-paper-plane text-danger"></i>
                        </div>
                        <div class="navi-text">
                            <div class="font-weight-bold">Finance report has been generated</div>
                            <div class="text-muted">25 hrs ago</div>
                        </div>
                    </div>
                </a>
                <!--end::Item-->
                <!--begin::Item-->
                <a href="#" class="navi-item">
                    <div class="navi-link">
                        <div class="navi-icon mr-2">
                            <i class="flaticon2-user flaticon2-line- text-success"></i>
                        </div>
                        <div class="navi-text">
                            <div class="font-weight-bold">New order has been received</div>
                            <div class="text-muted">2 hrs ago</div>
                        </div>
                    </div>
                </a>
                <!--end::Item-->
                <!--begin::Item-->
                <a href="#" class="navi-item">
                    <div class="navi-link">
                        <div class="navi-icon mr-2">
                            <i class="flaticon2-pin text-primary"></i>
                        </div>
                        <div class="navi-text">
                            <div class="font-weight-bold">New customer is registered</div>
                            <div class="text-muted">3 hrs ago</div>
                        </div>
                    </div>
                </a>
                <!--end::Item-->
                <!--begin::Item-->
                <a href="#" class="navi-item">
                    <div class="navi-link">
                        <div class="navi-icon mr-2">
                            <i class="flaticon2-sms text-danger"></i>
                        </div>
                        <div class="navi-text">
                            <div class="font-weight-bold">Application has been approved</div>
                            <div class="text-muted">3 hrs ago</div>
                        </div>
                    </div>
                </a>
                <!--end::Item-->
                <!--begin::Item-->
                <a href="#" class="navi-item">
                    <div class="navi-link">
                        <div class="navi-icon mr-2">
                            <i class="flaticon2-pie-chart-3 text-warning"></i>
                        </div>
                        <div class="navinavinavi-text">
                            <div class="font-weight-bold">New file has been uploaded</div>
                            <div class="text-muted">5 hrs ago</div>
                        </div>
                    </div>
                </a>
                <!--end::Item-->
                <!--begin::Item-->
                <a href="#" class="navi-item">
                    <div class="navi-link">
                        <div class="navi-icon mr-2">
                            <i class="flaticon-pie-chart-1 text-info"></i>
                        </div>
                        <div class="navi-text">
                            <div class="font-weight-bold">New user feedback received</div>
                            <div class="text-muted">8 hrs ago</div>
                        </div>
                    </div>
                </a>
                <!--end::Item-->
                <!--begin::Item-->
                <a href="#" class="navi-item">
                    <div class="navi-link">
                        <div class="navi-icon mr-2">
                            <i class="flaticon2-settings text-success"></i>
                        </div>
                        <div class="navi-text">
                            <div class="font-weight-bold">System reboot has been successfully completed</div>
                            <div class="text-muted">12 hrs ago</div>
                        </div>
                    </div>
                </a>
                <!--end::Item-->
                <!--begin::Item-->
                <a href="#" class="navi-item">
                    <div class="navi-link">
                        <div class="navi-icon mr-2">
                            <i class="flaticon-safe-shield-protection text-primary"></i>
                        </div>
                        <div class="navi-text">
                            <div class="font-weight-bold">New order has been placed</div>
                            <div class="text-muted">15 hrs ago</div>
                        </div>
                    </div>
                </a>
                <!--end::Item-->
                <!--begin::Item-->
                <a href="#" class="navi-item">
                    <div class="navi-link">
                        <div class="navi-icon mr-2">
                            <i class="flaticon2-notification text-primary"></i>
                        </div>
                        <div class="navi-text">
                            <div class="font-weight-bold">Company meeting canceled</div>
                            <div class="text-muted">19 hrs ago</div>
                        </div>
                    </div>
                </a>
                <!--end::Item-->
                <!--begin::Item-->
                <a href="#" class="navi-item">
                    <div class="navi-link">
                        <div class="navi-icon mr-2">
                            <i class="flaticon2-fax text-success"></i>
                        </div>
                        <div class="navi-text">
                            <div class="font-weight-bold">New report has been received</div>
                            <div class="text-muted">23 hrs ago</div>
                        </div>
                    </div>
                </a>
                <!--end::Item-->
                <!--begin::Item-->
                <a href="#" class="navi-item">
                    <div class="navi-link">
                        <div class="navi-icon mr-2">
                            <i class="flaticon-download-1 text-danger"></i>
                        </div>
                        <div class="navi-text">
                            <div class="font-weight-bold">Finance report has been generated</div>
                            <div class="text-muted">25 hrs ago</div>
                        </div>
                    </div>
                </a>
                <!--end::Item-->
                <!--begin::Item-->
                <a href="#" class="navi-item">
                    <div class="navi-link">
                        <div class="navi-icon mr-2">
                            <i class="flaticon-security text-warning"></i>
                        </div>
                        <div class="navi-text">
                            <div class="font-weight-bold">New customer comment recieved</div>
                            <div class="text-muted">2 days ago</div>
                        </div>
                    </div>
                </a>
                <!--end::Item-->
                <!--begin::Item-->
                <a href="#" class="navi-item">
                    <div class="navi-link">
                        <div class="navi-icon mr-2">
                            <i class="flaticon2-analytics-1 text-success"></i>
                        </div>
                        <div class="navi-text">
                            <div class="font-weight-bold">New customer is registered</div>
                            <div class="text-muted">3 days ago</div>
                        </div>
                    </div>
                </a>
                <!--end::Item-->
            </div>
        </div>
        <!--end::Notifications-->
    </div>
    <!--end::Content-->
</div>
