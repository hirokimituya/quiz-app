<?php

namespace App\Http\Requests;

use App\Rules\AnswerCheckCount;
use App\Rules\AnswerRadioRange;
use App\Rules\selectItemsNumRnage;
use Illuminate\Foundation\Http\FormRequest;

class QuizCreateRequest extends FormRequest
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
            'title' => 'required|max:100',
            'description' => 'max:255',
            'genre' => 'required|exists:genres,id',
            'image' => 'nullable|file|image|max:1024',
            'question.*.question' => 'required|max:255',
            'question.*.answerFormmat' => 'required|between:1,3',
            'question.*.answerText' => 'required_if:question.*.answerFormmat,1|max:255',
            'question.*.answerRadio' => ['required_if:question.*.answerFormmat,2', new AnswerRadioRange],
            'question.*.answerCheck' => ['required_if:question.*.answerFormmat,3','array', new AnswerCheckCount],
            'question.*.selectItemsNum' => ['required_if:question.*.answerFormmat,2,3', new selectItemsNumRnage],
            'question.*.selectItemText.*' => 'required_if:question.*.answerFormmat,2,3|max:255',
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
            'title' => 'タイトル',
            'description' => '説明',
            'genre' => 'ジャンル',
            'image' => '画像',
            'question.*.question' => '問題文',
            'question.*.answerFormmat' => '回答形式',
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
            'question.*.selectItemText.*.required_if' => '選択文は必ず入力してください。',
            'question.*.answerCheck.required_if' => '最低1つはチェックを入れてください。',
        ];
    }
}
