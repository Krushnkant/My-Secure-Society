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
}
