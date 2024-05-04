<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyAlert extends Model
{
    use HasFactory;

    protected $table = 'emergency_alert';
    protected $primaryKey = 'emergency_alert_id';
    public $timestamps = false;
}
