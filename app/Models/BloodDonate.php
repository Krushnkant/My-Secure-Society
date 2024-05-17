<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BloodDonate extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'blood_donate_request';
    protected $primaryKey = 'blood_donate_request_id';
    protected $dates = ['deleted_at'];

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

    public function user()
    {
        return $this->hasOne(User::class,'user_id', 'created_by');
    }

    public function reply()
    {
        return $this->hasMany(BloodDonateRequestResponse::class,'blood_donate_request_id', 'blood_donate_request_id');
    }


}
