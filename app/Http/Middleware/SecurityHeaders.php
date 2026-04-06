<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();
        array_walk_recursive($input, function (&$value) {
            if (is_string($value)) {
                $value = strip_tags($value);
            }
        });
        $request->merge($input);

        $response = $next($request);

        // Mencegah Clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        // MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        // Mencegah XSS
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        // HSTS
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        // Referrer-Policy
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
        // Permissions Policy
        $response->headers->set('Permissions-Policy', "geolocation=(), microphone=(), camera=()");
        // CSP
        $csp = "
                default-src 'self';

                script-src
                'self'
                'unsafe-inline'
                'unsafe-eval'
                https://challenges.cloudflare.com
                https://*.cloudflare.com
                https://maps.googleapis.com
                https://maps.gstatic.com;

                frame-src
                'self'
                https://challenges.cloudflare.com
                https://*.cloudflare.com
                https://www.google.com
                https://maps.google.com
                https://www.google.com/maps
                https://www.google.com/maps/embed;

                connect-src
                'self'
                https://challenges.cloudflare.com
                https://*.cloudflare.com
                https://maps.googleapis.com
                https://maps.gstatic.com;

                img-src
                'self'
                data:
                blob:
                https://*.cloudflare.com;

                style-src
                'self'
                'unsafe-inline'
                https://fonts.googleapis.com;

                font-src
                'self'
                https://fonts.gstatic.com;
                ";


        $csp = preg_replace('/\s+/', ' ', trim($csp));
        $response->headers->set('Content-Security-Policy', $csp);


        return $response;
    }
}
