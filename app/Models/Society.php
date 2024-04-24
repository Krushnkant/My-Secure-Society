<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Society extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'society';
    protected $primaryKey = 'society_id';

    protected $dates = ['deleted_at'];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'country_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'city_id');
    }

    public function society_blocks()
    {
        return $this->hasMany(Block::class, 'society_id');
    }
}
