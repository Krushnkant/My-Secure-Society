<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocietyMember extends Model
{
    use HasFactory;
    protected $table = 'society_member';
    protected $primaryKey = 'society_member_id';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

}
