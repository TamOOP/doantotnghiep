<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchStudentRequest extends FormRequest
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
            'course_id' => 'required',
            'search-condition' => 'nullable|in:name,email,enrol-method,last-active',
            'search-keyword' => 'nullable|string',
            'method-option' => 'nullable|in:0,1,2',
            'time-option' => 'nullable|in:1 day,2 day,3 day,4 day,5 day,6 day,1 week,2 week,3 week,4 week',
        ];
    }
}
