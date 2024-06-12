<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffMember extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'society_staff_member';
    protected $primaryKey = 'society_staff_member_id';
    protected $dates = ['deleted_at'];

    public function areatime()
    {
        return $this->hasOne(StaffDutyAreaTime::class,'society_staff_member_id', 'society_staff_member_id')->where('is_standing_location',1);
    }

    public function user()
    {
        return $this->hasOne(User::class,'user_id', 'user_id');
    }

    public function designation()
    {
        return $this->hasOne(ResidentDesignation::class,'resident_designation_id', 'resident_designation_id');
    }
}
