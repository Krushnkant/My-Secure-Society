<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceCategory extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'service_category';
    protected $primaryKey = 'service_category_id';

    protected $dates = ['deleted_at'];


    public function department()
    {
        return $this->hasOne(SocietyDepartment::class, 'society_department_id','society_department_id');
    }

}
