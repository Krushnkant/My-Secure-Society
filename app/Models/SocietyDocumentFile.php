<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocietyDocumentFile extends Model
{
    use HasFactory;
    protected $table = 'society_document_file';
    protected $primaryKey = 'society_document_file_id';
    public $timestamps = false;
}
