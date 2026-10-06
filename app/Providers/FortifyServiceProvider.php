<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

        // Login view
        Fortify::loginView(function () {
            return view('auth.login');
        });

        // Reset password view
        Fortify::resetPasswordView(function ($request) {
            return view('auth.reset-password', ['request' => $request]);
        });

        // Custom authentication logic
        Fortify::authenticateUsing(function (Request $request) {
            // 1. Verifikasi Cloudflare Turnstile
            $turnstileResponse = Http::timeout(5)->asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret'   => config('services.turnstile.secret'),
                'response' => $request->input('cf-turnstile-response'),
                'remoteip' => $request->ip(),
            ]);

            if ($turnstileResponse->failed()) {
                throw ValidationException::withMessages([
                    'username' => [__('auth.captcha_failed')],
                ]);
            }

            $inputUsername = $request->username;
            $inputPassword = $request->password;

            // 2. Coba Login Internal (SPK) via SyncMaster API
            $spkUser = null;

            try {
                $apiBase = rtrim(config('services.syncmaster.api_url'), '/');
                $apiKey  = config('services.syncmaster.key');

                $res = Http::timeout(5)->withHeaders([
                    'X-API-KEY' => $apiKey,
                    'Accept'    => 'application/json',
                ])->post($apiBase . '/api/mst-anggota/verify', [
                    'username' => $inputUsername,
                    'password' => $inputPassword,
                ]);

                Log::info('SyncMaster Verify Response', [
                    'status' => $res->status(),
                    'body'   => $res->json() ?? $res->body(),
                ]);

                if ($res->successful()) {
                    $json = $res->json();
                    if (isset($json['data']) && is_array($json['data'])) {
                        $json = $json['data'];
                    }
                    if (!empty($json) && !isset($json['error'])) {
                        $spkUser = (object) $json;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('SyncMaster API unreachable: ' . $e->getMessage());
            }

            if ($spkUser) {
                $localUser = User::firstOrCreate(
                    ['username' => $spkUser->nik],
                    [
                        'name'      => $spkUser->nama,
                        'email'     => $spkUser->email ?? $spkUser->nik . '@perusahaan.id',
                        'password'  => Hash::make($inputPassword),
                        'is_active' => 1,
                    ]
                );

                // Selalu sinkronkan password lokal dengan password SPK yang valid
                $localUser->password = Hash::make($inputPassword);
                $localUser->save();

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

                // ponytail: Simpan role simulasi ke session jika dipilih untuk testing (seperti pada aplikasi logsheet)
                $simulatedRole = $request->input('level');
                if ($simulatedRole) {
                    session(['simulated_role' => $simulatedRole]);
                } else {
                    session()->forget('simulated_role');
                }

                session([
                    'spk_jabatan'    => $spkUser->ref_nama_jabatan ?? null,
                    'spk_departemen' => $spkUser->ref_nama_departemen ?? null,
                    'spk_divisi'     => $spkUser->ref_nama_divisi ?? null,
                ]);

                return $localUser;
            }

            // 3. Login Eksternal (Khusus Supplier / Vendor)
            $externalUser = User::where('username', $inputUsername)
                ->orWhere('email', $inputUsername)
                ->first();

            if ($externalUser) {
                // Akun internal WAJIB login via API SyncMaster, tolak jika lewat jalur password lokal
                $internalRoles = ['Super Admin', 'Admin IT', 'Verifikator', 'Procurement', 'Quality Assurance', 'Apoteker', 'Specialist'];
                if ($externalUser->hasAnyRole($internalRoles)) {
                    return false;
                }

                if (Hash::check($inputPassword, $externalUser->password)) {
                    if ($externalUser->is_active == 0) {
                        throw ValidationException::withMessages([
                            'username' => [__('auth.account_disabled')],
                        ]);
                    }

                    // ponytail: Simpan role simulasi ke session jika dipilih untuk testing (seperti pada aplikasi logsheet)
                    $simulatedRole = $request->input('level');
                    if ($simulatedRole) {
                        session(['simulated_role' => $simulatedRole]);
                    } else {
                        session()->forget('simulated_role');
                    }

                    session()->forget(['spk_jabatan', 'spk_departemen', 'spk_divisi']);

                    return $externalUser;
                }
            }

            return false;
        });
    }
}
