<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDesignation extends Model
{
    use HasFactory;
    protected $table = 'company_user_designation';
    protected $primaryKey = 'company_user_designation_id';
    public $timestamps = false;


    public function designation()
    {
        return $this->hasOne(Designation::class,'company_designation_id', 'company_designation_id');
    }

}
