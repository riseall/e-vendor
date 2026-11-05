<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CronJobController extends Controller
{
    /**
     * Daftar artisan commands yang diizinkan untuk dieksekusi secara remote.
     */
    private const ALLOWED_COMMANDS = [
        'schedule:run',
        'vendor-applications:auto-verify',
        'vendor-applications:remind-verification',
        'vendor-applications:clean-drafts',
        'vendor-applications:trigger-rekualifikasi',
        'vendor-applications:check-cdob-expiry',
        'vendor-applications:remind-rekualifikasi',
        'evaluasi:pull-qad-data',
        'evaluasi:check-drop-score',
        'evaluasi:generate-annual',
    ];

    /**
     * Validasi secret key dari request (Authorization Bearer atau Header X-Cron-Key).
     */
    private function validateSecret(Request $request): bool
    {
        $expectedKey = env('CRON_SECRET_KEY', config('services.cron.key'));

        // Jika belum diset di .env, gunakan fallback hash aplikasi yang stabil namun aman
        if (empty($expectedKey)) {
            $expectedKey = hash('sha256', config('app.key', 'phapros-e-vendor-cron-secret'));
        }

        $providedKey = $request->bearerToken();
        if (empty($providedKey)) {
            $providedKey = $request->header('X-Cron-Key');
        }

        if (empty($providedKey) && $request->has('cron_key')) {
            $providedKey = $request->query('cron_key');
        }

        return !empty($providedKey) && hash_equals($expectedKey, (string) $providedKey);
    }

    /**
     * Endpoint utama untuk cronjob eksternal: Mengeksekusi schedule:run setiap menit.
     *
     * Contoh penggunaan di crontab server lain:
     * * * * * * curl -s -X POST https://e-vendor.phapros.co.id/api/cron/run -H "Authorization: Bearer <CRON_SECRET_KEY>" > /dev/null 2>&1
     */
    public function run(Request $request): JsonResponse
    {
        if (!$this->validateSecret($request)) {
            Log::warning('Percobaan akses unauthorized ke remote cron endpoint', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Secret key tidak valid atau tidak disertakan.',
            ], 401);
        }

        try {
            $startTime = microtime(true);
            Artisan::call('schedule:run');
            $output = Artisan::output();
            $duration = round(microtime(true) - $startTime, 4);

            Log::info('Remote cron schedule:run berhasil dieksekusi via webhook API', [
                'ip' => $request->ip(),
                'duration_sec' => $duration,
            ]);

            return response()->json([
                'success'      => true,
                'message'      => 'Laravel schedule:run berhasil dieksekusi.',
                'output'       => trim($output) ?: 'No scheduled commands were due to run.',
                'duration_sec' => $duration,
                'timestamp'    => now()->toIso8601String(),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error saat mengeksekusi remote cron schedule:run: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengeksekusi scheduler: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Endpoint opsional untuk memicu perintah spesifik secara langsung jika diperlukan.
     *
     * Contoh: POST /api/cron/trigger/vendor-applications:auto-verify
     */
    public function trigger(Request $request, string $command): JsonResponse
    {
        if (!$this->validateSecret($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Secret key tidak valid atau tidak disertakan.',
            ], 401);
        }

        if (!in_array($command, self::ALLOWED_COMMANDS, true)) {
            return response()->json([
                'success' => false,
                'message' => "Command '{$command}' tidak diizinkan atau tidak terdaftar dalam whitelist remote cron.",
            ], 400);
        }

        try {
            $startTime = microtime(true);
            Artisan::call($command);
            $output = Artisan::output();
            $duration = round(microtime(true) - $startTime, 4);

            return response()->json([
                'success'      => true,
                'command'      => $command,
                'output'       => trim($output),
                'duration_sec' => $duration,
                'timestamp'    => now()->toIso8601String(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'command' => $command,
                'message' => 'Gagal mengeksekusi command: ' . $e->getMessage(),
            ], 500);
        }
    }
}

