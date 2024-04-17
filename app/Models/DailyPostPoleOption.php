<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyPostPoleOption extends Model
{
    use HasFactory;
    protected $table = 'daily_post_pole_option';
    protected $primaryKey = 'daily_post_pole_option_id';
    public $timestamps = false;
}
