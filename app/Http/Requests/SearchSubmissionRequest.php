<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchSubmissionRequest extends FormRequest
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
            'search-condition' => 'nullable|in:name,email,grade-status',
            'search-keyword' => 'nullable|string',
            'search-option' => 'nullable|string|in:grade,not-grade',
        ];
    }
}
