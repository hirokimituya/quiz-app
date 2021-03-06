<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AnswerCheckCount implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return in_array(count($value), [1, 2, 3, 4]);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'チェックの数は1〜4個までです。';
    }
}
