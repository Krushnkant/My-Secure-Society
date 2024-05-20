<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveredCourier extends Model
{
    use HasFactory;

    protected $table = 'delivered_courier_at_gate';
    protected $primaryKey = 'delivered_courier_at_gate_id';

    public function service_vendor()
    {
        return $this->hasOne(ServiceVendor::class,'service_vendor_id', 'service_vendor_id');
    }

    public function parcel_image()
    {
        return $this->hasOne(DeliveredCourierFile::class,'delivered_courier_at_gate_id', 'delivered_courier_at_gate_id');
    }

    public function parcel_images()
    {
        return $this->hasMany(DeliveredCourierFile::class,'delivered_courier_at_gate_id', 'delivered_courier_at_gate_id');
    }

}
