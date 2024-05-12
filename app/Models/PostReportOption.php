<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostReportOption extends Model
{
    use HasFactory;
    protected $table = 'daily_post_report_option'; 
    protected $primaryKey = 'daily_post_report_option_id'; 
}
