<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitingHelpCategory extends Model
{
    use HasFactory;

    protected $table = 'visiting_help_category';
    protected $primaryKey = 'visiting_help_category_id';
}
