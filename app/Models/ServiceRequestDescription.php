<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequestDescription extends Model
{
    use HasFactory;
    protected $table = 'service_request_description';
    protected $primaryKey = 'service_req_desc_id';
    public $timestamps = false;

    public function images()
    {
        return $this->hasMany(ServiceRequestFile::class,'service_req_desc_id', 'service_req_desc_id')->where('file_type',1);
    }

    public function video()
    {
        return $this->hasOne(ServiceRequestFile::class,'service_req_desc_id', 'service_req_desc_id')->where('file_type',2);
    }

    public function createdBy()
    {
        return $this->hasOne(User::class,'user_id', 'created_by');
    }
}
