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
            $turnstileResponse = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => config('services.turnstile.secret'),
                'response' => $request->input('cf-turnstile-response'),
                'remoteip' => $request->ip(),
            ]);

            if ($turnstileResponse->failed()) {
                return false;
            }

            $inputUsername = $request->username;
            $inputPassword = $request->password;

            $spkUser = DB::connection('db_master')
                ->table('mst_anggota')
                ->where('nik', $inputUsername)
                ->first();

            if ($spkUser && Hash::check($inputPassword, $spkUser->password_hash)) {

                $localUser = User::firstOrCreate(
                    ['username' => $spkUser->nik],
                    ['name' => $spkUser->nama, 'email' => $spkUser->email]
                );

                $kode_direktorat = $spkUser->kode_direktorat ?? '';

                if ($kode_direktorat === 'DR00003' && $localUser->roles()->count() === 0) {
                    // $localUser->assignRole('mkt');
                    return $localUser;
                } elseif ($localUser->roles()->count() === 0) {
                    return false;
                }

                session([
                    'spk_jabatan' => $spkUser->ref_nama_jabatan,
                ]);

                return $localUser;
            }

            $externalUser = User::where('username', $inputUsername)
                ->orWhere('email', $inputUsername)
                ->first();

            if ($externalUser && Hash::check($inputPassword, $externalUser->password)) {
                session()->forget(['spk_jabatan']);

                return $externalUser;
            }

            return false;
        });
    }
}
