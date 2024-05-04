<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmenityBooking extends Model
{
    use HasFactory;
    protected $table = 'amenity_booking';
    protected $primaryKey = 'amenity_booking_id';
}
