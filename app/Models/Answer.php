<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Answer extends Model
{
    use HasFactory;

    /**
     * 複数代入可能な属性
     *
     * @var array
     */
    protected $fillable = [
        'item_id',
        'answer',
        'pass',
    ];

    /**
     * リレーションシップ - itemsテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id', 'items');
    }

    /**
     * 同じクイズアイテムについて、ユーザーが正解したことがあるかどうかを判定する
     * @return boolean
     */
    public function isNeverBeforeCorrect()
    {
        if (!$this->attributes['pass'] || !Auth::check()) {
            return false;
        }

        $correctCount = self::leftJoin('grades', 'answers.grade_id', '=', "grades.id")
            ->where('answers.item_id', $this->attributes['item_id'])
            ->where('answers.pass', true)
            ->where('grades.user_id', Auth::id())
            ->count();
        
        if ($correctCount > 0) {
            return false;
        }

        return true;
    }
}
