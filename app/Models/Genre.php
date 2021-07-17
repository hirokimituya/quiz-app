<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    /** JSONに含めない属性 */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /** JSONに含めるアクセサ */
    protected $appends = [
        'quiz_count',
    ];

    /**
     * リレーションシップ - quizzesテーブル
     * @ return \Illuminate\Database\Eloguent\Relations\HasMany
     */
    public function quizzes() {
        return $this->hasMany(Quiz::class);
    }

    /**
     * アクセサ - quiz_count
     * @return string
     */
    public function getQuizCountAttribute()
    {
        return $this->quizzes()->count();
    }
}
