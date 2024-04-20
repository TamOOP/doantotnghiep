<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class AtLeastTwoNonNullValues implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // foreach ($value as $v) {
        //     Log::info($v);
        // }
        $nonNullValues = array_filter($value, function ($item) {
            return !is_null($item);
        });

        return count($nonNullValues) >= 2;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Cần ít nhất 2 câu trả lời';
    }
}
