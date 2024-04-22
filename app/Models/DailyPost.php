<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

}