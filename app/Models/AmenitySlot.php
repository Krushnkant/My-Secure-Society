<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AmenitySlot extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'amenity_slot';
    protected $primaryKey = 'amenity_slot_id';
    protected $dates = ['deleted_at'];
}
