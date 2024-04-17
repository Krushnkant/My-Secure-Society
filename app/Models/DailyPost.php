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
}
