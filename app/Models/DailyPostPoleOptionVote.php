<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyPostPoleOptionVote extends Model
{
    use HasFactory;
    protected $table = 'daily_post_pole_option_vote';
    protected $primaryKey = 'daily_post_pole_option_vote_id';
    public $timestamps = false;
}
