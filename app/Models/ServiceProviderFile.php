<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProviderFile extends Model
{
    use HasFactory;

    protected $table = 'daily_help_providerfile';
    protected $primaryKey = 'daily_help_provider_file_id';
    public $timestamps = false;

    public function getFileUrlAttribute(){
        if($this->attributes['file_url'] != null){
            return asset($this->attributes['file_url']);
        }else{
            return null;
        }
    }
}
