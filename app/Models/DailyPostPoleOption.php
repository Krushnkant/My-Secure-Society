<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DailyPostPoleOption extends Model
{
    use HasFactory;
    protected $table = 'daily_post_pole_option';
    protected $primaryKey = 'daily_post_pole_option_id';
    public $timestamps = false;

    public function isVoted()
    {
        $user_id = Auth::user()->user_id;
        return $this->voted()->where('user_id', $user_id)->exists();
    }

    public function voted()
    {
        return $this->hasMany('App\Models\DailyPostPoleOptionVote', 'daily_post_pole_option_id');
    }
}
