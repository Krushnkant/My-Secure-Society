<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceProviderWorkFlat extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'daily_help_provider_work_flat';
    protected $primaryKey = 'work_flat_id';

    protected $dates = ['deleted_at'];
}
