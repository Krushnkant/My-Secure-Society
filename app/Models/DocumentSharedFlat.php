<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentSharedFlat extends Model
{
    protected $table = 'document_shared_flat'; // Set the table name
    protected $primaryKey = 'document_shared_flat_id'; // Set the primary key field
    public $timestamps = false; // Disable default timestamp columns
}
