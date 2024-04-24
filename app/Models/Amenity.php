<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Amenity extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'amenity';
    protected $primaryKey = 'amenity_id';
    protected $dates = ['deleted_at'];

    public function amenity_pdf()
    {
        return $this->hasOne(AmenityFile::class,'amenity_id', 'amenity_id')->where('file_type',4);
    }

    public function amenity_images()
    {
        return $this->hasMany(AmenityFile::class,'amenity_id', 'amenity_id')->where('file_type',1);
    }

    public function amenity_slots()
    {
        return $this->hasMany(AmenitySlot::class,'amenity_id', 'amenity_id');
    }
}
