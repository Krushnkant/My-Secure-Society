<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessProfileCategory extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'business_profile_category';
    protected $primaryKey = 'business_profile_category_id';

}
