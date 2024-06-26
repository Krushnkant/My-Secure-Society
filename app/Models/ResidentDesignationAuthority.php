<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResidentDesignationAuthority extends Model
{
    use HasFactory;
    protected $table = 'resident_designate_auth';
    protected $primaryKey = 'resident_designate_auth_id';
}
