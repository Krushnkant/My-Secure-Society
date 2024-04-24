<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostBanner extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'post_banner';
    protected $primaryKey = 'post_banner_id';
    protected $dates = ['deleted_at'];
}
