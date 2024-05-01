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
}
