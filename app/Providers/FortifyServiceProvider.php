<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Fortify::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        //login
        Fortify::loginView(function () {
            return view('auth.login');
        });

        //reset
        Fortify::resetPasswordView(function ($request) {
            return view('auth.reset-password', ['request' => $request]);
        });


        Fortify::authenticateUsing(function (Request $request) {
            // $turnstileResponse = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            //     'secret' => config('services.turnstile.secret'),
            //     'response' => $request->input('cf-turnstile-response'),
            //     'remoteip' => $request->ip(),
            // ]);

            // if ($turnstileResponse->failed()) {
            //     throw ValidationException::withMessages([
            //         'username' => [__('auth.captcha_failed')],
            //     ]);
            // }

            $inputUsername = $request->username;
            $inputPassword = $request->password;

            $spkUser = DB::connection('db_master')
                ->table('mst_anggota')
                ->where(function ($query) use ($inputUsername) {
                    $query->where('nik', $inputUsername)
                          ->orWhere('email', $inputUsername);
                })
                ->first();

            if ($spkUser && Hash::check($inputPassword, $spkUser->password_hash)) {
                $localUser = User::firstOrCreate(
                    ['username' => $spkUser->nik],
                    [
                        'name' => $spkUser->nama,
                        'email' => $spkUser->email ?? $spkUser->nik . '@perusahaan.id',
                        'password' => Hash::make($inputPassword),
                        'is_active' => 1,
                    ]
                );

                $localUser->refresh();

                if ($localUser->is_active == 0) {
                    throw ValidationException::withMessages([
                        'username' => [__('auth.account_disabled')],
                    ]);
                }

                $kode_divisi = trim($spkUser->kode_divisi ?? '');
                $kode_departemen = trim($spkUser->kode_departemen ?? '');

                if ($localUser->roles()->count() === 0) {
                    if ($kode_divisi === 'DV00014') {
                        $localUser->assignRole('Procurement');
                    } elseif ($kode_departemen === 'DP00048') {
                        $localUser->assignRole('Quality Assurance');
                    } elseif ($kode_departemen === 'DP00009') {
                        $localUser->assignRole('Admin IT');
                    } else {
                        throw ValidationException::withMessages(['username' => [__('auth.no_role_access')]]);
                    }
                }

                session(['spk_jabatan' => $spkUser->ref_nama_jabatan]);
                session(['spk_departemen' => $spkUser-> ref_nama_departemen]);
                session(['spk_divisi' => $spkUser->ref_nama_divisi]);
                return $localUser;
            }

            // Login eksternal (Supplier)
            $externalUser = User::where('username', $inputUsername)
                ->orWhere('email', $inputUsername)
                ->first();

            if ($externalUser && Hash::check($inputPassword, $externalUser->password)) {

                if ($externalUser->is_active == 0) {
                    throw ValidationException::withMessages([
                        'username' => [__('auth.account_disabled')],
                    ]);
                }

                session()->forget(['spk_jabatan', 'spk_departemen', 'spk_divisi']);

                return $externalUser;
            }

            return false;
        });
    }
}
