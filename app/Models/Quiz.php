<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Grade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quiz extends Model
{
    use HasFactory;

    /**
     * 複数代入可能な属性
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'genre_id',
        'title',
        'description',
        'filename',
    ];

    /** JSONに含めるアクセサ */
    protected $appends = [
        'url',
        'formattedCreatedAt',
        'gradesCount',
        'commentsCount',
        'likesCount',
        'likedByUser'
    ];

    /** JSONに含めない属性 */
    protected $hidden = [
        'user_id',
        'genre_id',
        'filename',
        'created_at',
        'updated_at',
        'likes',
    ];

    /** 1ページあたりのアイテム数 */
    protected $perPage = 10;

    /** 常にロードする必要があるリレーション */
    protected $with = ['user', 'genre'];

    /**
     * リレーションシップ - usersテーブル
     * @ return \Illuminate\Database\Eloguent\Relations\BellngsTo
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * リレーションシップ - genresテーブル
     * @ return \Illuminate\Database\Eloguent\Relations\BellngsTo
     */
    public function genre() {
        return $this->belongsTo(Genre::class);
    }

    /**
     * リレーションシップ - itemsテーブル
     * @ return \Illuminate\Database\Eloguent\Relations\HasMany
     */
    public function items() {
        return $this->hasMany(Item::class);
    }

    /**
     * リレーションシップ - gradesテーブル
     * @ return \Illuminate\Database\Eloguent\Relations\HasMany
     */
    public function grades() {
        return $this->hasMany(Grade::class);
    }

    /**
     * リレーションシップ - commentsテーブル
     * @ return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments() {
        return $this->hasMany(Comment::class);
    }

    /**
     * リレーションシップ - usersテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }

    /**
     * アクセサ - url
     * @return string
     */
    public function getUrlAttribute()
    {
        if ($this->attributes['filename'] == null) {
            return asset('storage/images/noimage.png');
        }

        return asset('storage/images/' . $this->attributes['filename']);
    }

    /**
     * 整形した期限日
     * @return string
     */
    public function getFormattedCreatedAtAttribute() {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'])->format('Y/m/d');
    }

    /**
     * 回答回数
     * @return int
     */
    public function getGradesCountAttribute() {
        return $this->grades()->count();
    }

    /**
     * コメント数
     * @return int
     */
    public function getCommentsCountAttribute() {
        return $this->comments()->count();
    }

    /**
     * いいね数
     */
    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    /**
     * アクセサ -liked_by_user
     * @return boolean
     */
    public function getLikedByUserAttribute()
    {
        if (Auth::guest()) {
            return false;
        }

        return $this->likes->contains(function($user) {
            return $user->id === Auth::user()->id;
        });
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
            case 'quiz':
                return $this->sortCount($query, 'grades');
            case 'like':
                return $this->sortCount($query, 'likes');
            case 'comment':
                return $this->sortCount($query, 'comments');
            default:    /* case 'new': */
                return $query->latest();
        }
    }

    private function sortCount($query, $table)
    {
        $count_table = self::selectRaw("quizzes.id, COUNT({$table}.quiz_id) AS count")
                            ->leftJoin($table, 'quizzes.id', '=', "{$table}.quiz_id")
                            ->groupBy('quizzes.id');

        return $query
                    ->select('quizzes.*')
                    ->leftJoinSub($count_table, 'count_table', function ($join) {
                        $join->on('quizzes.id', '=', 'count_table.id');
                    })
                    ->orderByDesc('count_table.count')
                    ->orderByDesc('quizzes.created_at');
    }

    /**
     * 引数のタイプをもとに表示するクイズを選択する
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type, $sort_item)
    {
        switch ($type) {
            case 'like':
                return $this->whereOfType($query, 'likes', $sort_item);
            case 'comment':
                return $this->whereOfType($query, 'comments', $sort_item);
            case 'grade':
                return $this->whereOfType($query, 'grades', $sort_item);
            default:    /* case 'make': */
                return $query->where('user_id', Auth::id());
        }
    }

    private function whereOfType($query, $table, $sort_item)
    {
        if (in_array($sort_item, ['quiz', 'like', 'comment'])) {
            return $query
                    ->whereExists(function ($query) use ($table) {
                        $query
                            ->selectRaw(1)
                            ->from($table)
                            ->whereColumn("{$table}.quiz_id", 'count_table.id')
                            ->where("{$table}.user_id", Auth::id());
                    });
        }
        else {
            return $query
                    ->whereExists(function ($query) use ($table) {
                        $query
                            ->selectRaw(1)
                            ->from($table)
                            ->whereColumn("{$table}.quiz_id", 'quizzes.id')
                            ->where("{$table}.user_id", Auth::id());
                    });
        }
    }

    /**
     * 引数の値をもとにtitleとdescriptionから検索する
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search_query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchWith($query, $search_query)
    {
        return $query->where('title', 'like', $search_query)
                        ->orWhere('description', 'like', $search_query);
    }
}
