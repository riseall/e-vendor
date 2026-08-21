<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProcurementVerificationController;
use App\Http\Controllers\RegistrasiController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorAuditController;
use App\Http\Controllers\VendorCapaController;
use App\Http\Controllers\VendorQualificationController;
use App\Http\Controllers\VendorQuestionnaireController;
use App\Http\Controllers\VendorRekualifikasiController;
use App\Http\Controllers\VendorSupplierController;
use App\Http\Controllers\VendorUploadController;
use App\Http\Controllers\QuestionnaireFormController;
use App\Http\Controllers\Admin\EvaluasiVendorController;
use App\Http\Controllers\Vendor\VendorEvaluasiDashboardController;
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

            Route::prefix('verification')->name('verifikasi.')->middleware('can:verifikasi-list')->group(function () {
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

            // Supplier Terekomendasi
            Route::get('/supplier', [VendorSupplierController::class, 'index'])
                ->name('supplier.index');
            Route::post('/supplier/{id}/update-qad', [VendorSupplierController::class, 'updateQadCode'])
                ->name('supplier.update-qad');

            // QA - Risk Assessment
            Route::prefix('qa/risk-assessment')->name('qa.risk-assessment.')->middleware('can:qa-risk-list')->group(function () {
                Route::get('/', [VendorQualificationController::class, 'index'])
                    ->name('index');

                Route::get('/{application_id}/create', [VendorQualificationController::class, 'create'])
                    ->name('create');

                Route::post('/{application_id}', [VendorQualificationController::class, 'store'])
                    ->name('store');
            });

            // QA - Audit (Stage 5)
            Route::prefix('qa/audit')->name('qa.audit.')->middleware('can:audit-list')->group(function () {
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
                Route::post('/{auditId}/result', [VendorAuditController::class, 'storeResult'])->name('store-result');
            });

            // Vendor - Audit (Stage 5)
            Route::prefix('vendor/audit')->name('vendor.audit.')->group(function () {
                Route::get('/results', [VendorQuestionnaireController::class, 'indexResults'])
                    ->name('results');
                // Questionnaire (on desk)
                Route::get('/{auditId}/questionnaire', [VendorQuestionnaireController::class, 'show'])
                    ->name('questionnaire');
                Route::post('/{auditId}/questionnaire/save', [VendorQuestionnaireController::class, 'saveDraft'])
                    ->name('questionnaire.save');
                Route::post('/{auditId}/questionnaire/submit', [VendorQuestionnaireController::class, 'submit'])
                    ->name('questionnaire.submit');
            });

            // Rekualifikasi Vendor & Admin Trigger
            Route::prefix('rekualifikasi')->name('rekualifikasi.')->middleware('can:rekualifikasi-list')->group(function () {
                Route::get('/', [VendorRekualifikasiController::class, 'index'])->name('index');
                Route::post('/initiate', [VendorRekualifikasiController::class, 'initiate'])->name('initiate');
                Route::post('/{applicationId}/trigger', [VendorRekualifikasiController::class, 'triggerByAdmin'])->name('trigger');
            });

            // Users
            Route::middleware('can:user-list')->group(function () {
                Route::resource('user', UserController::class)->only('index', 'store', 'update', 'destroy');
                Route::get('/user/data', [UserController::class, 'getUser'])->name('user.data');
                Route::get('/user/search', [UserController::class, 'searchInternalUser'])->name('user.search');
            });

            // Master Role & Permission
            Route::prefix('role')->name('role.')->middleware('can:role-list')->group(function () {
                Route::get('/', [RoleController::class, 'index'])->name('index');
                Route::post('/', [RoleController::class, 'store'])->name('store');
                Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
                Route::get('/{role}/permissions', [RoleController::class, 'getPermissions'])->name('permissions.get');
                Route::post('/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('permissions.update');
            });

            // Master Questionnaire
            Route::resource('questionnaire-form', QuestionnaireFormController::class);
            Route::get('/questionnaire-form/{form}/questions', [QuestionnaireFormController::class, 'questions'])->name('questionnaire-form.questions');
            Route::post('/questionnaire-form/{form}/questions', [QuestionnaireFormController::class, 'storeQuestion'])->name('questionnaire-form.questions.store');
            Route::post('/questionnaire-form/{form}/questions/import', [QuestionnaireFormController::class, 'importQuestions'])->name('questionnaire-form.questions.import');
            Route::put('/questionnaire-form/{form}/questions/{question}', [QuestionnaireFormController::class, 'updateQuestion'])->name('questionnaire-form.questions.update');
            Route::delete('/questionnaire-form/{form}/questions/{question}', [QuestionnaireFormController::class, 'destroyQuestion'])->name('questionnaire-form.questions.destroy');

            // Evaluasi Vendor (Admin)
            Route::prefix('admin/evaluasi-vendor')->name('admin.evaluasi.')->middleware('can:evaluasi-list')->group(function () {
                Route::get('/', [EvaluasiVendorController::class, 'index'])->name('index');
                Route::get('/fetch-qad', [EvaluasiVendorController::class, 'fetchQadData'])->name('fetch-qad');
                Route::post('/monthly', [EvaluasiVendorController::class, 'storeMonthly'])->name('store-monthly');
                Route::post('/batch-monthly', [EvaluasiVendorController::class, 'storeBatchMonthly'])->name('store-batch-monthly');
                Route::post('/annual/generate', [EvaluasiVendorController::class, 'generateAnnual'])->name('annual.generate');
                Route::post('/annual/{id}/approve', [EvaluasiVendorController::class, 'approveAnnual'])->name('annual.approve');
                Route::post('/annual/{id}/trigger-action', [EvaluasiVendorController::class, 'triggerAction'])->name('annual.trigger-action');
                Route::get('/settings', [EvaluasiVendorController::class, 'settings'])->name('settings')->middleware('can:evaluasi-settings');
                Route::post('/settings', [EvaluasiVendorController::class, 'updateSettings'])->name('settings.update')->middleware('can:evaluasi-settings');
            });

            // Evaluasi Vendor (Vendor Dashboard)
            Route::get('/vendor/evaluasi-kinerja', [VendorEvaluasiDashboardController::class, 'index'])->name('vendor.evaluasi.index');
        });
    }
);
