<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceProvider extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'daily_help_provider';
    protected $primaryKey = 'daily_help_provider_id';

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->hasOne(User::class,'user_id', 'user_id');
    }

    public function daily_help_service()
    {
        return $this->hasOne(DailyHelpService::class,'daily_help_service_id', 'daily_help_service_id');
    }

    public function user_rating()
    {
        return $this->hasOne(UserRating::class,'user_id', 'user_id');
    }

    public function front_img()
    {
        return $this->hasOne(ServiceProviderFile::class,'daily_help_provider_id', 'daily_help_provider_id')->where('file_view',1);
    }

    public function back_img()
    {
        return $this->hasOne(ServiceProviderFile::class,'daily_help_provider_id', 'daily_help_provider_id')->where('file_view',2);
    }

    public function work_flat()
    {
        return $this->hasMany(ServiceProviderWorkFlat::class,'daily_help_provider_id', 'daily_help_provider_id');
    }
}
