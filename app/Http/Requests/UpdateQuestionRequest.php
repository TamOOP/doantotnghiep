<?php

namespace App\Http\Requests;

use App\Rules\AtLeastTwoNonNullValues;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class UpdateQuestionRequest extends FormRequest
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
        Log::info('validateCalled');

        return [
            'question-description' => 'required|string',
            'question-mark' => 'required|numeric|min:0',
            'multi-answer' => 'required|integer|in:0,1',
            'choice-numbering' => 'required|string|in:abc,ABCD,iii,IIII,none',
            'choices' => ['required', 'array', 'min:2', new AtLeastTwoNonNullValues],
            'choice-grades' => ['required', 'array', 'min:2', new AtLeastTwoNonNullValues],
            'choice-grades.*' => 'required|numeric|between:-1,1',
        ];
    }
}
