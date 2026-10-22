<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExistingRiskAssessmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'vendor_application_id'       => 'required|exists:vendor_applications,id',

            // Section A: Safety Efficacy
            'score_safety_efficacy_doc'   => 'required|integer|in:1,3,4',
            'score_complaint_side_effect' => 'required|integer|in:1,2,3,4',
            'score_safety_efficacy_attr'  => 'required|integer|in:1,8',

            // Section B: Availability
            'score_pareto'                => 'required|integer|in:1,2,3,4',
            'score_purchase_volume'       => 'required|integer|in:1,2,3,4',
            'score_availability_type'     => 'required|integer|in:1,2,3,4',
            'score_nie_status'            => 'required|integer|in:1,2,3',

            // Section C: Detectability
            'score_audit_year'            => 'required|integer|in:1,2,3,4',
            'score_audit_result'          => 'required|integer|in:1,2,3,4',
            'score_audit_response'        => 'required|integer|in:1,4',
            'score_complaint_count'       => 'required|integer|in:1,2,3,4',

            // Section D: Probability
            'score_material_grade'        => 'required|integer|in:1,2,3,4',
            'score_facility_variation'    => 'required|integer|in:1,2,3,4',
            'score_material_purpose'      => 'required|integer|in:1,2,3,4',

            'qa_manager_id'               => 'nullable|exists:users,id',
            'notes'                       => 'nullable|string|max:2000',
        ];
    }
}
