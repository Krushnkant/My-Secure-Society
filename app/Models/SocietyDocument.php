<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocietyDocument extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'society_document';
    protected $primaryKey = 'society_document_id';
    protected $dates = ['deleted_at'];

    public function sharedocumentflat()
    {
        return $this->hasMany('App\Models\DocumentSharedFlat', 'society_document_id');
    }

    public function document_file()
    {
        return $this->hasOne('App\Models\SocietyDocumentFile', 'society_document_id');
    }
}
