<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRating extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'user_rating';
    protected $primaryKey = 'user_rating_id';
    public $timestamps = false;
}
