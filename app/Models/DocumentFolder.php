<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentFolder extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'document_folder';
    protected $primaryKey = 'document_folder_id';
    protected $dates = ['deleted_at'];

    public function documents()
    {
        return $this->hasMany(SocietyDocument::class, 'document_folder_id');
    }
}
