<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitorGatepass extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'visitor_gatepass';
    protected $primaryKey = 'visitor_gatepass_id';
    public $timestamps = false;

    public function user()
    {
        return $this->hasOne(User::class,'user_id', 'created_by');
    }

    public function daily_help_provider()
    {
        return $this->hasOne(ServiceProvider::class,'daily_help_provider_id', 'daily_help_provider_id');
    }

    public function service_vendor()
    {
        return $this->hasOne(ServiceVendor::class,'service_vendor_id', 'service_vendor_id');
    }

    public function visitor_image()
    {
        return $this->hasOne(VisitorGatepassFile::class,'society_visitor_id', 'society_visitor_id');
    }
}
