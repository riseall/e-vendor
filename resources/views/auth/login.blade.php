@extends('layouts.guest', ['title' => 'Login'])

@section('content')
    <div class="login login-2 login-signin-on d-flex flex-row-fluid" id="kt_login">
        <div class="d-flex flex-center flex-row-fluid bgi-size-cover bgi-position-top bgi-no-repeat"
            style="background-image: url('{{ asset('images/bg-3.jpg') }}');">
            <div class="login-form text-center p-7 position-relative overflow-hidden">
                <div class="d-flex flex-center mb-15">
                    <a href="#">
                        <img src="{{ asset('images/vendor.png') }}" class="max-h-150px" alt="" />
                    </a>
                </div>
                <div class="login-signin">
                    <div class="mb-20">
                        <h3>Sign In</h3>
                        <div class="text-muted font-weight-bold">Enter your details to login to your account:</div>
                    </div>
                    <form class="form" id="kt_login_signin_form">
                        @csrf
                        <div class="form-group mb-5">
                            <input
                                class="form-control h-auto form-control-solid py-4 px-8 @error('username') is-invalid @enderror"
                                type="text" placeholder="Username" name="username" autocomplete="off"
                                value="{{ old('username') }}" />
                            @error('username')
                                <div class="invalid-feedback text-left">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-5">
                            <div class="input-group">
                                <input
                                    class="form-control h-auto form-control-solid py-4 px-8 @error('password') is-invalid @enderror"
                                    type="password" id="password" placeholder="Password" name="password" />
                                <div class="input-group-append rounded-right">
                                    <span id="togglePassword" onclick="togglePassword()"
                                        class="input-group-text cursor-pointer border-0">
                                        <i class="far fa-eye" id="eyeIcon"></i>
                                    </span>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback text-left">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group d-flex flex-wrap justify-content-between align-items-center">
                            <label class="checkbox m-0 text-muted">
                                <input type="checkbox" name="remember" />Remember me
                                <span></span>
                            </label>
                            <a href="javascript:;" id="kt_login_forgot" class="text-muted text-hover-primary">Forget
                                Password ?</a>
                        </div>
                        <button id="kt_login_signin_submit"
                            class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-4">Sign In</button>
                    </form>
                    <div class="mt-10">
                        <span class="opacity-70 mr-4">Don't have an account yet?</span>
                        <a href="javascript:;" id="kt_login_signup"
                            class="text-muted text-hover-primary font-weight-bold">Sign Up!</a>
                    </div>
                </div>
                <div class="login-signup">
                    <div class="mb-20">
                        <h3>Sign Up</h3>
                        <div class="text-muted font-weight-bold">Enter your details to create your account</div>
                    </div>
                    <form class="form" id="kt_login_signup_form">
                        @csrf

                        <div class="form-group mb-5">
                            <input
                                class="form-control h-auto form-control-solid py-4 px-8 @error('name') is-invalid @enderror"
                                type="text" placeholder="Fullname" name="name" value="{{ old('name') }}" />
                            @error('name')
                                <div class="invalid-feedback text-left">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group row align-items-center mb-5">
                            <div class="col-md-6">
                                <input
                                    class="form-control h-auto form-control-solid py-4 px-8 @error('username') is-invalid @enderror"
                                    type="text" placeholder="Username" name="username" value="{{ old('username') }}" />
                                @error('username')
                                    <div class="invalid-feedback text-left">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mt-5 mt-md-0">
                                <input
                                    class="form-control h-auto form-control-solid py-4 px-8 @error('email') is-invalid @enderror"
                                    type="text" placeholder="Email" name="email" autocomplete="off"
                                    value="{{ old('email') }}" />
                                @error('email')
                                    <div class="invalid-feedback text-left">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group mb-5">
                            <input
                                class="form-control h-auto form-control-solid py-4 px-8 @error('password') is-invalid @enderror"
                                type="password" placeholder="Password" name="password" />
                            @error('password')
                                <div class="invalid-feedback text-left">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-5">
                            <input class="form-control h-auto form-control-solid py-4 px-8" type="password"
                                placeholder="Password Confirmation" name="password_confirmation" />
                        </div>
                        <div class="form-group mb-5 text-left">
                            <label class="checkbox m-0 @error('agree') text-danger @enderror">
                                <input type="checkbox" name="agree" />I Agree the
                                <a href="#" class="font-weight-bold ml-1">terms and conditions</a>.
                                <span></span>
                            </label>
                            @error('agree')
                                <div class="text-danger mt-1" style="font-size: 0.9rem;">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted text-center"></div>
                        </div>
                        <div class="form-group d-flex flex-wrap flex-center mt-10">
                            <button id="kt_login_signup_submit"
                                class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-2">Sign Up</button>
                            <button id="kt_login_signup_cancel"
                                class="btn btn-light-danger font-weight-bold px-9 py-4 my-3 mx-2">Cancel</button>
                        </div>
                    </form>
                </div>
                <div class="login-forgot">
                    <div class="mb-20">
                        <h3>Forgotten Password ?</h3>
                        <div class="text-muted font-weight-bold">Enter your email to reset your password</div>
                    </div>
                    <form class="form" id="kt_login_forgot_form">
                        @csrf
                        <div class="form-group mb-10">
                            <input
                                class="form-control form-control-solid h-auto py-4 px-8 @error('email') is-invalid @enderror"
                                type="text" placeholder="Email" name="email" autocomplete="off"
                                value="{{ old('email') }}" />
                            @error('email')
                                <div class="invalid-feedback text-left">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group d-flex flex-wrap flex-center mt-10">
                            <button id="kt_login_forgot_submit"
                                class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-2">Request</button>
                            <button id="kt_login_forgot_cancel"
                                class="btn btn-light-danger font-weight-bold px-9 py-4 my-3 mx-2">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.auth = {
            login: "{{ route('login') }}",
            register: "{{ route('register') }}",
            forgot: "{{ route('password.email') }}",
            dashboard: "{{ route('dashboard') }}"
        }
    </script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.className = "far fa-eye-slash";
            } else {
                passwordInput.type = 'password';
                eyeIcon.className = "far fa-eye";
            }
        }
    </script>
@endpush
