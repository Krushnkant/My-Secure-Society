<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class VisitingHelpCategory extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'visiting_help_category';
    protected $primaryKey = 'visiting_help_category_id';
}
