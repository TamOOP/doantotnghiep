<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExaminationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'cb-start' => 'nullable|in:on',
            'cb-end' => 'nullable|in:on',
            'cb-limit' => 'nullable|in:on',
            'date-start' => 'date',
            'date-end' => 'date',
            'start-hour' => 'integer | between:0,23',
            'start-minute' => 'integer | between:0,60',
            'end-hour' => 'integer | between:0,23',
            'end-minute' => 'integer | between:0,60',
            'time-limit' => 'integer|min:0',
            'time-unit' => 'integer|in:1,60,3600,86400',
            'grade-scale' => 'required|integer|min:1',
            'grade-pass' => 'required|integer|min:0',
            'attempt-allow' => 'required|integer|between:0,10',
            'grading-method' => 'required|integer|in:0,1,2,3',
            'question-per-page' => 'required|integer|between:0,50',
            'random-answer' => 'required|integer|in:0,1',
            'show-answer' => 'required|integer|in:0,1',
        ];
    }
}
