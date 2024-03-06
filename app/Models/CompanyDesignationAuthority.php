<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyDesignationAuthority extends Model
{
    use HasFactory;
    protected $table = 'company_designation_authority';
    protected $primaryKey = 'designation_auth_id';
}
