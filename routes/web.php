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
use App\Http\Controllers\Admin\EvaluasiImportController;
use App\Http\Controllers\Admin\EvaluasiAnnualController;
use App\Http\Controllers\Admin\EvaluasiSettingController;
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

        Route::get('/home', function () {
            return redirect()->route(auth()->check() ? 'dashboard' : 'welcome');
        })->name('home');

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
            Route::get('/vendor/dashboard', [DashboardController::class, 'vendorIndex'])->name('vendor.dashboard');

            // Role Simulation Switcher (Testing)
            Route::post('/simulation/switch', function (\Illuminate\Http\Request $request) {
                $role = $request->input('level');
                if ($role) {
                    session(['simulated_role' => $role]);
                } else {
                    session()->forget('simulated_role');
                }
                return redirect()->route('dashboard');
            })->name('simulation.switch');

            Route::get('/simulation/reset', function () {
                session()->forget('simulated_role');
                return redirect()->route('dashboard');
            })->name('simulation.reset');

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
                    ->name('index')
                    ->middleware('can:verifikasi-list');
                Route::get('/{application}', [ProcurementVerificationController::class, 'show'])
                    ->name('show')
                    ->middleware('can:verifikasi-detail');
                Route::post('/{application}/verify', [ProcurementVerificationController::class, 'verify'])
                    ->name('verify')
                    ->middleware('can:verifikasi-approve');
                Route::post('/{application}/revisi', [ProcurementVerificationController::class, 'requestRevision'])
                    ->name('revisi')
                    ->middleware('can:verifikasi-reject');
                Route::post('/{application}/items/{item}/approve', [ProcurementVerificationController::class, 'approveItem'])
                    ->name('items.approve')
                    ->middleware('can:verifikasi-approve');
                Route::post('/{application}/items/{item}/reject', [ProcurementVerificationController::class, 'rejectItem'])
                    ->name('items.reject')
                    ->middleware('can:verifikasi-reject');
            });

            // Supplier Terekomendasi
            Route::get('/supplier', [VendorSupplierController::class, 'index'])
                ->name('supplier.index')
                ->middleware('can:supplier-list');
            Route::get('/supplier/{id}', [VendorSupplierController::class, 'show'])
                ->name('supplier.show')
                ->middleware('can:supplier-detail');
            Route::post('/supplier/{id}/update-qad', [VendorSupplierController::class, 'updateQadCode'])
                ->name('supplier.update-qad')
                ->middleware('can:supplier-edit-qad');

            // QA - Risk Assessment
            Route::prefix('qa/risk-assessment')->name('qa.risk-assessment.')->middleware('can:qa-risk-list')->group(function () {
                Route::get('/', [VendorQualificationController::class, 'index'])
                    ->name('index');

                Route::get('/{application_id}/create', [VendorQualificationController::class, 'create'])
                    ->name('create')
                    ->middleware('can:qa-risk-create');

                Route::post('/{application_id}', [VendorQualificationController::class, 'store'])
                    ->name('store')
                    ->middleware('can:qa-risk-store');

                // Vendor Eksisting (BB/BK - Rekualifikasi)
                Route::get('/{application_id}/create-existing', [VendorQualificationController::class, 'createExisting'])
                    ->name('create-existing')
                    ->middleware('can:qa-risk-create');

                Route::post('/{application_id}/existing', [VendorQualificationController::class, 'storeExisting'])
                    ->name('store-existing')
                    ->middleware('can:qa-risk-store');
            });

            // QA - Audit (Stage 5)
            Route::prefix('qa/audit')->name('qa.audit.')->middleware('can:audit-list')->group(function () {
                Route::get('/', [VendorAuditController::class, 'index'])->name('index');
                Route::get('/{applicationId}/create', [VendorAuditController::class, 'create'])->name('create')->middleware('can:audit-create');
                Route::post('/{applicationId}/store', [VendorAuditController::class, 'store'])->name('store')->middleware('can:audit-create');
                Route::get('/{auditId}', [VendorAuditController::class, 'show'])->name('show');

                // Verifikasi questionnaire (on desk)
                Route::post('/{auditId}/questionnaire/verify', [VendorAuditController::class, 'verifyQuestionnaire'])
                    ->name('questionnaire.verify')
                    ->middleware('can:audit-verify');

                // Konfirmasi jadwal (on site)
                Route::post('/{auditId}/schedule/confirm', [VendorAuditController::class, 'confirmSchedule'])
                    ->name('schedule.confirm')
                    ->middleware('can:audit-create');

                // Temuan audit
                Route::post('/{auditId}/result', [VendorAuditController::class, 'storeResult'])
                    ->name('store-result')
                    ->middleware('can:audit-result');
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
            Route::prefix('rekualifikasi')->name('rekualifikasi.')->group(function () {
                Route::get('/', [VendorRekualifikasiController::class, 'index'])
                    ->name('index')
                    ->middleware('can:rekualifikasi-list');
                Route::post('/initiate', [VendorRekualifikasiController::class, 'initiate'])
                    ->name('initiate')
                    ->middleware('can:rekualifikasi-initiate');
                Route::post('/{applicationId}/trigger', [VendorRekualifikasiController::class, 'triggerByAdmin'])
                    ->name('trigger')
                    ->middleware('can:rekualifikasi-initiate');
            });

            // Users
            Route::middleware('can:user-list')->group(function () {
                Route::get('/user', [UserController::class, 'index'])->name('user.index');
                Route::post('/user', [UserController::class, 'store'])->name('user.store')->middleware('can:user-create');
                Route::put('/user/{user}', [UserController::class, 'update'])->name('user.update')->middleware('can:user-edit');
                Route::delete('/user/{user}', [UserController::class, 'destroy'])->name('user.destroy')->middleware('can:user-delete');
                Route::get('/user/data', [UserController::class, 'getUser'])->name('user.data');
                Route::get('/user/search', [UserController::class, 'searchInternalUser'])->name('user.search');
            });

            // Master Role & Permission
            Route::prefix('role')->name('role.')->middleware('can:role-list')->group(function () {
                Route::get('/', [RoleController::class, 'index'])->name('index');
                Route::post('/', [RoleController::class, 'store'])->name('store')->middleware('can:role-create');
                Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy')->middleware('can:role-create');
                Route::get('/{role}/permissions', [RoleController::class, 'getPermissions'])->name('permissions.get')->middleware('can:role-permission-assign');
                Route::post('/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('permissions.update')->middleware('can:role-permission-assign');
            });

            // Master Questionnaire
            Route::middleware('can:questionnaire-list')->group(function () {
                Route::get('/questionnaire-form', [QuestionnaireFormController::class, 'index'])->name('questionnaire-form.index');
                Route::get('/questionnaire-form/create', [QuestionnaireFormController::class, 'create'])->name('questionnaire-form.create')->middleware('can:questionnaire-create');
                Route::post('/questionnaire-form', [QuestionnaireFormController::class, 'store'])->name('questionnaire-form.store')->middleware('can:questionnaire-create');
                Route::get('/questionnaire-form/{questionnaire_form}', [QuestionnaireFormController::class, 'show'])->name('questionnaire-form.show');
                Route::get('/questionnaire-form/{questionnaire_form}/edit', [QuestionnaireFormController::class, 'edit'])->name('questionnaire-form.edit')->middleware('can:questionnaire-edit');
                Route::put('/questionnaire-form/{questionnaire_form}', [QuestionnaireFormController::class, 'update'])->name('questionnaire-form.update')->middleware('can:questionnaire-edit');
                Route::delete('/questionnaire-form/{questionnaire_form}', [QuestionnaireFormController::class, 'destroy'])->name('questionnaire-form.destroy')->middleware('can:questionnaire-edit');

                Route::get('/questionnaire-form/{form}/questions', [QuestionnaireFormController::class, 'questions'])->name('questionnaire-form.questions');
                Route::post('/questionnaire-form/{form}/questions', [QuestionnaireFormController::class, 'storeQuestion'])->name('questionnaire-form.questions.store')->middleware('can:questionnaire-create');
                Route::post('/questionnaire-form/{form}/questions/import', [QuestionnaireFormController::class, 'importQuestions'])->name('questionnaire-form.questions.import')->middleware('can:questionnaire-create');
                Route::put('/questionnaire-form/{form}/questions/{question}', [QuestionnaireFormController::class, 'updateQuestion'])->name('questionnaire-form.questions.update')->middleware('can:questionnaire-edit');
                Route::delete('/questionnaire-form/{form}/questions/{question}', [QuestionnaireFormController::class, 'destroyQuestion'])->name('questionnaire-form.questions.destroy')->middleware('can:questionnaire-edit');
            });

            // Evaluasi Vendor (Admin)
            Route::prefix('evaluasi-vendor')->name('admin.evaluasi.')->middleware('can:evaluasi-list')->group(function () {
                // Dashboard & Evaluasi Bulanan
                Route::get('/', [EvaluasiVendorController::class, 'index'])->name('index');
                Route::get('/fetch-qad', [EvaluasiVendorController::class, 'fetchQadData'])->name('fetch-qad');
                Route::post('/monthly', [EvaluasiVendorController::class, 'storeMonthly'])->name('store-monthly')->middleware('can:evaluasi-create');
                Route::post('/batch-monthly', [EvaluasiVendorController::class, 'storeBatchMonthly'])->name('store-batch-monthly')->middleware('can:evaluasi-create');

                // Import & Template Excel QA
                Route::get('/download-template-qa', [EvaluasiImportController::class, 'downloadTemplateQa'])->name('download-template-qa');
                Route::post('/import-qa', [EvaluasiImportController::class, 'importQaExcel'])->name('import-qa')->middleware('can:evaluasi-create');

                // Evaluasi Tahunan & 2-Tier Approval (Manager -> GM Pengadaan)
                Route::post('/annual/generate', [EvaluasiAnnualController::class, 'generateAnnual'])->name('annual.generate')->middleware('can:evaluasi-create');
                Route::post('/annual/{id}/verify-manager', [EvaluasiAnnualController::class, 'verifyManager'])->name('annual.verify-manager')->middleware('can:evaluasi-verify-manager');
                Route::post('/annual/{id}/approve-gm', [EvaluasiAnnualController::class, 'approveGm'])->name('annual.approve-gm')->middleware('can:evaluasi-approve-gm');
                Route::post('/annual/{id}/approve', [EvaluasiAnnualController::class, 'approveAnnual'])->name('annual.approve')->middleware('can:evaluasi-approve');
                Route::post('/annual/{id}/trigger-action', [EvaluasiAnnualController::class, 'triggerAction'])->name('annual.trigger-action');

                // Pengaturan Bobot & Batas Threshold
                Route::get('/settings', [EvaluasiSettingController::class, 'settings'])->name('settings')->middleware('can:evaluasi-settings');
                Route::post('/settings', [EvaluasiSettingController::class, 'updateSettings'])->name('settings.update')->middleware('can:evaluasi-settings');
            });

            // Evaluasi Vendor (Vendor Dashboard)
            Route::get('/vendor/evaluasi-kinerja', [VendorEvaluasiDashboardController::class, 'index'])->name('vendor.evaluasi.index');
        });
    }
);
