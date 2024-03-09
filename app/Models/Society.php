<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Society extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'society';
    protected $primaryKey = 'society_id';

    protected $dates = ['deleted_at'];
}
