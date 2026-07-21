<?php

namespace App\Http\Controllers;

use App\Models\VendorAudit;
use App\Models\VendorAuditQuestionTemplate;
use App\Services\VendorApplicationWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VendorQuestionnaireController extends Controller
{
    public function show(int $auditId): View
    {
        $audit = $this->loadAuditForVendor($auditId);

        $application = $audit->application;

        // Ponytail: prefer form_id-based lookup. Falls back to category-based for old audits
        // (rows where questionnaire_form_id was not set during migration). Add when: all audits
        // migrated, drop the fallback.
        if ($audit->questionnaire_form_id) {
            $questions = VendorAuditQuestionTemplate::forForm($audit->questionnaire_form_id);
        } else {
            $categoryIds = $application->categories->pluck('category_id')->all();
            $questions   = VendorAuditQuestionTemplate::forCategories($categoryIds);
        }

        $payload = $audit->questionnaire_payload ?? [];
        $revisionNotes = $audit->questionnaire_revision_notes ?? [];

        return view('admin.questionnaire.questionnaire', [
            'audit'         => $audit,
            'application'   => $application,
            'questions'     => $questions,
            'payload'       => $payload,
            'revisionNotes' => $revisionNotes,
        ]);
    }

    public function saveDraft(Request $request, int $auditId)
    {
        $audit = $this->loadAuditForVendor($auditId);

        abort_unless(
            in_array($audit->status, [
                VendorAudit::STATUS_QUESTIONNAIRE_PROGRESS,
                VendorAudit::STATUS_NEED_REVISION,
            ], true),
            422,
            'Questionnaire tidak dapat disimpan pada status ini.'
        );

        $data = $request->validate([
            'answers'                => 'nullable|array',
            'answers.*'              => 'nullable',
        ]);

        $answers = $data['answers'] ?? [];



        // Merge existing payload to not lose previous files if not re-uploaded
        $existingPayload = $audit->questionnaire_payload ?? [];
        $mergedAnswers = array_replace($existingPayload, $answers);

        $audit->update([
            'questionnaire_payload' => $mergedAnswers,
            'status' => VendorAudit::STATUS_QUESTIONNAIRE_PROGRESS,
        ]);

        return response()->json([
            'ok'      => true,
            'message' => 'Draft questionnaire tersimpan.',
            'saved_at' => now()->toDateTimeString(),
        ]);
    }

    public function submit(Request $request, int $auditId, VendorApplicationWorkflowService $workflow)
    {
        $audit = $this->loadAuditForVendor($auditId);

        abort_unless(
            in_array($audit->status, [
                VendorAudit::STATUS_QUESTIONNAIRE_PROGRESS,
                VendorAudit::STATUS_NEED_REVISION,
            ], true),
            422
        );

        $data = $request->validate([
            'answers'   => 'required|array|min:1',
            'answers.*' => 'required',
        ]);

        $answers = $data['answers'];


        
        // Merge existing payload to not lose previous files if not re-uploaded
        $existingPayload = $audit->questionnaire_payload ?? [];
        $mergedAnswers = array_replace($existingPayload, $answers);

        DB::transaction(function () use ($audit, $mergedAnswers, $workflow) {
            $audit->update([
                'questionnaire_payload'        => $mergedAnswers,
                'questionnaire_submitted_at'   => now(),
                'status'                       => VendorAudit::STATUS_QUESTIONNAIRE_SUBMITTED,
                'questionnaire_revision_notes' => null, // reset notes after resubmit
            ]);

            $workflow->record(
                $audit->application,
                'questionnaire_submitted',
                Auth::user(),
                ['audit_id' => $audit->id]
            );
        });

        return redirect()
            ->route('vendor.audit.questionnaire', $audit->id)
            ->with('success', 'Questionnaire berhasil di-submit. Menunggu verifikasi QA.');
    }

    private function loadAuditForVendor(int $auditId): VendorAudit
    {
        $audit = VendorAudit::with([
            'application.user',
            'application.general',
            'application.categories',
        ])
            ->findOrFail($auditId);

        abort_unless(
            $audit->application->user_id === Auth::id(),
            403,
            'Anda tidak memiliki akses ke audit ini.'
        );

        abort_unless(
            $audit->audit_type === VendorAudit::TYPE_ON_DESK,
            404,
            'Audit ini bukan on-desk questionnaire.'
        );

        return $audit;
    }
}
