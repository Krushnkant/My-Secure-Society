<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessProfileFile extends Model
{
    use HasFactory;

    protected $table = 'business_profile_file';
    protected $primaryKey = 'business_profile_file_id';
    public $timestamps = false;
}
