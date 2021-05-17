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
}
