<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Grade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
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
        'likedByUser',
        'avgCorrectRate',
        'itemsCount'
    ];

    /** JSONに含めない属性 */
    protected $hidden = [
        'user_id',
        'genre_id',
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
            return Storage::disk('s3')->url('images/noimage.png');
        }

        return Storage::disk('s3')->url('images/' . $this->attributes['filename']);
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
     * 平均正解数
     * @return int
     */
    public function getAvgCorrectRateAttribute() 
    {
        $grades = Grade::where('quiz_id', $this->attributes['id'])->with('quiz')->get();

        if ($grades->count() == 0) {
            return 0;
        }

        $item_count = $this->items()->count();

        $correct_rate_ary = [];
        foreach ($grades as $grade) {
            $correct_count = $grade->correctCount();
            $correct_rate_ary[] = $correct_count / $item_count;
        }
        
        return array_sum($correct_rate_ary) / count($correct_rate_ary) * 100;
    }

    /**
     * アクセサ - items_count
     * @return int
     */
    public function getItemsCountAttribute()
    {
        return $this->items()->count();
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
    public function scopeOfType($query, $user_id, $type, $sort_item)
    {
        switch ($type) {
            case 'like':
                return $this->whereOfType($query, $user_id, 'likes', $sort_item);
            case 'comment':
                return $this->whereOfType($query, $user_id, 'comments', $sort_item);
            case 'grade':
                return $this->whereOfType($query, $user_id, 'grades', $sort_item);
            default:    /* case 'make': */
                return $query->where('user_id', $user_id);
        }
    }

    private function whereOfType($query, $user_id, $table, $sort_item)
    {
        if (in_array($sort_item, ['quiz', 'like', 'comment'])) {
            return $query
                    ->whereExists(function ($query) use ($table, $user_id) {
                        $query
                            ->selectRaw(1)
                            ->from($table)
                            ->whereColumn("{$table}.quiz_id", 'count_table.id')
                            ->where("{$table}.user_id", $user_id);
                    });
        }
        else {
            return $query
                    ->whereExists(function ($query) use ($table, $user_id) {
                        $query
                            ->selectRaw(1)
                            ->from($table)
                            ->whereColumn("{$table}.quiz_id", 'quizzes.id')
                            ->where("{$table}.user_id", $user_id);
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

    /**
     * クイズの持っているアイテムを整形して返す
     *
     * @param  boolean $ans_need 正解を入れるかどうか
     * @return Item
     */
    public function getItems($ans_need = false)
    {
        $items = Item::where('quiz_id', $this->id)->get();

        $correct_rates = $this->getItemCorrectRate();

        $items_data = [];
        foreach ($items as $item) {
            $key = Item::NUM_STR[$item->question_number];
            $items_data[$key] = [
                'id' => $item->id,
                'question' => $item->question,
                'answerFormat' => $item->format,
            ];

            if ($ans_need && $item->format == Item::FORMAT_DESCRIPTION) {
                $items_data[$key]['answerText'] = $item->answer;
            }

            if ($item->format == Item::FORMAT_RADIO || $item->format == Item::FORMAT_CHECK) {
                $num = 0;
                $text_ary = [];
                for ($i = 1; $i <= 4; $i++) {
                    if (!empty($item['choice' . $i])) {
                        $num++;
                        $text_ary[Item::NUM_STR[$i]] = $item['choice' . $i];
                    }
                }
                $items_data[$key]['selectItemsNum'] = $num;
                $items_data[$key]['selectItemText'] = $text_ary;

                if ($ans_need) {
                    if ($item->format == Item::FORMAT_RADIO) {
                        $answer = Item::STR_NUM[$item->answer];
                        $items_data[$key]['answerRadio'] = $answer;
                    }
                    else {
                        $ans_str = explode(',', $item->answer);
                        $answers = collect($ans_str)->map(fn($str) => Item::STR_NUM[$str]);
                        $items_data[$key]['answerCheck'] = $answers;
                    }
                }
            }
            $items_data[$key]['correctRate'] = $correct_rates[$item->question_number];
        }

        return $items_data;
    }

    /**
     * クイズが持っているアイテムの正答率を返す
     *
     * @return array
     */
    public function getItemCorrectRate()
    {
        $grades = Grade::where('quiz_id', $this->id)->with('quiz')->get();

        $item_count = $this->items()->count();

        $correct_rates = [];

        for ($i = 1; $i <= $item_count; $i++) {
            $correct_rates[$i] = 0;
        }
            
        if ($grades->count() == 0) {
            return $correct_rates;
        }

        foreach ($grades as $grade) {
            $correct_ary = $grade->correctAry();
            if (empty($correct_ary)) {
                continue;
            }

            foreach ($correct_ary as $correct_num) {
                if (isset($correct_rates[$correct_num])) {
                    $correct_rates[$correct_num]++;
                }
            }
        }

        foreach ($correct_rates as $key => $correct_rate) {
            $correct_rates[$key] = $correct_rate / $grades->count() * 100;
        }

        return $correct_rates;
    }
}
