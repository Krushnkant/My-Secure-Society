<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostBannerConfig extends Model
{
    protected $table = 'post_banner_config'; // Set the table name
    protected $primaryKey = 'post_banner_config_id'; // Set the primary key field
    public $timestamps = false; // Disable default timestamp columns

}
