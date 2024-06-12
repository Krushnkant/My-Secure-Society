<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocietyVisitorFile extends Model
{
    use HasFactory;
    protected $table = 'society_visitor_file';
    protected $primaryKey = 'society_visitor_file_id';
    public $timestamps = false;
}
