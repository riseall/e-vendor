<?php

namespace App\Http\Controllers;

use App\Models\VendorAuditQuestionnaireForm;
use App\Models\VendorAuditQuestionTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionnaireFormController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function authorizeAdmin(): void
    {
        $user = Auth::user();
        abort_unless($user && ($user->hasAnyRole(['Super Admin', 'Admin IT']) || $user->can('questionnaire-list')), 403);
    }

    public function index()
    {
        $this->authorizeAdmin();
        $forms = VendorAuditQuestionnaireForm::orderBy('order')->orderBy('name')->get();
        return view('admin.questionnaire.index', compact('forms'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'code'            => 'required|string|max:80|unique:vendor_audit_questionnaire_forms,code',
            'name'            => 'required|string|max:255',
            'material_type'   => 'required|string|in:bahan_baku,bahan_kemas,produk_jadi,alkes',
            'document_number' => 'nullable|string|max:80',
            // 'description'     => 'nullable|string',
            'is_active'       => 'nullable|boolean',
            'order'           => 'required|integer|min:0',
        ]);

        $data['is_active'] = $request->has('is_active') ? true : false;
        $data['created_by'] = Auth::id();

        VendorAuditQuestionnaireForm::create($data);

        return redirect()->route('questionnaire-form.index')->with('success', 'Form Kuesioner berhasil ditambahkan.');
    }

    public function update(Request $request, VendorAuditQuestionnaireForm $questionnaireForm)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'code'            => 'required|string|max:80|unique:vendor_audit_questionnaire_forms,code,' . $questionnaireForm->id,
            'name'            => 'required|string|max:255',
            'material_type'   => 'required|string|in:bahan_baku,bahan_kemas,produk_jadi,alkes',
            'document_number' => 'nullable|string|max:80',
            // 'description'     => 'nullable|string',
            'is_active'       => 'nullable|boolean',
            'order'           => 'required|integer|min:0',
        ]);

        $data['is_active'] = $request->has('is_active') ? true : false;

        $questionnaireForm->update($data);

        return redirect()->route('questionnaire-form.index')->with('success', 'Form Kuesioner berhasil diperbarui.');
    }

    public function destroy(VendorAuditQuestionnaireForm $questionnaireForm)
    {
        $this->authorizeAdmin();
        $questionnaireForm->delete();
        return redirect()->route('questionnaire-form.index')->with('success', 'Form Kuesioner berhasil dihapus.');
    }

    public function questions(VendorAuditQuestionnaireForm $form)
    {
        $this->authorizeAdmin();
        $questions = VendorAuditQuestionTemplate::where('form_id', $form->id)
            ->orderBy('order')
            ->get();

        return view('admin.questionnaire.questions', compact('form', 'questions'));
    }

    public function storeQuestion(Request $request, VendorAuditQuestionnaireForm $form)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'section'      => 'required|string|max:255',
            'question'     => 'required|string',
            'answer_type'  => 'required|string|in:yes_no,multiple_choice,text,document',
            'options'      => 'nullable|string',
            'weight'       => 'required|integer|min:0',
            'is_required'  => 'nullable|boolean',
            'is_active'    => 'nullable|boolean',
            'order'        => 'required|integer|min:0',
        ]);

        $data['form_id'] = $form->id;
        $data['is_required'] = $request->has('is_required') ? true : false;
        $data['is_active'] = $request->has('is_active') ? true : false;
        $data['created_by'] = Auth::id();

        if (!empty($data['options'])) {
            // Convert newline-separated options into an array
            $data['options'] = array_filter(array_map('trim', explode("\n", $data['options'])));
        } else {
            $data['options'] = null;
        }

        VendorAuditQuestionTemplate::create($data);

        return redirect()->route('questionnaire-form.questions', $form->id)->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    public function updateQuestion(Request $request, VendorAuditQuestionnaireForm $form, VendorAuditQuestionTemplate $question)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'section'      => 'required|string|max:255',
            'question'     => 'required|string',
            'answer_type'  => 'required|string|in:yes_no,multiple_choice,text,document',
            'options'      => 'nullable|string',
            'weight'       => 'required|integer|min:0',
            'is_required'  => 'nullable|boolean',
            'is_active'    => 'nullable|boolean',
            'order'        => 'required|integer|min:0',
        ]);

        $data['is_required'] = $request->has('is_required') ? true : false;
        $data['is_active'] = $request->has('is_active') ? true : false;

        if (!empty($data['options'])) {
            $data['options'] = array_filter(array_map('trim', explode("\n", $data['options'])));
        } else {
            $data['options'] = null;
        }

        $question->update($data);

        return redirect()->route('questionnaire-form.questions', $form->id)->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    public function destroyQuestion(VendorAuditQuestionnaireForm $form, VendorAuditQuestionTemplate $question)
    {
        $this->authorizeAdmin();
        $question->delete();
        return redirect()->route('questionnaire-form.questions', $form->id)->with('success', 'Pertanyaan berhasil dihapus.');
    }

    public function importQuestions(Request $request, VendorAuditQuestionnaireForm $form)
    {
        $this->authorizeAdmin();
        $request->validate([
            'file' => 'required|file'
        ]);

        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension(); // 'xlsx' or 'csv'

            $rows = \Spatie\SimpleExcel\SimpleExcelReader::create($file->getRealPath(), $extension)->getRows();

            $order = 1;
            foreach ($rows as $row) {
                // Ensure array keys exist since empty cells might not be set depending on the parser
                $section = $row['section'] ?? '';
                $question = $row['question'] ?? '';

                if (empty($section) || empty($question)) continue;

                VendorAuditQuestionTemplate::create([
                    'form_id'     => $form->id,
                    'section'     => $section,
                    'question'    => $question,
                    'answer_type' => !empty($row['answer_type']) ? $row['answer_type'] : 'text',
                    'options'     => !empty($row['options']) ? array_values(array_filter(array_map('trim', explode(',', $row['options'])))) : null,
                    'weight'      => isset($row['weight']) && $row['weight'] !== '' ? (int) $row['weight'] : 0,
                    'is_required' => isset($row['is_required']) && $row['is_required'] !== '' ? filter_var($row['is_required'], FILTER_VALIDATE_BOOLEAN) : true,
                    'is_active'   => isset($row['is_active']) && $row['is_active'] !== '' ? filter_var($row['is_active'], FILTER_VALIDATE_BOOLEAN) : true,
                    'order'       => isset($row['order']) && $row['order'] !== '' ? (int) $row['order'] : $order++,
                    'created_by'  => \Illuminate\Support\Facades\Auth::id(),
                ]);
            }

            return redirect()->back()->with('success', 'Questions imported successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing questions: ' . $e->getMessage());
        }
    }
}
