<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorrectRecord extends Model
{
    use HasFactory;

    /**
     * 1週間未満のデータを取得する
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWeekly($query)
    {   
        return $query->where('created_at', '>', now()->subWeek());
    }

    /**
     * 1ヶ月未満のデータを取得する
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMonthly($query)
    {   
        return $query->where('created_at', '>', now()->subMonth());
    }
}
