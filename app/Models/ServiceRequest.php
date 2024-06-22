<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequest extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'service_request';
    protected $primaryKey = 'service_request_id';

    protected $dates = ['deleted_at'];


    public function description()
    {
        return $this->hasOne(ServiceRequestDescription::class,'service_request_id', 'service_request_id');
    }

    public function createdBy()
    {
        return $this->hasOne(User::class,'user_id', 'created_by');
    }

    public function category()
    {
        return $this->hasOne(ServiceCategory::class,'service_category_id', 'service_category_id');
    }

    public function replies()
    {
        return $this->hasMany(ServiceRequestDescription::class,'service_request_id', 'service_request_id');
    }


}
