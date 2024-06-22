<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocietyLedgerDetail extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'society_ledger_detail';
    protected $primaryKey = 'society_ledger_detail_id';
    protected $dates = ['deleted_at'];
}
