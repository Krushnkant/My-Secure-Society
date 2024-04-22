<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyPostLike extends Model
{
    use HasFactory;
    protected $table = 'daily_post_like';
    protected $primaryKey = 'daily_post_like_id';
    public $timestamps = false;

    protected $fillable = [
        'society_daily_post_id', 
        'user_id',
    ];
}
