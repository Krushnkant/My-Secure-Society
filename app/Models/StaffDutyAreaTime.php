<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffDutyAreaTime extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'staff_duty_area_time';
    protected $primaryKey = 'duty_area_time_id';
    protected $dates = ['deleted_at'];

    public function duty_area()
    {
        return $this->belongsTo(StaffDutyArea::class, 'staff_duty_area_id', 'staff_duty_area_id');
    }


}
