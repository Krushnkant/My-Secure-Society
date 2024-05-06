<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocietyMember extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'society_member';
    protected $primaryKey = 'society_member_id';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function flat()
    {
        return $this->belongsTo(Flat::class, 'block_flat_id', 'block_flat_id');
    }

    public function society()
    {
        return $this->belongsTo(Society::class, 'society_id', 'society_id');
    }

    public function residentdesignation()
    {
        return $this->belongsTo(ResidentDesignation::class, 'resident_designation_id', 'resident_designation_id');
    }

    public function residentdesignationauthority()
    {
        return $this->hasMany(ResidentDesignationAuthority::class,'resident_designation_id', 'resident_designation_id')->select('resident_designation_id','eauthority as auth','can_view as v','can_add as a','can_edit as e','can_delete as d','can_print as p');
    }



}
