<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocietyVisitor extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'society_visitor';
    protected $primaryKey = 'society_visitor_id';
    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->hasOne(User::class,'user_id', 'created_by');
    }

    public function daily_help_provider()
    {
        return $this->hasOne(ServiceProvider::class,'daily_help_provider_id', 'daily_help_provider_id');
    }

    public function service_vendor()
    {
        return $this->hasOne(ServiceVendor::class,'service_vendor_id', 'service_vendor_id');
    }

    public function visiting_help_categori()
    {
        return $this->hasOne(VisitingHelpCategory::class,'visiting_help_category_id', 'visiting_help_category_id');
    }
}
