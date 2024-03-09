<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Flat extends Model
{
    use HasFactory;
    use HasFactory,SoftDeletes;

    protected $table = 'block_flat';
    protected $primaryKey = 'block_flat_id';
    protected $dates = ['deleted_at'];


   
}
