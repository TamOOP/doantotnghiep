<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchStudentAttemptRequest extends FormRequest
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
            'search-condition' => 'required|in:name,email,result,grade-morethan',
            'search-keyword' => 'nullable|string',
            'status-option' => 'nullable|integer|in:0,1',
            'grade-option' => 'nullable|numeric',
        ];
    }
}
