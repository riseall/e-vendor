<?php

use App\Http\Controllers\Api\CronJobController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Remote Cronjob Webhook API
|--------------------------------------------------------------------------
| Digunakan untuk server cron terpisah/eksternal untuk mengeksekusi
| scheduler Laravel via HTTP POST yang diproteksi token CRON_SECRET_KEY.
*/
Route::prefix('cron')->name('api.cron.')->group(function () {
    Route::post('/run', [CronJobController::class, 'run'])->name('run');
    Route::post('/trigger/{command}', [CronJobController::class, 'trigger'])->name('trigger');
});
