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
}
