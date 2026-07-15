<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProcurementVerificationController;
use App\Http\Controllers\RegistrasiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorAuditController;
use App\Http\Controllers\VendorCapaController;
use App\Http\Controllers\VendorQualificationController;
use App\Http\Controllers\VendorQuestionnaireController;
use App\Http\Controllers\VendorUploadController;
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

        // Tutorial
        Route::get('/tutorial', function () {
            return view('user.tutorial');
        })->name('tutorial');

        // Contact
        Route::get('/contact', function () {
            return view('user.contact');
        })->name('contact');

        Route::middleware('auth')->group(function () {
            // Dashboard
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            // Registrasi
            Route::prefix('registration')->name('registrasi.')->group(function () {

                // Halaman form wizard
                Route::get('/', [RegistrasiController::class, 'index'])
                    ->name('index');

                // Simpan draft (AJAX - dipanggil tiap klik "Simpan Draft")
                Route::post('/save-draft', [RegistrasiController::class, 'saveDraft'])
                    ->name('save-draft');

                // Simpan form umum (step 2 - dengan dynamic validation)
                Route::post('/save-umum', [RegistrasiController::class, 'saveUmum'])
                    ->name('save-umum');

                // Simpan form spesifik (step 3 - dengan dynamic validation)
                Route::post('/save-specific', [RegistrasiController::class, 'saveSpecificStep'])
                    ->name('save-specific');

                // Submit permohonan final (step 5)
                Route::post('/submit', [RegistrasiController::class, 'submit'])
                    ->name('submit');

                Route::get('/success/{application}', [RegistrasiController::class, 'success'])
                    ->name('success');

                Route::get('/tracking/{applicationNumber?}', [RegistrasiController::class, 'tracking'])
                    ->name('tracking');

                Route::get('/files/{application}', [RegistrasiController::class, 'showFile'])
                    ->name('files.show');

                //ajax upload document
                Route::post('/upload-temp', [VendorUploadController::class, 'uploadTemp'])->name('upload.temp');
            });


            // Product Search
            Route::get('/search-products', [RegistrasiController::class, 'searchProducts'])->name('search-products');

            Route::prefix('verification')->name('verifikasi.')->group(function () {
                Route::get('/', [ProcurementVerificationController::class, 'index'])
                    ->name('index');
                Route::get('/{application}', [ProcurementVerificationController::class, 'show'])
                    ->name('show');
                Route::post('/{application}/verify', [ProcurementVerificationController::class, 'verify'])
                    ->name('verify');
                Route::post('/{application}/revisi', [ProcurementVerificationController::class, 'requestRevision'])
                    ->name('revisi');
                Route::post('/{application}/items/{item}/approve', [ProcurementVerificationController::class, 'approveItem'])
                    ->name('items.approve');
                Route::post('/{application}/items/{item}/reject', [ProcurementVerificationController::class, 'rejectItem'])
                    ->name('items.reject');
            });

            // QA - Risk Assessment
            Route::prefix('qa/risk-assessment')->name('qa.risk-assessment.')->group(function () {
                Route::get('/', [VendorQualificationController::class, 'index'])
                    ->name('index');

                Route::get('/{application_id}/create', [VendorQualificationController::class, 'create'])
                    ->name('create');

                Route::post('/{application_id}', [VendorQualificationController::class, 'store'])
                    ->name('store');
            });

            // QA - Audit (Stage 5)
            Route::prefix('qa/audit')->name('qa.audit.')->group(function () {
                Route::get('/', [VendorAuditController::class, 'index'])->name('index');
                Route::get('/{applicationId}/create', [VendorAuditController::class, 'create'])->name('create');
                Route::post('/{applicationId}/store', [VendorAuditController::class, 'store'])->name('store');
                Route::get('/{auditId}', [VendorAuditController::class, 'show'])->name('show');

                // Verifikasi questionnaire (on desk)
                Route::post('/{auditId}/questionnaire/verify', [VendorAuditController::class, 'verifyQuestionnaire'])
                    ->name('questionnaire.verify');

                // Konfirmasi jadwal (on site)
                Route::post('/{auditId}/schedule/confirm', [VendorAuditController::class, 'confirmSchedule'])
                    ->name('schedule.confirm');

                // Surat audit
                Route::post('/{auditId}/letter', [VendorAuditController::class, 'generateLetter'])
                    ->name('letter.generate');
                Route::get('/{auditId}/letter/download', [VendorAuditController::class, 'downloadLetter'])
                    ->name('letter.download');

                // Temuan audit
                Route::post('/{auditId}/findings', [VendorAuditController::class, 'storeFindings'])
                    ->name('findings.store');

                // Verifikasi CAPA per item
                Route::post('/capa/{capaId}/verify', [VendorAuditController::class, 'verifyCapa'])
                    ->name('capa.verify');

                // Final decision
                Route::post('/{auditId}/finalize', [VendorAuditController::class, 'finalize'])
                    ->name('finalize');
            });

            // Vendor - Audit (Stage 5)
            Route::prefix('vendor/audit')->name('vendor.audit.')->group(function () {
                // Questionnaire (on desk)
                Route::get('/{auditId}/questionnaire', [VendorQuestionnaireController::class, 'show'])
                    ->name('questionnaire');
                Route::post('/{auditId}/questionnaire/save', [VendorQuestionnaireController::class, 'saveDraft'])
                    ->name('questionnaire.save');
                Route::post('/{auditId}/questionnaire/submit', [VendorQuestionnaireController::class, 'submit'])
                    ->name('questionnaire.submit');

                // CAPA (on site)
                Route::get('/capa', [VendorCapaController::class, 'index'])->name('capa.index');
                Route::get('/{auditId}/capa', [VendorCapaController::class, 'show'])->name('capa');
                Route::post('/{auditId}/capa/save', [VendorCapaController::class, 'save'])->name('capa.save');
            });

            // Users
            Route::resource('user', UserController::class)->only('index', 'store', 'update', 'destroy');
            Route::get('/user/data', [UserController::class, 'getUser'])->name('user.data');
            Route::get('/user/search', [UserController::class, 'searchInternalUser'])->name('user.search');
        });
    }
);
