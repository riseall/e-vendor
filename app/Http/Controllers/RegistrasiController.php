<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormUmumRequest;
use App\Models\VendorApplication;
use App\Models\VendorApplicationCategory;
use App\Services\SupplierItemService;
use App\Services\VendorRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegistrasiController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Cek apakah ada draft yang belum selesai
        $application = VendorApplication::where('user_id', $user->id)
            ->where('status', VendorApplication::STATUS_DRAFT)
            ->with(['general', 'products', 'categories', 'documents'])
            ->latest()
            ->first();

        $hasDraft    = $application ? true : false;
        $draftStep   = $application ? $application->current_step : 1;
        $applicationId = $application ? $application->id : null;

        $isReadOnly = false;
        $draft = [];
        $uploadedDocs = [];

        if ($application) {
            $draft['categories'] = $application->getCategoryIds();
            $draft['general'] = $application->general;

            $draft['products'] = $application->products->mapWithKeys(function ($item) {
                return [$item->erp_product_id => $item->toArray()];
            })->toArray();

            $uploadedDocs = $application->documents->keyBy('field_name')->map(function ($doc) {
                return [
                    'original_name' => $doc->original_name,
                    'url' => asset('storage/' . $doc->file_path),
                ];
            })->toArray();

            if ($application->status == VendorApplication::STATUS_SUBMITTED) {
                $isReadOnly = true;
            }
        }

        return view('admin.registrasi.reg', compact(
            'hasDraft',
            'draftStep',
            'applicationId',
            'draft',
            'isReadOnly',
            'uploadedDocs'
        ));
    }

    public function saveDraft(Request $request)
    {
        $request->validate([
            'categories' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();
            $user = Auth::user();

            // Gunakan updateOrCreate agar tidak duplikat data saat klik draft berkali-kali
            $application = VendorApplication::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'status'  => VendorApplication::STATUS_DRAFT
                ],
                [
                    'current_step' => 1,
                    'updated_at'   => now()
                ]
            );

            // Sync Kategori
            VendorApplicationCategory::where('application_id', $application->id)->delete();
            foreach ($request->categories as $catId) {
                VendorApplicationCategory::create([
                    'application_id' => $application->id,
                    'category_id'    => $catId
                ]);
            }

            DB::commit();

            return response()->json([
                'status'         => 'success',
                'message'        => 'Draft kategori berhasil disimpan.',
                'application_id' => $application->id // Kunci agar step 2 bisa jalan
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function saveUmum(StoreFormUmumRequest $request, VendorRegistrationService $service)
    {
        try {
            $user = Auth::user();

            // Pastikan aplikasi memang milik user yang login
            $application = VendorApplication::where('id', $request->application_id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            $result = $service->saveFormUmum(
                $request->validated(),
                $application->id,
                $request->action
            );

            return response()->json([
                'status'  => 'success',
                'message' => $request->action === 'submit' ? 'Data berhasil disubmit!' : 'Draft berhasil diperbarui.',
                'application_id' => $result['application_id']
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memproses data.',
                'debug'   => $e->getMessage() // Hapus 'debug' saat production
            ], 500);
        }
    }

    public function submit(Request $request)
    {
        // TODO: akan diisi saat step 5 selesai
    }



    public function searchProducts(Request $request, SupplierItemService $qad)
    {
        $keyword = $request->keyword;
        $page    = $request->page ?? 1;

        // Proteksi karakter minimal
        if (strlen($keyword) < 3) {
            return response()->json(['results' => [], 'pagination' => ['more' => false]]);
        }

        $data = $qad->searchItems($keyword, $page, 20);

        return response()->json([
            'results' => collect($data['items'])->map(function ($item) {
                return [
                    'id'           => $item['id'],
                    // 'text'         => $item['id'] . ' - ' . $item['desc'],
                    'text'         => $item['desc'],
                    'product_name' => $item['desc']
                ];
            }),
            'pagination' => ['more' => $data['hasMore']]
        ]);
    }
}
