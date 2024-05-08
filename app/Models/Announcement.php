<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory;
    use HasFactory,SoftDeletes;

    protected $table = 'announcement';
    protected $primaryKey = 'announcement_id';
    protected $dates = ['deleted_at'];

    public function announcement_file()
    {
        return $this->hasOne(AnnouncementFile::class,'announcement_id', 'announcement_id');
    }

    public function user()
    {
        return $this->hasOne(User::class,'user_id', 'created_by');
    }
}
