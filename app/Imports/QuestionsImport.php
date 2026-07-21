<?php

namespace App\Imports;

use App\Models\VendorAuditQuestionTemplate;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;

class QuestionsImport implements ToCollection, WithHeadingRow
{
    protected $formId;
    protected $createdBy;

    public function __construct($formId)
    {
        $this->formId = $formId;
        $this->createdBy = Auth::id();
    }

    public function collection(Collection $rows)
    {
        // 👱‍♀️ ponytail: no complex validation per row, just insert what's valid. minimum that works.
        // Ceiling: If rows are empty or missing 'section', it skips. Upgrade path: add WithValidation concern later.
        
        foreach ($rows as $index => $row) {
            if (empty($row['section']) || empty($row['question'])) {
                continue;
            }
            
            VendorAuditQuestionTemplate::create([
                'form_id'     => $this->formId,
                'section'     => $row['section'],
                'question'    => $row['question'],
                'answer_type' => $row['answer_type'] ?? 'text',
                'options'     => !empty($row['options']) ? implode("\n", array_map('trim', explode(',', $row['options']))) : null,
                'weight'      => isset($row['weight']) ? (int) $row['weight'] : 0,
                'is_required' => isset($row['is_required']) ? filter_var($row['is_required'], FILTER_VALIDATE_BOOLEAN) : true,
                'is_active'   => isset($row['is_active']) ? filter_var($row['is_active'], FILTER_VALIDATE_BOOLEAN) : true,
                'order'       => isset($row['order']) ? (int) $row['order'] : ($index + 1),
                'created_by'  => $this->createdBy,
            ]);
        }
    }
}
