<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    /**
     * 複数代入可能な属性
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'correct_count',
    ];

    /**
     * ユーザに対するクイズの成績を取得する
     * 
     * @param \Illuminate\Database\Eloquent\Builder  $query
     * @param app\Models\Quiz $quiz
     * @param app\Models\User $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeQuizUserGet($query, $quiz, $user) 
    {
        $query->where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(10);
    }

    /**
     * 引数のソートタイプをもとにソートする
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSort($query, $type)
    {
        switch ($type) {
            case 'quiz_title':
                return $query
                            ->select('grades.*')
                            ->leftJoin('quizzes', 'grades.quiz_id', '=', 'quizzes.id')
                            ->orderBy('quizzes.title');
            default:    /* case 'latest': */
                return $query->latest();
        }
    }

    /**
     * リレーションシップ - quizzesテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'id', 'quizzes');
    }
}
