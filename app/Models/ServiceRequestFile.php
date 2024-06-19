<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequestFile extends Model
{
    use HasFactory;
    protected $table = 'service_request_file';
    protected $primaryKey = 'service_request_file_id';
    public $timestamps = false;
}
