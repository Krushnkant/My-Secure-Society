<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmenityFile extends Model
{
    use HasFactory;
    protected $table = 'amenity_file';
    protected $primaryKey = 'amenity_file_id';
    public $timestamps = false;
}
