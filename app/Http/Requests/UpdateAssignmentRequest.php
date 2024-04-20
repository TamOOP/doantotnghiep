<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssignmentRequest extends FormRequest
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
            'max-grade' => 'required|integer',
            'grade-pass' => 'required|integer',
            'cb-start' => 'nullable|in:on',
            'cb-end' => 'nullable|in:on',
            'file' => 'nullable|file|max:2048',
            'isDeleted' => 'required|in:true,false',
            'date-start' => 'date',
            'date-end' => 'date',
            'start-hour' => 'integer | between:0,23',
            'start-minute' => 'integer | between:0,60',
            'end-hour' => 'integer | between:0,23',
            'end-minute' => 'integer | between:0,60',
        ];
    }
}
