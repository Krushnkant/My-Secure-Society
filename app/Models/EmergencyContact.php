<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmergencyContact extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'emergency_contact';
    protected $primaryKey = 'emergency_contact_id';

    protected $dates = ['deleted_at'];
}
