<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnouncementFile extends Model
{
    use HasFactory;
    protected $table = 'announcement_file';
    protected $primaryKey = 'announcement_file_id';
    public $timestamps = false;
}
