<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\LoginLog;
use Illuminate\Http\Request;

class LogSuccessfulLogin
{
    protected $request;

    /**
     * Create the event listener.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;

        LoginLog::create([
            'nip' => $user->username ?? null,
            'nama' => $user->name ?? null,
            'email' => $user->email ?? null,
            'jabatan' => session('spk_jabatan'),
            'departemen' => session('spk_departemen'),
            'divisi' => session('spk_divisi'),
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
        ]);
    }
}
