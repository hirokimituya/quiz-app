<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    const FORMAT_DESCRIPTION = 1;
    const FORMAT_RADIO = 2;
    const FORMAT_CHECK = 3;

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
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
        10 => 'ten'
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

    /**
     * 配列に対して非表示にする必要がある属性
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * アイテムが選択式の場合、項目数を返す
     * @return int アイテムの項目数
     */
    public function getChoicesNum(): int
    {
        if ($this->attributes['format'] == self::FORMAT_DESCRIPTION) {
            return 0;
        }
        else if ($this->attributes['choice2'] === null) {
            return 1;
        }
        else if ($this->attributes['choice3'] === null) {
            return 2;
        }
        else if ($this->attributes['choice4'] === null) {
            return 3;
        }
        else {
            return 4;
        }
    }

    /**
     * アイテムの項目を配列で返す。
     * @return array アイテムの項目の配列
     */
    public function getChoices(): array
    {
        $result_ary = [];
        if ($this->attributes['choice1'] !== null) {
            $result_ary['one'] = $this->attributes['choice1'];
        }
        if ($this->attributes['choice2'] !== null) {
            $result_ary['two'] = $this->attributes['choice2'];
        }
        if ($this->attributes['choice3'] !== null) {
            $result_ary['three'] = $this->attributes['choice3'];
        }
        if ($this->attributes['choice4'] !== null) {
            $result_ary['four'] = $this->attributes['choice4'];
        }
        return $result_ary;
    }

    /**
     * アイテムが選択式の場合、正解の文字列または、配列を返す。
     * @return array|string アイテムの正解の文字列または、配列を返す。
     */
    public function getCorrectStr(): mixed
    {
        switch ($this->attributes['format']) {
            case self::FORMAT_DESCRIPTION:
                return $this->attributes['answer'];
            case self::FORMAT_RADIO:
                $correct_num = self::STR_NUM[$this->attributes['answer']];
                return $this->attributes['choice' . $correct_num];
            case self::FORMAT_CHECK:
                $correct_num_ary = explode(',', $this->attributes['answer']);
                return array_map(fn($n) => $this->attributes['choice' . self::STR_NUM[$n]], $correct_num_ary);
        }
    }
}
