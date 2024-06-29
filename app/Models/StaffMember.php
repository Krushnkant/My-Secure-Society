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

    public function department()
    {
        return $this->hasOne(SocietyDepartment::class,'society_department_id', 'society_department_id');
    }

    public function residentdesignationauthority()
    {
        return $this->hasMany(ResidentDesignationAuthority::class,'resident_designation_id', 'resident_designation_id')->select('resident_designation_id','eauthority as auth','can_view as v','can_add as a','can_edit as e','can_delete as d','can_print as p');
    }

    public function duty_areas()
    {
        return $this->hasMany(StaffDutyAreaTime::class,'society_staff_member_id', 'society_staff_member_id');
    }
}
