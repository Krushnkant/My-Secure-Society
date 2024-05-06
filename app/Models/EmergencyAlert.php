<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyAlert extends Model
{
    use HasFactory;

    protected $table = 'emergency_alert';
    protected $primaryKey = 'emergency_alert_id';
    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime', // This will cast the 'created_at' attribute to a DateTime object.
        // Add other attributes here if needed.
    ];

    public function society_member()
    {
        return $this->hasOne(SocietyMember::class, 'society_member_id', 'society_member_id');
    }
}
