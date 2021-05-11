<?php

namespace App\Http\Requests;

use App\Rules\AnswerCheckCount;
use App\Rules\AnswerRadioRange;
use App\Rules\selectItemsNumRnage;
use Illuminate\Foundation\Http\FormRequest;

class QuizAnswerRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'question.*.answerText' => 'nullable|max:255',
            'question.*.answerRadio' => ['nullable', new AnswerRadioRange],
            'question.*.answerCheck' => ['nullable','array', new AnswerCheckCount],
        ];
    }

    /**
     * バリデーションエラーのカスタム属性の取得
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'question.*.question' => '問題文',
            'question.*.answerFormat' => '回答形式',
            'question.*.answerRadio' => '回答',
        ];
    }

    /**
     * 定義済みバリデーションルールのエラーメッセージ取得
     *
     * @return array
     */
    public function messages()
    {
        return [
            'question.*.answerText.required_if' =>  '回答は必ず入力してください。',
            'question.*.answerCheck.required_if' => '最低1つはチェックを入れてください。',
        ];
    }
}
