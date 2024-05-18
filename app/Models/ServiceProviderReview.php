<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceProviderReview extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'daily_help_provider_review';
    protected $primaryKey = 'daily_help_provider_review_id';

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->hasOne(User::class,'user_id', 'created_by');
    }
}
