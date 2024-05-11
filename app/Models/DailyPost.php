<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class DailyPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'society_daily_post';
    protected $primaryKey = 'society_daily_post_id';

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->hasOne(User::class, 'user_id', 'created_by');
    }

    public function poll_options()
    {
        return $this->hasMany(DailyPostPoleOption::class, 'society_daily_post_id');
    }

    public function isLike()
    {
        $user_id = Auth::user()->user_id;
        return $this->likes()->where('user_id', $user_id)->exists();
    }

    public function likes()
    {
        return $this->hasMany('App\Models\DailyPostLike', 'society_daily_post_id');
    }

    public function daily_post_files()
    {
        return $this->hasMany('App\Models\DailyPostFile', 'society_daily_post_id');
    }

}
