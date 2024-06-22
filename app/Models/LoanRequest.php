<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'loan_request';
    protected $primaryKey = 'loan_request_id';
}
