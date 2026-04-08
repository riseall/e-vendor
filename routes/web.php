<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(
    [
        'prefix'     => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {

        require base_path('vendor/laravel/fortify/routes/routes.php');

        Route::get('/', function () {
            return view('welcome');
        })->name('welcome');

        Route::middleware('auth')->group(function () {
            // Dashboard
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            // Users
            Route::resource('user', UserController::class)->only('index', 'store', 'update', 'destroy');
            Route::get('/user/data', [UserController::class, 'getUser'])->name('user.data');
            Route::get('/user/search', [UserController::class, 'searchInternalUser'])->name('user.search');
        });
    }
);
