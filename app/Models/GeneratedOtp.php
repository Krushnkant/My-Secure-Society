<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneratedOtp extends Model
{
    use HasFactory;

    protected $casts = [
        'otp_code' => 'string',
    ];

    protected $table = 'generated_otp';
    protected $primaryKey = 'generated_otp_id';
    public $timestamps = false;


}
