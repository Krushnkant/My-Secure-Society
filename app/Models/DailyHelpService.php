<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyHelpService extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'daily_help_service';
    protected $primaryKey = 'daily_help_service_id';

    protected $dates = ['deleted_at'];

    public function getServiceIconAttribute(){
        if($this->attributes['service_icon'] != null){
            return asset($this->attributes['service_icon']);
        }else{
            return null;
        }
    }
}
