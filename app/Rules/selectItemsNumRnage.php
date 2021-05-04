<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class selectItemsNumRnage implements Rule
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
        return in_array($value, ['2', '3', '4']);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '選択肢は2〜4を指定してください。';
    }
}
