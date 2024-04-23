<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Amenity extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'amenity';
    protected $primaryKey = 'amenity_id';
    protected $dates = ['deleted_at'];
}
