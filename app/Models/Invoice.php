<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'invoice';
    protected $primaryKey = 'invoice_id';

    public function paymentTransaction()
    {
        return $this->hasOne(PaymentTransaction::class, 'invoice_id','invoice_id');
    }

}
