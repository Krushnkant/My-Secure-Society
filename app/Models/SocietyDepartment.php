<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocietyDepartment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'society_department';
    protected $primaryKey = 'society_department_id';

    protected $dates = ['deleted_at'];
}
