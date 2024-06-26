<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceVendor extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'service_vendor';
    protected $primaryKey = 'service_vendor_id';

    protected $dates = ['deleted_at'];

    public function service_vendor_file()
    {
        return $this->hasOne(ServiceVendorFile::class,'service_vendor_id', 'service_vendor_id');
    }
}
