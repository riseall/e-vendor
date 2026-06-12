@extends('layouts.guest', ['title' => 'Reset Password'])

@section('content')
    <div class="login login-2 login-signin-on d-flex flex-row-fluid" id="kt_login">
        <div class="d-flex flex-center flex-row-fluid bgi-size-cover bgi-position-top bgi-no-repeat"
            style="background-image: url('{{ asset('images/bg-3.jpg') }}');">
            <div class="login-form text-center p-7 position-relative overflow-hidden">
                <div class="d-flex flex-center mb-15">
                    <a href="javascript:void(0);">
                        <img src="{{ asset('images/vendor.png') }}" class="max-h-150px" alt="" />
                    </a>
                </div>
                <div class="mb-20">
                    <h3>Reset Password</h3>
                    <div class="text-muted font-weight-bold">Enter a new password for your account</div>
                </div>
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div class="form-group mb-5">
                        <input id="email" type="email"
                            class="form-control h-auto form-control-solid py-4 px-8 @error('email') is-invalid @enderror"
                            name="email" value="{{ $request->email ?? old('email') }}" required autocomplete="email"
                            autofocus placeholder="Email" disabled>
                        @error('email')
                            <div class="invalid-feedback text-left">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-5">
                        <div class="input-group">
                            <input
                                class="form-control h-auto form-control-solid py-4 px-8 @error('password') is-invalid @enderror"
                                type="password" id="password" placeholder="Password" name="password"
                                style="border-right: none;" />
                            <div class="input-group-append rounded-right">
                                <span id="togglePassword" onclick="togglePassword('password', 'eyeIcon')"
                                    class="input-group-text cursor-pointer">
                                    <i class="far fa-eye" id="eyeIcon"></i>
                                </span>
                            </div>
                            @error('password')
                                <div class="invalid-feedback text-left">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <input class="form-control h-auto form-control-solid py-4 px-8" type="password"
                                id="password_confirmation" placeholder="Password Confirmation" name="password_confirmation"
                                style="border-right: none;" />
                            <div class="input-group-append rounded-right">
                                <span onclick="togglePassword('password_confirmation', 'eyeIconConfirm')"
                                    class="input-group-text cursor-pointer">
                                    <i class="far fa-eye" id="eyeIconConfirm"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.key') }}"></div>

                    @error('cf-turnstile-response')
                        <span class="text-danger" style="color: red; font-size: 0.875rem;">
                            {{ $message }}
                        </span>
                    @enderror

                    <button type="submit" class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-2">Reset
                        Password</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeIcon.className = "far fa-eye-slash";
            } else {
                passwordInput.type = "password";
                eyeIcon.className = "far fa-eye";
            }
        }
    </script>
@endpush
