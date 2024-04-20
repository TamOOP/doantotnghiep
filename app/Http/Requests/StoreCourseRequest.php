<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
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
            'name' => 'required',
            'description' => 'nullable|string',
            'cb-start' => 'nullable|in:on',
            'cb-end' => 'nullable|in:on',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'date-start' => 'date',
            'date-end' => 'date',
            'start-hour' => 'integer | between:0,23',
            'start-minute' => 'integer | between:0,60',
            'end-hour' => 'integer | between:0,23',
            'end-minute' => 'integer | between:0,60',
            'enrolment-method' => 'required | in:0,1,2',
        ];
    }
}
