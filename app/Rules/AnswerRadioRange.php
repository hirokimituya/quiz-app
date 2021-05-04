<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AnswerRadioRange implements Rule
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
        return in_array($value, ['1', '2', '3', '4']);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'ラジオボタンは1つ選択してください。';
    }
}
