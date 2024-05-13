<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BloodDonate extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'blood_donate_request';
    protected $primaryKey = 'blood_donate_request_id';
    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->hasOne(User::class,'user_id', 'created_by');
    }
}
