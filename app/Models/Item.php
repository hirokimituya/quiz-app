<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    /** 文字->数値変換 */
    const STR_NUM = [
        'one' => 1,
        'two' => 2,
        'three' => 3,
        'four' => 4,
        'five' => 5,
        'six' => 6,
        'seven' => 7,
        'eight' => 8,
        'nine' => 9,
        'ten' => 10,
    ];

    /** 数字->文字変換 */
    const NUM_STR = [
        '1' => 'one',
        '2' => 'two',
        '3' => 'three',
        '4' => 'four',
    ];

    /**
     * 複数代入可能な属性
     *
     * @var array
     */
    protected $fillable = [
        'question_number',
        'format',
        'question',
        'answer',
        'choice1',
        'choice2',
        'choice3',
        'choice4',
    ];
}
