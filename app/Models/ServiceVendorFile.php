<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceVendorFile extends Model
{
    use HasFactory;
    protected $table = 'service_vendor_file';
    protected $primaryKey = 'service_vendor_file_id';
    public $timestamps = false;
}
