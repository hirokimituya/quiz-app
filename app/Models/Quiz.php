<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quiz extends Model
{
    use HasFactory;

    /** JSONに含めるアクセサ */
    protected $appends = [
        'url',
        'formattedCreatedAt'
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
     * リレーションシップ - genreテーブル
     * @ return \Illuminate\Database\Eloguent\Relations\BellngsTo
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * リレーションシップ - genreテーブル
     * @ return \Illuminate\Database\Eloguent\Relations\BellngsTo
     */
    public function genre() {
        return $this->belongsTo(Genre::class);
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
}
