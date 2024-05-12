<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessPrifile extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'business_profile';
    protected $primaryKey = 'business_profile_id';
    protected $dates = ['deleted_at'];
}
