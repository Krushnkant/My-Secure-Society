<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveredCourierFile extends Model
{
    use HasFactory;
    protected $table = 'delivered_courier_file';
    protected $primaryKey = 'delivered_courier_file_id';
    public $timestamps = false;
}
