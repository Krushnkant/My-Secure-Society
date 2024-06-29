<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffDutyAttendance extends Model
{
    use HasFactory;
    protected $table = 'staff_duty_attendance';
    protected $primaryKey = 'staff_duty_attendance_id';

    public $timestamps = false;

    public function duty_area_time()
    {
        return $this->belongsTo(StaffDutyAreaTime::class, 'duty_area_time_id', 'duty_area_time_id');
    }

    public function staff()
    {
        return $this->belongsTo(StaffMember::class, 'society_staff_member_id', 'society_staff_member_id');
    }
}
