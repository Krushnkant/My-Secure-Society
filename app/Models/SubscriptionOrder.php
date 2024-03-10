<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionOrder extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'subscription_order';
    protected $primaryKey = 'subscription_order_id';
    protected $dates = ['deleted_at'];
}
