<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessPrifileFile extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'business_profile_file';
    protected $primaryKey = 'business_profile_file_id';
    protected $dates = ['deleted_at'];
}
