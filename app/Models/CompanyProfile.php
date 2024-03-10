<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    use HasFactory;
    protected $table = 'company_profile';
    protected $primaryKey = 'company_profile_id';
    public $timestamps = false;

    protected $fillable = [
        'company_name',
        'logo_url',
        'gst_in_number',
        'street_address1',
        'street_address2',
        'landmark',
        'pin_code',
        'city_id',
        'state_id',
        'latitude',
        'longitude',
        'country_id',
        'updated_by',
    ];
}
