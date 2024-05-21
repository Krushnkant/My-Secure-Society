<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessProfile extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'business_profile';
    protected $primaryKey = 'business_profile_id';
    protected $dates = ['deleted_at'];

    public function pdf_file()
    {
        return $this->hasOne(BusinessProfileFile::class,'business_profile_id', 'business_profile_id')->where('file_type',4);
    }

    public function image_files()
    {
        return $this->hasMany(BusinessProfileFile::class,'business_profile_id', 'business_profile_id')->where('file_type',1);
    }

    public function user()
    {
        return $this->hasOne(User::class,'user_id', 'created_by');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'country_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'city_id');
    }

    public function business_category()
    {
        return $this->belongsTo(BusinessCategory::class, 'business_category_id', 'business_category_id');
    }
}
