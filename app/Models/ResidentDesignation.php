<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResidentDesignation extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'resident_designation';
    protected $primaryKey = 'resident_designation_id';

    protected $dates = ['deleted_at'];

    public function claim()
    {
        return $this->hasMany(ResidentDesignationAuthority::class, 'resident_designation_id', 'resident_designation_id');
    }
}
