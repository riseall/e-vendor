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
                <div class="mt-1">
                    @if (session('simulated_role'))
                        <span class="label label-inline font-weight-bold"
                            style="background-color: #fff8dd; color: #b58105; border: 1px solid #ffe79a; font-size: 11px;">
                            <i class="fas fa-flask font-size-xs mr-1" style="color: #b58105;"></i>{{ session('simulated_role') }} (Simulasi)
                        </span>
                    @else
                        <span class="text-dark-50 font-weight-bold">{{ session('spk_jabatan') ?: Auth::user()->roles->pluck('name')->implode(', ') }}</span>
                    @endif
                </div>
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

        <!--begin::Role Simulation Switcher-->
        <div class="card card-custom gutter-b border-0 p-4 rounded-xl" style="background-color: #fffbf0; border: 1px solid #ffe79a !important;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="font-size-xs font-weight-bolder text-uppercase" style="color: #b58105;">
                    <i class="fas fa-flask font-size-xs mr-1" style="color: #b58105;"></i> Level Akses (Simulasi)
                </span>
                @if (session('simulated_role'))
                    <a href="{{ route('simulation.reset') }}" class="btn btn-xs btn-outline-warning font-weight-bolder py-1 px-2" style="font-size: 10px;">
                        Reset
                    </a>
                @endif
            </div>
            <form action="{{ route('simulation.switch') }}" method="POST" class="d-flex align-items-center">
                @csrf
                <select name="level" class="form-control form-control-sm form-control-solid mr-2 font-size-xs cursor-pointer" onchange="this.form.submit()">
                    <option value="">-- Sesuai Akun (Default) --</option>
                    @foreach (\Spatie\Permission\Models\Role::orderBy('name')->pluck('name') as $roleItem)
                        <option value="{{ $roleItem }}" {{ session('simulated_role') === $roleItem ? 'selected' : '' }}>
                            {{ $roleItem }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-warning font-weight-bolder px-3 py-2">
                    Pilih
                </button>
            </form>
        </div>
        <!--end::Role Simulation Switcher-->
    </div>
    <!--end::Content-->
</div>
