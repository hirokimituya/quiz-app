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
}
