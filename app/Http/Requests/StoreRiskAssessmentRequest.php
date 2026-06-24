<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRiskAssessmentRequest extends FormRequest
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
            'vendor_application_id' => 'required|exists:vendor_applications,id',
            'score_safety_efficacy_attr' => 'required|integer|in:1,8',
            'score_detectability_country' => 'required|integer|in:1,2,3,4',
            'score_detectability_warning' => 'required|integer|in:1,4',
            'score_probability_function' => 'required|integer|in:1,2,3,4',
            'qa_manager_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:2000',
        ];
    }
}
