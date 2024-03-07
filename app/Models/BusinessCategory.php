<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessCategory extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'business_category';
    protected $primaryKey = 'business_category_id';
    protected $dates = ['deleted_at'];
}
