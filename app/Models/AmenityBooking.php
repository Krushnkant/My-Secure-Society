<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmenityBooking extends Model
{
    use HasFactory;
    protected $table = 'amenity_booking';
    protected $primaryKey = 'amenity_booking_id';

    public function amenity()
    {
        return $this->hasOne(Amenity::class,'amenity_id', 'amenity_id');
    }

    public function slot()
    {
        return $this->hasOne(AmenitySlot::class,'amenity_slot_id', 'amenity_slot_id');
    }
}
