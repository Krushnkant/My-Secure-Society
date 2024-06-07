<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffDutyArea extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'staff_duty_area';
    protected $primaryKey = 'staff_duty_area_id';
    protected $dates = ['deleted_at'];
}
