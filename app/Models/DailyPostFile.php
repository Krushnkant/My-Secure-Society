<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyPostFile extends Model
{
    use HasFactory;
    protected $table = 'daily_post_file';
    protected $primaryKey = 'daily_post_file_id';
    public $timestamps = false;
}
