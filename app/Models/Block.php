<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Block extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'society_block';
    protected $primaryKey = 'society_block_id';
    protected $dates = ['deleted_at'];

    public function society()
    {
        return $this->belongsTo(Society::class, 'society_id');
    }

    public function block_flats()
    {
        return $this->hasMany(Flat::class, 'society_block_id');
    }
}
